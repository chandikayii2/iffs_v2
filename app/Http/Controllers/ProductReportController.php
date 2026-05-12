<?php

namespace App\Http\Controllers;
use PDF; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\IssueNote;
use App\Models\IssueNoteProduct;
use App\Models\Grn;
use App\Models\GrnProduct;
//use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductReportController extends Controller
{
    public function showForm()
    {
        $products = Product::all(); // Get all products or filter as needed
        return view('reports.product.form', compact('products'));
    }
   public function generateReport(Request $request)
{
    
//dd($request->all());
    // Validate the request
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'date_range' => 'required|in:this_week,last_week,custom',
        'from_date' => [
            'nullable',
            'date',
            Rule::requiredIf(function () use ($request) {
                return $request->date_range === 'custom';
            })
        ],
        'to_date' => [
            'nullable', 
            'date',
            Rule::requiredIf(function () use ($request) {
                return $request->date_range === 'custom';
            }),
            function ($attribute, $value, $fail) use ($request) {
                if ($request->date_range === 'custom' && $value && $request->from_date && $value < $request->from_date) {
                    $fail('The to date must be after or equal to from date.');
                }
            }
        ]
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $product = Product::findOrFail($request->product_id);
    
    // Determine date range
    switch ($request->date_range) {
        case 'this_week':
            $fromDate = now()->startOfWeek();
            $toDate = now()->endOfWeek();
            break;
        case 'last_week':
            $fromDate = now()->subWeek()->startOfWeek();
            $toDate = now()->subWeek()->endOfWeek();
            break;
        case 'custom':
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            break;
    }

    // Get report data
    $reportData = $this->getReportData($product, $fromDate, $toDate);

    if ($request->has('download')) {
        return $this->downloadPdfReport($product, $reportData, [
            'from_date' => $fromDate,
            'to_date' => $toDate
        ]);
    }

    return view('reports.product.show', [
        'product' => $product,
        'reportData' => $reportData,
        'fromDate' => $fromDate->format('d.m.Y'),
        'toDate' => $toDate->format('d.m.Y'),
    ]);
}

    protected function getDateRange(Request $request): array
    {
        switch ($request->date_range) {
            case 'this_week':
                return [
                    'from_date' => Carbon::now()->startOfWeek(),
                    'to_date' => Carbon::now()->endOfWeek()
                ];
            case 'last_week':
                return [
                    'from_date' => Carbon::now()->subWeek()->startOfWeek(),
                    'to_date' => Carbon::now()->subWeek()->endOfWeek()
                ];
            case 'custom':
                return [
                    'from_date' => Carbon::parse($request->from_date),
                    'to_date' => Carbon::parse($request->to_date)
                ];
        }
    }

   protected function getReportData(Product $product, Carbon $fromDate, Carbon $toDate): array
{
    // Get opening balance
    $openingBalance = $this->calculateOpeningBalance($product, $fromDate);
    
    // Format dates in d-m-Y format to match your database
    $dbFromDate = $fromDate->format('d-m-Y');
    $dbToDate = $toDate->format('d-m-Y');
    
    // Get all GRN transactions using JOIN
    $grnProducts = GrnProduct::with(['grn', 'grnSerialNumbers'])
        ->join('grns', 'grn_products.grn_id', '=', 'grns.id')
        ->where('grn_products.product_id', $product->id)
        ->whereRaw("STR_TO_DATE(grns.grn_date, '%d-%m-%Y') >= STR_TO_DATE(?, '%d-%m-%Y')", [$dbFromDate])
        ->whereRaw("STR_TO_DATE(grns.grn_date, '%d-%m-%Y') <= STR_TO_DATE(?, '%d-%m-%Y')", [$dbToDate])
        ->orderByRaw("STR_TO_DATE(grns.grn_date, '%d-%m-%Y') ASC")
        ->select('grn_products.*') // Select only the main table columns
        ->get();
    
    // Get all issue transactions using JOIN
    $issueProducts = IssueNoteProduct::with(['issueNote', 'issueSerialNumbers'])
        ->join('issue_notes', 'issue_note_products.issue_note_id', '=', 'issue_notes.id')
        ->where('issue_note_products.product_id', $product->id)
        ->whereRaw("STR_TO_DATE(issue_notes.issue_note_date, '%d-%m-%Y') >= STR_TO_DATE(?, '%d-%m-%Y')", [$dbFromDate])
        ->whereRaw("STR_TO_DATE(issue_notes.issue_note_date, '%d-%m-%Y') <= STR_TO_DATE(?, '%d-%m-%Y')", [$dbToDate])
        ->orderByRaw("STR_TO_DATE(issue_notes.issue_note_date, '%d-%m-%Y') ASC")
        ->select('issue_note_products.*')
        ->get();

    return $this->prepareReportDataWithDailyBalance($openingBalance, $grnProducts, $issueProducts, $product);
}

protected function prepareReportDataWithDailyBalance(
    float $openingBalance,
    $grnProducts,
    $issueProducts,
    Product $product
): array {
    \Log::info('=== START prepareReportDataWithDailyBalance ===');
    \Log::info('Opening Balance: ' . $openingBalance);
    \Log::info('GRN Products Count: ' . $grnProducts->count());
    \Log::info('Issue Products Count: ' . $issueProducts->count());

    // Get the product and its unit of measurement
    $unit = " ".$product->unit_of_measurement ?? '';
    \Log::info('Unit: ' . $unit);

    // Group transactions by date
    $dailyTransactions = [];
    
    // Process GRNs (stock in) - allow multiple per date
    \Log::info('Processing GRN Products...');
    foreach ($grnProducts as $grnProduct) {
        if ($grnProduct && $grnProduct->grn) {
            $date = $grnProduct->grn->grn_date ?? '';
            \Log::info('GRN Date: ' . $date);
            if (!isset($dailyTransactions[$date])) {
                $dailyTransactions[$date] = [
                    'stock_ins' => [],
                    'stock_outs' => []
                ];
            }
            $dailyTransactions[$date]['stock_ins'][] = [
                'date' => $date,
                'amount' => $grnProduct->received_quantity ?? 0,
                'reference' => $grnProduct->grn->grn_number ?? '',
                'company_info' => optional($grnProduct->grn->createdBy)->name ?? 'System',
                'remarks' => $grnProduct->grn->remarks ?? null,
                'created_at' => $grnProduct->created_at ?? null,
            ];
            \Log::info('Added GRN to date: ' . $date . ', Amount: ' . $grnProduct->received_quantity);
        }
    }
    
    // Process issues (stock out) - allow multiple per date
    \Log::info('Processing Issue Products...');
    foreach ($issueProducts as $issueProduct) {
        if ($issueProduct && $issueProduct->issueNote) {
            $date = $issueProduct->issueNote->issue_note_date ?? '';
            \Log::info('Issue Date: ' . $date);
            if (!isset($dailyTransactions[$date])) {
                $dailyTransactions[$date] = [
                    'stock_ins' => [],
                    'stock_outs' => []
                ];
            }
            $dailyTransactions[$date]['stock_outs'][] = [
                'date' => $date,
                'amount' => $issueProduct->issued_quantity ?? 0,
                'vehicle' => $issueProduct->issueNote->lorry_number ?? '',
                'km' => $this->extractKmFromRemarks($issueProduct->issueNote->remarks ?? ''),
                'remarks' => $issueProduct->issueNote->remarks ?? null,
                'company_info' => optional($issueProduct->issueNote->createdBy)->name ?? 'Driver: ' . ($issueProduct->issueNote->driver_name ?? ''),
                'created_at' => $issueProduct->created_at ?? null,
            ];
            \Log::info('Added Issue to date: ' . $date . ', Amount: ' . $issueProduct->issued_quantity);
        }
    }

    \Log::info('Daily Transactions Dates: ' . implode(', ', array_keys($dailyTransactions)));
    
    // Sort dates chronologically
    uksort($dailyTransactions, function($a, $b) {
        try {
            // Convert dates to Carbon instances for comparison
            $dateA = Carbon::parse($a);
            $dateB = Carbon::parse($b);
            return $dateA <=> $dateB;
        } catch (\Exception $e) {
            return 0;
        }
    });

    \Log::info('Sorted Dates: ' . implode(', ', array_keys($dailyTransactions)));
    
    // Prepare final report data
    $reportData = [];
    $currentBalance = $openingBalance;
    \Log::info('Starting Balance: ' . $currentBalance);

    foreach ($dailyTransactions as $date => $transactions) {
        \Log::info('Processing date: ' . $date);
        $stockIns = $transactions['stock_ins'] ?? [];
        $stockOuts = $transactions['stock_outs'] ?? [];
        
        \Log::info('Stock Ins count: ' . count($stockIns));
        \Log::info('Stock Outs count: ' . count($stockOuts));

        // Get the maximum count between stock-ins and stock-outs
        $maxCount = max(count($stockIns), count($stockOuts));
        
        // If no transactions, skip
        if ($maxCount === 0) {
            \Log::info('No transactions for date: ' . $date . ', skipping');
            continue;
        }
        
        // Create rows for each transaction pair
        for ($i = 0; $i < $maxCount; $i++) {
            $stockIn = $stockIns[$i] ?? null;
            $stockOut = $stockOuts[$i] ?? null;
            
            // Calculate balance after stock-in (if exists)
            $balanceAfterIn = $currentBalance;
            if ($stockIn) {
                $balanceAfterIn = $currentBalance + $stockIn['amount'];
                \Log::info('Stock In: ' . $stockIn['amount'] . ', Balance after in: ' . $balanceAfterIn);
            }
             $createdAt = null;
            if ($stockOut && isset($stockOut['created_at'])) {
                $createdAt = Carbon::parse($stockOut['created_at'])->format('d.m.Y H:i:s');
            } elseif ($stockIn && isset($stockIn['created_at'])) {
                $createdAt = Carbon::parse($stockIn['created_at'])->format('d.m.Y H:i:s');
            }
            $row = [
                'unit' => $unit,
                'opening_balance' => $currentBalance,
                'stock_in_amount_num' => $stockIn ? $stockIn['amount'] : 0,
                'stock_in_date' => $stockIn ? $stockIn['date'] : '-',
                'stock_in_amount' => $stockIn ? $stockIn['amount'] . $unit : '-',
                'company_info' => $stockIn ? $stockIn['company_info'] : ($stockOut ? $stockOut['company_info'] : '-'),
                'balance' => $balanceAfterIn . $unit,
                'issue_date' => $stockOut ? $stockOut['date'] : '-',
                'created_at' => $createdAt ?? '-',
                'vehicle' => $stockOut ? $stockOut['vehicle'] : '-',
                'issue_amount_num' => $stockOut ? $stockOut['amount'] : 0,
                'issue_amount' => $stockOut ? $stockOut['amount'] . $unit : '-',
                'km' => $stockOut ? ($stockOut['km'] ?? '-') : '-',
                'remarks' => $stockIn ? ($stockIn['remarks'] ?? '-') : ($stockOut ? ($stockOut['remarks'] ?? '-') : '-'),
            ];
            
            // Update current balance for next row
            if ($stockOut) {
                $currentBalance = $balanceAfterIn - $stockOut['amount'];
                \Log::info('Stock Out: ' . $stockOut['amount'] . ', New Balance: ' . $currentBalance);
            } elseif ($stockIn) {
                $currentBalance = $balanceAfterIn;
                \Log::info('No Stock Out, Balance remains: ' . $currentBalance);
            }
            
            $reportData[] = $row;
            \Log::info('Added row to report data');
        }
    }
    
    \Log::info('Final Report Data Rows: ' . count($reportData));
    \Log::info('=== END prepareReportDataWithDailyBalance ===');
    
    return $reportData;
}
protected function calculateOpeningBalance(Product $product, Carbon $fromDate): float
{
    $openingDate = $fromDate->copy()->subDay()->format('d-m-Y');
    
    \Log::info('Calculating opening balance for date: ' . $openingDate);
    
    $totalReceived = GrnProduct::where('product_id', $product->id)
        ->whereHas('grn', function($query) use ($openingDate) {
            $query->whereRaw("STR_TO_DATE(grn_date, '%d-%m-%Y') <= STR_TO_DATE(?, '%d-%m-%Y')", [$openingDate]);
        })
        ->sum('received_quantity');

    $totalIssued = IssueNoteProduct::where('product_id', $product->id)
        ->whereHas('issueNote', function($query) use ($openingDate) {
            $query->whereRaw("STR_TO_DATE(issue_note_date, '%d-%m-%Y') <= STR_TO_DATE(?, '%d-%m-%Y')", [$openingDate]);
        })
        ->sum('issued_quantity');

    \Log::info('Opening Balance Calculation - Received: ' . $totalReceived . ', Issued: ' . $totalIssued . ', Balance: ' . ($totalReceived - $totalIssued));

    return $totalReceived - $totalIssued;
}


    protected function downloadPdfReport(Product $product, array $reportData, array $dateRange)
{
    $pdf = PDF::loadView('reports.product.pdf', [
        'product' => $product,
        'reportData' => $reportData,
        'fromDate' => $dateRange['from_date']->format('d.m.Y'),
        'toDate' => $dateRange['to_date']->format('d.m.Y'),
    ])->setPaper('a4', 'landscape');
    
    $fileName = "{$product->product_code}_Report_" 
             . $dateRange['from_date']->format('Y-m-d') 
             . '_to_' 
             . $dateRange['to_date']->format('Y-m-d') 
             . '.pdf';
    
    return $pdf->download($fileName);
}

   protected function extractKmFromRemarks($remarks)
{
    if (preg_match('/(\d+[a-zA-Z]\d+)/', $remarks, $matches)) {
        return $matches[1];
    }
    return null;
}
}