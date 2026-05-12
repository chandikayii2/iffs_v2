<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repository\Interfaces\SupplierRepositoryInterface;
use App\Services\Interfaces\SupplierServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierService implements SupplierServiceInterface
{

    protected $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {

        $this->supplierRepository = $supplierRepository;
    }

    public function create($attributes)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;


            $response = $this->supplierRepository->create($attributes);

            DB::commit();
            return ['status' => 200, 'message' => 'Supplier has been successfully created', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getAll()
    {
        try {

            $response = $this->supplierRepository->getAll();

            return ['status' => 200, 'message' => 'Supplier retrieved successfully', 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function edit($supplierId)
    {
        try {
            $response = $this->supplierRepository->edit($supplierId);

            if ($response) {
                return ['status' => 200, 'message' => 'Supplier retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'Supplier not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function update($attributes, $supplierId)
    {
        DB::beginTransaction();
        try {

            $user = $this->supplierRepository->edit($supplierId);

            if (!$user) {
                return ['status' => 400, 'message' => 'Supplier not found', 'data' => null];
            }

            $response = $this->supplierRepository->update($attributes, $supplierId);

            DB::commit();
            return ['status' => 200, 'message' => 'Supplier has been successfully Updated', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
}
