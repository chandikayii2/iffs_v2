<?php
// app/Http/Controllers/Tyre/TyreScrapController.php
namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\Tyre;
use App\Models\TyreScrapRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PDF;

class TyreScrapController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'store');
        
        $scrapTyres = Tyre::with(['scrapRecord'])
            ->whereHas('scrapRecord', function($q) use ($category) {
                $q->where('scrap_category', $category);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
            
        $scrapTyres->appends(['category' => $category]);
        
        $stats = [
            'total_scrap' => TyreScrapRecord::count(),
            'store_count' => TyreScrapRecord::where('scrap_category', 'store')->count(),
            'kurunagala_count' => TyreScrapRecord::where('scrap_category', 'kurunagala')->count(),
            'sold_count' => TyreScrapRecord::where('scrap_category', 'sold')->count(),
            'scrap_this_month' => TyreScrapRecord::whereMonth('scrap_date', now()->month)->count(),
            'avg_life_km' => TyreScrapRecord::avg('final_mileage')
        ];
        
        return view('tyre.scrap.index', compact('scrapTyres', 'stats'));
    }

    public function exportPdf(Request $request)
    {
        $category = $request->get('category', 'store');
        
        $scrapTyres = Tyre::with(['scrapRecord'])
            ->whereHas('scrapRecord', function($q) use ($category) {
                $q->where('scrap_category', $category);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $pdf = PDF::loadView('tyre.scrap.pdf', compact('scrapTyres', 'category'));
        return $pdf->download('scrapped_tyres_' . $category . '_' . date('d_m_Y') . '.pdf');
    }

    public function scrapTyre($tyreId)
    {
        $tyre = Tyre::with(['currentAllocation.vehicle'])->findOrFail($tyreId);
        
        return view('tyre.scrap.create', compact('tyre'));
    }

    public function processScrap(Request $request, $tyreId)
    {
        $rules = [
            'scrap_reason' => 'required|string',
            'final_mileage' => 'nullable|integer|min:0',
            'disposal_method' => 'nullable|string',
            'notes' => 'nullable|string'
        ];

        if ($request->scrap_reason === 'Damage') {
            $rules['custom_scrap_damage_reason'] = 'required|string';
        } elseif ($request->scrap_reason === 'Other') {
            $rules['custom_scrap_reason'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $tyre = Tyre::findOrFail($tyreId);
            
            if ($request->scrap_reason === 'Damage') {
                $reason = 'Damage: ' . $request->custom_scrap_damage_reason;
            } elseif ($request->scrap_reason === 'Other') {
                $reason = $request->custom_scrap_reason;
            } else {
                $reason = $request->scrap_reason;
            }
            
            // If tyre is currently allocated, remove it first
            if ($tyre->currentAllocation) {
                $allocation = $tyre->currentAllocation;
                $allocation->mileage_at_removal = $request->final_mileage ?? $allocation->vehicle->current_mileage;
                $allocation->removal_date = now()->toDateString();
                $allocation->removal_reason = 'Scrapped - ' . $reason;
                $allocation->save();
            }
            
            // Create scrap record
            TyreScrapRecord::create([
                'tire_id' => $tyreId,
                'scrap_date' => now()->toDateString(),
                'scrap_reason' => $reason,
                'final_mileage' => $request->final_mileage,
                'disposal_method' => $request->disposal_method,
                'notes' => $request->notes,
                'scrap_category' => 'store'
            ]);
            
            // Update tyre status
            $tyre->status = 'scrap';
            $tyre->current_location = 'Store';
            $tyre->save();

            DB::commit();

            return redirect()->route('tyre.scrap.index')
                ->with('success', 'Tyre marked as scrap successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to scrap tyre: ' . $e->getMessage());
        }
    }

    public function scrapReport()
    {
        $scrapRecords = TyreScrapRecord::with(['tyre'])
            ->orderBy('scrap_date', 'desc')
            ->paginate(20);
        
        $summary = [
            'by_reason' => TyreScrapRecord::selectRaw('scrap_reason, count(*) as count')
                ->groupBy('scrap_reason')
                ->get(),
            'by_month' => TyreScrapRecord::selectRaw('DATE_FORMAT(scrap_date, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get()
        ];
        
        return view('tyre.scrap.report', compact('scrapRecords', 'summary'));
    }

    public function sendToKurunagala($scrapId)
    {
        try {
            DB::beginTransaction();

            $scrapRecord = TyreScrapRecord::findOrFail($scrapId);
            $scrapRecord->scrap_category = 'kurunagala';
            $scrapRecord->save();

            $tyre = Tyre::findOrFail($scrapRecord->tire_id);
            $tyre->current_location = 'Kurunagala';
            $tyre->save();

            DB::commit();

            return redirect()->route('tyre.scrap.index', ['category' => 'kurunagala'])
                ->with('success', 'Tyre ' . $tyre->serial_number . ' successfully sent to Kurunagala!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process request: ' . $e->getMessage());
        }
    }

    public function sellScrap(Request $request, $scrapId)
    {
        $validator = Validator::make($request->all(), [
            'sale_price' => 'required|numeric|min:0',
            'sold_date' => 'required|date',
            'customer_details' => 'required|string',
            'sale_payment_method' => 'required|in:cash,online,check',
            'sale_reference' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $scrapRecord = TyreScrapRecord::findOrFail($scrapId);
            $scrapRecord->scrap_category = 'sold';
            $scrapRecord->sale_price = $request->sale_price;
            $scrapRecord->sale_date = $request->sold_date;
            $scrapRecord->buyer_name = $request->customer_details;
            $scrapRecord->sale_payment_method = $request->sale_payment_method;
            $scrapRecord->sale_reference = $request->sale_reference;
            $scrapRecord->sale_payment_status = 'paid';
            $scrapRecord->save();

            $tyre = Tyre::findOrFail($scrapRecord->tire_id);
            $tyre->current_location = 'Sold';
            $tyre->save();

            DB::commit();

            return redirect()->route('tyre.scrap.index', ['category' => 'sold'])
                ->with('success', 'Tyre ' . $tyre->serial_number . ' marked as sold successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record sale: ' . $e->getMessage());
        }
    }
}