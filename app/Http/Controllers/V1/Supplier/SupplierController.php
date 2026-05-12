<?php

namespace App\Http\Controllers\V1\Supplier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\SupplierServiceInterface;

class SupplierController extends Controller
{

    protected $supplierService;

    public function __construct(SupplierServiceInterface $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function create(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'contact' => 'required|string',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->with(['message' => $validator->errors()->first()]);
        }

        $response = $this->supplierService->create($attributes);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }


    public function getAll()
    {
        $response = $this->supplierService->getAll();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $suppliers = $response['data'];
        } else {
            $suppliers = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('supplier.supplierList', compact('suppliers'));
    }

    public function edit($supplierId)
    {

        $response = $this->supplierService->edit($supplierId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function update(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'supplierId' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email|unique:suppliers,email,' . $attributes->input('supplierId'),
            'contact' => 'string',
            'address' => 'string',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors(['message' => $validator->errors()->first()]);
        }

        $supplierId = $attributes['supplierId'];

        $response = $this->supplierService->update($attributes->all(), $supplierId);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }
}
