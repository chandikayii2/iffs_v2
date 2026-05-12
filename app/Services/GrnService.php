<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Interfaces\GrnServiceInterface;
use App\Repository\Interfaces\GrnRepositoryInterface;
use App\Repository\Interfaces\PurchaseOrderRepositoryInterface;

class GrnService implements GrnServiceInterface
{

    protected $grnRepository;
    protected $purchaseOrderRepository;

    public function __construct(GrnRepositoryInterface $grnRepository, PurchaseOrderRepositoryInterface $purchaseOrderRepository)
    {
        $this->grnRepository = $grnRepository;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
    }

    public function createView()
    {
        try {
            $po_num = $this->purchaseOrderRepository->getPoNum();

            return ['status' => 200, 'message' => 'Purchase orders number retrieved successfully', 'data' => $po_num];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function getPurchaseOrderProducts($purchaseOrderId)
    {
        try {
            $response = $this->grnRepository->getPurchaseOrderProducts($purchaseOrderId);

            if ($response) {
                return ['status' => 200, 'message' => 'Product retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'Product not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function createGrn($attributes)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();
            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $grn = $this->grnRepository->createGrn($attributes);

            // Check if 'products' is present and is an array
            if (isset($attributes['grn_products']) && is_array($attributes['grn_products'])) {
                $grnProducts = $this->grnRepository->createGrnProducts($grn->id, $attributes['grn_products'], $userId);
            }


            // $reduceQuantityPop = $this->grnRepository->reduceQuantityByPopId($attributes['grn_products'], $userId);

            $increaseQuantityProducts = $this->grnRepository->increaseQuantityByProductId($attributes['grn_products'], $userId);

            $updateGrnStatus = $this->grnRepository->updateGrnStatus($attributes['grn_products'], $userId);


            $grn['grnProducts'] = $grnProducts;
            //$grn['reduceQuantityPop'] = $reduceQuantityPop;
            $grn['increaseQuantityProducts'] = $increaseQuantityProducts;

            DB::commit();

            return ['status' => 200, 'message' => 'Grn has been successfully created', 'data' => $grn];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function getAll()
    {
        try {

            $response = $this->grnRepository->getAll();

            return ['status' => 200, 'message' => 'grn retrieved successfully', 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function grnProductsView($grn_id)
    {
        try {
            $response = $this->grnRepository->grnProductsView($grn_id);

            if ($response) {
                return ['status' => 200, 'message' => 'grn products retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'grn products not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function deleteGrn($grn_id)
    {
        try {
            $delete_grn_serial_numbers = $this->grnRepository->deleteGrnSerialNumbers($grn_id);

            $delete_grn_products = $this->grnRepository->deleteGrnProducts($grn_id);

            $delete_grn = $this->grnRepository->deleteGrnById($grn_id);

            return ['status' => 200, 'message' => 'Grn deleted successfully', 'data' => $delete_grn];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function genaratePdf($grn_id)
    {
        try {

            $currentDate = Carbon::now()->format('Y-m-d');

            $grn_pdf = $this->grnRepository->genaratePdfGrnDetails($grn_id);

            $grn_products = $this->grnRepository->genaratePdfGrnProductDetails($grn_id);

            $grn_pdf['current_date'] = $currentDate;

            $grn_pdf['grn_products'] = $grn_products;


            return ['status' => 200, 'message' => 'Grn pdf get successfully', 'data' => $grn_pdf];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
}
