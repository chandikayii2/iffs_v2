<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\IssueNote; // Add this line
use App\Models\IssueNoteProduct; // Add this line
use App\Models\IssueNoteSerialNumber; // Add this line
use App\Models\GrnSerialNumber; // Add this line
use App\Models\GrnProduct; // Add this line
use Illuminate\Support\Facades\Auth;
use App\Services\Interfaces\IssueNoteServiceInterface;
use App\Repository\Interfaces\IssueNoteRepositoryInterface;

class IssueNoteService implements IssueNoteServiceInterface
{

    protected $issueNoteRepository;

    public function __construct(IssueNoteRepositoryInterface $issueNoteRepository)
    {
        $this->issueNoteRepository = $issueNoteRepository;
    }

    public function createView()
    {
        try {
            $products = $this->issueNoteRepository->getAllGrnProducts();

            return ['status' => 200, 'message' => 'products retrieved successfully', 'data' => [
                'products' => $products,
            ]];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getProductData($productId)
    {
        try {
            $response = $this->issueNoteRepository->getProductData($productId);

            if ($response) {
                return ['status' => 200, 'message' => 'Product retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'Product not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

  public function createIssueNote($attributes)
{
    DB::beginTransaction();

    try {
        $userId = Auth::id();
        $attributes['created_by'] = $userId;
        $attributes['updated_by'] = $userId;

        // Validate if products with unit "L" can have decimal quantities
        if (isset($attributes['issue_products']) && is_array($attributes['issue_products'])) {
            foreach ($attributes['issue_products'] as $product) {
                $productModel = Product::find($product['productId']);
                
                if (!$productModel) {
                    DB::rollback();
                    return ['status' => 400, 'message' => "Product not found for product_id: {$product['productId']}", 'data' => null];
                }

                // If unit is NOT "L" (Liters), check if quantity is integer
                if (strtoupper($productModel->unit_of_measurement) !== 'L') {
                    $quantity = $product['issueQuantity'];
                    if (!is_int($quantity) && $quantity != (int)$quantity) {
                        DB::rollback();
                        return ['status' => 400, 'message' => "Product '{$productModel->name}' must have whole number quantity (unit: {$productModel->unit_of_measurement})", 'data' => null];
                    }
                }
            }
        }

        $issue_note = $this->issueNoteRepository->createIssueNote($attributes);

        // Check if 'products' is present and is an array
        if (isset($attributes['issue_products']) && is_array($attributes['issue_products'])) {
            $issueProducts = $this->issueNoteRepository->createIssueProducts($issue_note->id, $attributes['issue_products'], $userId);
        }

        $reduceQuantityProduct = $this->issueNoteRepository->reduceQuantityByProductId($attributes['issue_products'], $userId);

        $changeSerialNumberStatus = $this->issueNoteRepository->changeSerialNumberStatus($attributes['issue_products'], $userId);

        $grnProductIssuedCheck = $this->issueNoteRepository->grnProductIssuedCheck($attributes['issue_products'], $userId);

        $issue_note['issueProducts'] = $issueProducts;
        $issue_note['reduceQuantityProduct'] = $reduceQuantityProduct;

        DB::commit();

        return ['status' => 200, 'message' => 'Issue Note has been successfully created', 'data' => $issue_note];
    } catch (Exception $e) {
        DB::rollback();
        return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
    }
}
    public function createIssueNote_old($attributes)
    {

        DB::beginTransaction();

        try {
            $userId = Auth::id();
            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $issue_note = $this->issueNoteRepository->createIssueNote($attributes);

            // Check if 'products' is present and is an array
            if (isset($attributes['issue_products']) && is_array($attributes['issue_products'])) {
                $issueProducts = $this->issueNoteRepository->createIssueProducts($issue_note->id, $attributes['issue_products'], $userId);
            }

            $reduceQuantityProduct = $this->issueNoteRepository->reduceQuantityByProductId($attributes['issue_products'], $userId);

            $changeSerialNumberStatus = $this->issueNoteRepository->changeSerialNumberStatus($attributes['issue_products'], $userId);


            $grnProductIssuedCheck = $this->issueNoteRepository->grnProductIssuedCheck($attributes['issue_products'], $userId);



            $issue_note['issueProducts'] = $issueProducts;
            $issue_note['reduceQuantityProduct'] = $reduceQuantityProduct;


            DB::commit();

            return ['status' => 200, 'message' => 'Issue Note has been successfully created', 'data' => $issue_note];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getAll()
    {
        try {

            $response = $this->issueNoteRepository->getAll();

            return ['status' => 200, 'message' => 'Issue note retrieved successfully', 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

   public function deleteIssueNote($issue_note_id)
{
    DB::beginTransaction();

    try {
        // Get the issue note with products and their serial numbers
        $issueNote = IssueNote::with(['issueNoteProducts.issueSerialNumbers'])->find($issue_note_id);
        
        if (!$issueNote) {
            return ['status' => 404, 'message' => 'Issue note not found', 'data' => null];
        }

        // Restore stock quantities and serial numbers
        foreach ($issueNote->issueNoteProducts as $issueProduct) {
            $productId = $issueProduct->product_id;
            $issuedQuantity = $issueProduct->issued_quantity;

            // 1. Restore product stock quantity
            $product = Product::find($productId);
            if ($product) {
                $product->stock_quantity += $issuedQuantity;
                $product->save();
            }

            // 2. Restore GRN product quantities - CRITICAL FIX
            $remainingQuantity = $issuedQuantity;
            
            // Get GRN products that have out_quantity > 0 for this product, ordered by latest first (LIFO)
            $grnProducts = GrnProduct::where('product_id', $productId)
                ->where('out_quantity', '>', 0)
                ->orderBy('created_at', 'desc') // Latest first (LIFO)
                ->get();

            foreach ($grnProducts as $grnProduct) {
                if ($remainingQuantity <= 0) break;
                
                // Calculate how much to restore from this GRN product
                $quantityToRestore = min($remainingQuantity, $grnProduct->out_quantity);
                
                // Reduce the out_quantity
                $grnProduct->out_quantity -= $quantityToRestore;
                
                // If out_quantity becomes 0, set issued_status back to 1 (available)
                if ($grnProduct->out_quantity == 0) {
                    $grnProduct->issued_status = 1;
                }
                
                $grnProduct->save();
                $remainingQuantity -= $quantityToRestore;
            }

            // 3. Restore serial numbers status
            if ($issueProduct->issueSerialNumbers) {
                foreach ($issueProduct->issueSerialNumbers as $issueSerial) {
                    $grnSerial = GrnSerialNumber::find($issueSerial->serial_number_id);
                    if ($grnSerial) {
                        $grnSerial->is_active = 1; // Reactivate the serial number
                        $grnSerial->save();
                    }
                    // Delete the issue serial number record
                    $issueSerial->delete();
                }
            }
        }

        // 4. Delete all issue note products
        IssueNoteProduct::where('issue_note_id', $issue_note_id)->delete();

        // 5. Delete the issue note itself
        $issueNote->delete();

        DB::commit();

        return ['status' => 200, 'message' => 'Issue note deleted successfully and stock restored', 'data' => null];
        
    } catch (Exception $e) {
        DB::rollback();
        return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
    }
}

    public function issueNoteProductsView($issue_note_id)
    {
        try {
            $response = $this->issueNoteRepository->issueNoteProductsView($issue_note_id);

            if ($response) {
                return ['status' => 200, 'message' => 'issue products retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'issue products not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function genaratePdf($issue_note_id)
    {
        try {

            $currentDate = Carbon::now()->format('Y-m-d');

            $issue_note_pdf = $this->issueNoteRepository->genaratePdfIssueDetails($issue_note_id);

            $issue_note_products = $this->issueNoteRepository->genaratePdfIssueProductDetails($issue_note_id);

            $issue_note_pdf['current_date'] = $currentDate;

            $issue_note_pdf['issue_note_products'] = $issue_note_products;


            return ['status' => 200, 'message' => 'Issue note pdf get successfully', 'data' => $issue_note_pdf];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
}
