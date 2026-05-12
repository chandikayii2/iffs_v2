<?php

namespace App\Services;

use App\Repository\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\ProductServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService implements ProductServiceInterface
{

    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {

        $this->productRepository = $productRepository;
    }

    public function create($attributes)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;


            $response = $this->productRepository->create($attributes);

            DB::commit();
            return ['status' => 200, 'message' => 'Product has been successfully created', 'data' => $response];
        } catch (Exception $e) {
            //  DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getAll()
    {
        try {

            $response = $this->productRepository->getAll();

            return ['status' => 200, 'message' => 'Product retrieved successfully', 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function edit($productId)
    {
        try {
            $response = $this->productRepository->edit($productId);

            if ($response) {
                return ['status' => 200, 'message' => 'Product retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'Product not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function update($attributes, $productId)
    {
        DB::beginTransaction();
        try {

            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $product = $this->productRepository->edit($productId);

            if (!$product) {
                return ['status' => 400, 'message' => 'product not found', 'data' => null];
            }

            $response = $this->productRepository->update($attributes, $productId);

            DB::commit();
            return ['status' => 200, 'message' => 'Product has been successfully Updated', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function delete($product_id)
    {
        try {
            $delete_products = $this->productRepository->deleteProduct($product_id);

            return ['status' => 200, 'message' => 'Product deleted successfully', 'data' => $delete_products];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
    
      public function genaratePdf()
    {
        try {

            //$currentDate = Carbon::now()->format('Y-m-d');

            $stock_pdf = $this->productRepository->genaratePdfStockDetails();

            //  $stock_pdf['current_date'] = $currentDate;

            return ['status' => 200, 'message' => 'stock pdf get successfully', 'data' => $stock_pdf];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
}
