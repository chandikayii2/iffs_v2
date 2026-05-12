<?php

namespace App\Http\Controllers\V1\Purchase;

use PDF;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\PurchaseOrderServiceInterface;

class PurchaseOrderController extends Controller
{

    protected $purchaseOrderService;

    public function __construct(PurchaseOrderServiceInterface $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }



    public function createView()
    {
        $lastPoNo = PurchaseOrder::max('purchase_order_number');
        $lastPoNum = intval(substr($lastPoNo, 3));
        $newPoNum = str_pad($lastPoNum + 1, 6, '0', STR_PAD_LEFT);
        $newPoNo = 'PO' . '-' . $newPoNum;

        $response = $this->purchaseOrderService->createView();

        if ($response['status'] === 200) {
            return view('purchase.create', [
                'suppliers' => $response['data']['suppliers'],
                'products' => $response['data']['products'],
                'newPoNo' => $newPoNo,
            ]);
        } else {
            return view('error')->with('message', $response['message']);
        }
    }

    public function getProductData($productId)
    {

        $response = $this->purchaseOrderService->getProductData($productId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

   public function savePurchaseOrder(Request $attributes)
{
    $validator = Validator::make($attributes->all(), [
        'purchase_order_number' => 'required|string',
        'supplier_id' => 'required|integer',
        'purchase_order_date' => 'required|string',
        'reference' => 'nullable|string',
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:0.01',
        'products.*.unit_price' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 400, 'message' => $validator->errors()->first(), 'data' => null], 400);
    }

    // Additional validation: Check if products with unit "L" can have decimal quantities
    $products = $attributes->input('products', []);
    
    foreach ($products as $index => $product) {
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
            $quantity = $product['quantity'];
            if (!is_int($quantity) && $quantity != (int)$quantity) {
                return response()->json([
                    'status' => 400,
                    'message' => "Product '{$productModel->name}' must have whole number quantity (unit: {$productModel->unit_of_measurement})",
                    'data' => null
                ], 400);
            }
        }
    }

    $response = $this->purchaseOrderService->savePurchaseOrder($attributes);

    return response()->json(['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
}

    public function getAll()
    {
        $response = $this->purchaseOrderService->getAll();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $purchase_orders = $response['data'];
        } else {
            $purchase_orders = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('purchase.purchaseOrderList', compact('purchase_orders'));
    }

    public function purchaseOrderProductView($purchase_order_id)
    {

        $response = $this->purchaseOrderService->purchaseOrderProductView($purchase_order_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function edit($purchase_order_id)
    {
        $response = $this->purchaseOrderService->edit($purchase_order_id);

        if ($response['status'] == 200) {
            $data = $response['data'];
            $purchase_orders = $data['purchase_order'];
            $purchase_order_products = $data['purchase_order_products'];
            $products = $data['products'];
            $suppliers = $data['suppliers'];


            return view('purchase.purchaseOrderEdit', compact('purchase_orders', 'purchase_order_products', 'products', 'suppliers'));
        } else {
            return back()->with('error', $response['message']);
        }
    }

    public function deletePoProduct($po_product_id)
    {

        $response = $this->purchaseOrderService->deletePoProduct($po_product_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function updatePurchaseOrder(Request $attributes)
    {

        $validator = Validator::make($attributes->all(), [
            'purchase_order_id' => 'required|integer',
            'supplier_id' => 'integer',
            'purchase_order_date' => 'string',
            'reference' => 'nullable|string',
            'products' => 'array|min:1',
            'products.*.po_product_id' => 'integer|exists:purchase_order_products,id',
            'products.*.quantity' => 'integer',
            'products.*.unit_price' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first(), 'data' => null], 400);
        }

        $purchaseOrderId = $attributes['purchase_order_id'];

        $response = $this->purchaseOrderService->updatePurchaseOrder($attributes, $purchaseOrderId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function deletePurchaseOrder($purchase_order_id)
    {
        $response = $this->purchaseOrderService->deletePurchaseOrder($purchase_order_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }


    public function genaratePdf($purchase_order_id)
    {
        $response = $this->purchaseOrderService->genaratePdf($purchase_order_id);

        if ($response['status'] === 200) {
            $pdfData = $response['data'];
            // dd($pdfData);

            $date = $pdfData->current_date;

            $pdf = PDF::loadView('purchase.pdf', compact('pdfData'));

            // Return the PDF as a download
            return $pdf->download('Purchase-Order' . $date . '.pdf');
        } else {
            // Handle error response
            return response()->json(['status' => $response['status'], 'message' => $response['message']], $response['status']);
        }
    }
}
