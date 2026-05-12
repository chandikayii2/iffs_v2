<?php

namespace App\Http\Controllers\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\ProductServiceInterface;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function create(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'product_code' => 'required|string|unique:products,product_code',
            'product_name' => 'required|string',
            // 'description' => 'nullable|string',
            //'unit_price' => 'required|numeric',
            //'stock_quantity' => 'required|integer',
            'unit_of_measurement' => 'required|string',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
            'serial_number' => 'required|in:1,0', // Assuming serial number is either 1 or 0
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $response = $this->productService->create($attributes);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }



    public function getAll()
    {
        $response = $this->productService->getAll();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $products = $response['data'];
        } else {
            $suppliers = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('products.productList', compact('products'));
    }


    public function edit($productId)
    {
        $response = $this->productService->edit($productId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }


    public function update(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'productId' => 'required|integer',
            'product_code' => 'string',
            'product_name' => 'string',
            'unit_of_measurement' => 'string',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors(['message' => $validator->errors()->first()]);
        }

        $productId = $attributes['productId'];

        $response = $this->productService->update($attributes->all(), $productId);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }

    public function delete($productId)
    {
        $response = $this->productService->delete($productId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }
}
