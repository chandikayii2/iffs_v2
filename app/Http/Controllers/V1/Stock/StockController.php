<?php

namespace App\Http\Controllers\V1\Stock;

use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\ProductServiceInterface;

class StockController extends Controller
{

    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function getAll()
    {
        $response = $this->productService->getAll();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $stocks = $response['data'];
        } else {
            $suppliers = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('stock.stockList', compact('stocks'));
    }


    public function genaratePdf()
    {
        $response = $this->productService->genaratePdf();

        if ($response['status'] === 200) {
            $pdfData = $response['data'];
            // dd($pdfData);

            $date = Carbon::now()->format('Y-m-d');

            // Pass both pdfData and date to the Blade file
            $pdf = PDF::loadView('stock.pdf', compact('pdfData', 'date'));

            // Return the PDF as a download
            return $pdf->download('Stock' . $date . '.pdf');
        } else {
            // Handle error response
            return response()->json(['status' => $response['status'], 'message' => $response['message']], $response['status']);
        }
    }
}
