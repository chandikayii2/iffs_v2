<?php

namespace App\Http\Controllers\V1\Grn;

use PDF;
use App\Models\Grn;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\GrnServiceInterface;

class GrnController extends Controller
{
    protected $grnService;

    public function __construct(GrnServiceInterface $grnService)
    {
        $this->grnService = $grnService;
    }

    public function createView()
    {
        // Get the last GRN number from the database
        $lastGrnNo = Grn::max('grn_number');
        // Extract the numeric part and increment it
        $lastGrnNumericPart = intval(substr($lastGrnNo, 4)); // Updated to start from index 4 to remove 'GRN-'
        $newGrnNumericPart = $lastGrnNumericPart + 1;
        // Pad the numeric part to 5 digits with leading zeros
        $newGrnNumericPartPadded = str_pad($newGrnNumericPart, 6, '0', STR_PAD_LEFT);
        // Concatenate with 'GRN-' prefix
        $newGrnNo = 'GRN-' . $newGrnNumericPartPadded;

        // Call the service to get PO numbers
        $response = $this->grnService->createView();

        if ($response['status'] === 200) {
            // Assuming $response['data'] contains the array of PO numbers
            $poNumbers = $response['data'];

            return view('grn.create', compact('poNumbers', 'newGrnNo'));
        } else {
            return view('error')->with('message', $response['message']);
        }
    }


    public function getPurchaseOrderProducts($purchaseOrderId)
    {
        $response = $this->grnService->getPurchaseOrderProducts($purchaseOrderId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

   public function createGrn(Request $attributes)
{
    $validator = Validator::make($attributes->all(), [
        'grn_number' => 'required|string',
        'purchase_order_id' => 'required|integer|exists:purchase_orders,id',
        'grn_date' => 'required|string',
        'reference' => 'nullable|string',
        'grn_products' => 'required|array|min:1',
        'grn_products.*.pop_id' => 'required|integer|exists:purchase_order_products,id',
        'grn_products.*.product_id' => 'required|integer|exists:products,id',
        'grn_products.*.received_quantity' => 'required|numeric|min:0.01',
        'grn_products.*.received_price' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 400, 'message' => $validator->errors()->first(), 'data' => null], 400);
    }

    // Additional validation: Check if products with unit "L" can have decimal quantities
    $grnProducts = $attributes->input('grn_products', []);
    
    foreach ($grnProducts as $index => $product) {
        $productModel = Product::find($product['product_id']);
        
        if (!$productModel) {
            return response()->json([
                'status' => 400,
                'message' => "Product not found for product_id: {$product['product_id']}",
                'data' => null
            ], 400);
        }

        // If unit is NOT "L" (Liters), check if quantity is integer
        if (strtoupper($productModel->unit_of_measurement) !== 'L') {
            $quantity = $product['received_quantity'];
            if (!is_int($quantity) && $quantity != (int)$quantity) {
                return response()->json([
                    'status' => 400,
                    'message' => "Product '{$productModel->name}' must have whole number quantity (unit: {$productModel->unit_of_measurement})",
                    'data' => null
                ], 400);
            }
        }
    }

    $response = $this->grnService->createGrn($attributes);

    return response()->json(['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
}

    public function getAll()
    {
        $response = $this->grnService->getAll();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $grns = $response['data'];
            // dd($grns);
        } else {
            $grns = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('grn.grnList', compact('grns'));
    }

    public function grnProductsView($grn_id)
    {

        $response = $this->grnService->grnProductsView($grn_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function deleteGrn($grn_id)
    {
        $response = $this->grnService->deleteGrn($grn_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }


    public function genaratePdf($grn_id)
    {
        $response = $this->grnService->genaratePdf($grn_id);

        if ($response['status'] === 200) {
            $pdfData = $response['data'];
            // dd($pdfData);
            $date = $pdfData->current_date;

            $pdf = PDF::loadView('grn.pdf', compact('pdfData'));

            // Return the PDF as a download
            return $pdf->download('Grn_' . $date . '.pdf');
        } else {
            // Handle error response
            return response()->json(['status' => $response['status'], 'message' => $response['message']], $response['status']);
        }
    }
}
