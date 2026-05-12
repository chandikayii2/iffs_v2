<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repository\Interfaces\ProductRepositoryInterface;
use App\Repository\Interfaces\SupplierRepositoryInterface;
use App\Services\Interfaces\PurchaseOrderServiceInterface;
use App\Repository\Interfaces\PurchaseOrderRepositoryInterface;

class PurchaseOrderService implements PurchaseOrderServiceInterface
{


    protected $purchaseOrderRepository;
    protected $supplierRepository;
    protected $productRepository;

    public function __construct(
        PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        SupplierRepositoryInterface $supplierRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->supplierRepository = $supplierRepository;
        $this->productRepository = $productRepository;
    }

    public function createView()
    {
        try {
            $suppliers = $this->supplierRepository->getAll();
            $products = $this->productRepository->getAll();

            return ['status' => 200, 'message' => 'Suppliers and products retrieved successfully', 'data' => [
                'suppliers' => $suppliers,
                'products' => $products
            ]];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getProductData($productId)
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


    public function savePurchaseOrder($attributes)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();
            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $purchaseOrder = $this->purchaseOrderRepository->savePurchaseOrder($attributes);

            // Check if 'products' is present and is an array
            if (isset($attributes['products']) && is_array($attributes['products'])) {

                $purchaseOrderProducts = $this->purchaseOrderRepository->savePurchaseOrderProduct($purchaseOrder->id, $attributes['products']);
            }

            DB::commit();

            return ['status' => 200, 'message' => 'Purchase Order has been successfully created', 'data' => $purchaseOrder];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getAll()
    {
        try {

            $response = $this->purchaseOrderRepository->getAll();

            return ['status' => 200, 'message' => 'Purchase Orders retrieved successfully', 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function purchaseOrderProductView($purchase_order_id)
    {
        try {
            $response = $this->purchaseOrderRepository->purchaseOrderProductView($purchase_order_id);

            if ($response) {
                return ['status' => 200, 'message' => 'po products retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'po products not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function edit($purchase_order_id)
    {
        try {
            // Fetch purchase order details
            $purchase_order = $this->purchaseOrderRepository->purchaseOrderDetails($purchase_order_id);

            // Fetch purchase order products
            $purchase_order_products = $this->purchaseOrderRepository->purchaseOrderProductView($purchase_order_id);

            $suppliers = $this->supplierRepository->getAll();

            $products = $this->productRepository->getAll();
            // Return the combined data
            return [
                'status' => 200,
                'message' => 'Purchase order retrieved successfully',
                'data' => [
                    'purchase_order' => $purchase_order,
                    'purchase_order_products' => $purchase_order_products,
                    'suppliers' => $suppliers,
                    'products' => $products
                ]
            ];
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function deletePoProduct($po_product_id)
    {
        try {
            $response = $this->purchaseOrderRepository->deletePoProduct($po_product_id);

            return ['status' => 200, 'message' => 'po products deleted successfully', 'data' => $response];
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function updatePurchaseOrder($attributes, $purchaseOrderId)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();
            $attributes['updated_by'] = $userId;

            $purchaseOrder = $this->purchaseOrderRepository->updatePurchaseOrder($attributes, $purchaseOrderId);

            // Check if 'products' is present and is an array
            if (isset($attributes['products']) && is_array($attributes['products'])) {

                $purchaseOrderProducts = $this->purchaseOrderRepository->updateOrCreatePurchaseOrderProduct($purchaseOrder->id, $attributes['products']);
            }

            DB::commit();

            return ['status' => 200, 'message' => 'Purchase Order has been successfully updated', 'data' => $purchaseOrder];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function deletePurchaseOrder($purchase_order_id)
    {
        try {
            $delete_po_products = $this->purchaseOrderRepository->deletePurchaseOrderProducts($purchase_order_id);

            $delete_purchase_order = $this->purchaseOrderRepository->deletePurchaseOrderById($purchase_order_id);

            return ['status' => 200, 'message' => 'Purchase order deleted successfully', 'data' => $delete_purchase_order];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function genaratePdf($purchase_order_id)
    {
        try {

            $currentDate = Carbon::now()->format('Y-m-d');

            $purchase_order_pdf = $this->purchaseOrderRepository->genaratePdfPurchaseOrderDetails($purchase_order_id);

            $purchase_order_products = $this->purchaseOrderRepository->genaratePdfPurchaseOrderProductDetails($purchase_order_id);

            $purchase_order_pdf['current_date'] = $currentDate;

            $purchase_order_pdf['purchase_order_products'] = $purchase_order_products;

            return ['status' => 200, 'message' => 'purchase order pdf get successfully', 'data' => $purchase_order_pdf];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
}
