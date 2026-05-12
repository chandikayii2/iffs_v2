<?php

namespace App\Repository;

use App\Models\Product;
use App\Models\IssuedGrn;
use App\Models\IssueNote;
use App\Models\GrnProduct;
use App\Models\GrnSerialNumber;
use App\Models\IssueNoteProduct;
use Illuminate\Support\Facades\DB;
use App\Models\IssueNoteSerialNumber;
use App\Repository\Interfaces\IssueNoteRepositoryInterface;
use Exception; // ADD THIS LINE - This is what's missing

class IssueNoteRepository implements IssueNoteRepositoryInterface
{
    public function getAllGrnProducts()
    {
        return GrnProduct::join('products', 'grn_products.product_id', '=', 'products.id')
            ->select(
                'grn_products.product_id',
                'products.id',
                'products.product_code',
                'products.product_name',
                DB::raw('SUM(grn_products.received_quantity) as received_quantity')
            )
            ->groupBy('grn_products.product_id', 'products.id', 'products.product_code', 'products.product_name')
            ->get();
    }


    // public function getProductData($productId)
    // {
    //     // Get product data with received quantity sum
    //     $productData = Product::with(['serialNumbers' => function ($query) {
    //         $query->select('id', 'product_id', 'grn_product_id', 'serial_number')
    //             ->where('is_active', 1);
    //     }])
    //         ->join('grn_products', 'products.id', '=', 'grn_products.product_id')
    //         ->select(
    //             'products.id',
    //             'grn_products.product_id',
    //             DB::raw('SUM(grn_products.received_quantity) as stock_quantity')
    //         )
    //         ->where('products.id', $productId)
    //         ->groupBy('products.id', 'grn_products.product_id')
    //         ->first();

    //     // If product data is found, add grn_id to it
    //     if ($productData) {
    //         $productData->grn_id = GrnProduct::where('product_id', $productId)
    //             ->where('issued_status', 1)
    //             ->pluck('grn_id')->toArray();
    //     }

    //     return $productData;
    // }
public function getProductData($productId)
{
    // Get product data with received quantity sum and issued quantity sum
    $productData = Product::with(['serialNumbers' => function ($query) {
        $query->select('id', 'product_id', 'grn_product_id', 'serial_number')
            ->where('is_active', 1);
    }])
        ->leftJoin('grn_products', 'products.id', '=', 'grn_products.product_id')
        ->select(
            'products.id',
            'grn_products.product_id',
            DB::raw('COALESCE(SUM(grn_products.received_quantity), 0) as received_quantity'),
            DB::raw('COALESCE(SUM(grn_products.out_quantity), 0) as issued_quantity')
        )
        ->where('products.id', $productId)
        ->groupBy('products.id', 'grn_products.product_id')
        ->first();

    // Calculate stock quantity
    if ($productData) {
        $received_quantity = (float) $productData->received_quantity;
        $issued_quantity = (float) $productData->issued_quantity;
        $productData->stock_quantity = $received_quantity - $issued_quantity;
        
        // FIX: Get GRN products that have available quantity
        $productData->grn_ids = GrnProduct::where('product_id', $productId)
            ->where(function($query) {
                $query->where('issued_status', 1)
                      ->orWhere(function($q) {
                          $q->where('issued_status', 0)
                            ->whereRaw('received_quantity > out_quantity');
                      });
            })
            ->whereRaw('received_quantity > out_quantity') // Has available stock
            ->pluck('grn_id')
            ->toArray();
    } else {
        // If no product data found, create empty structure
        $productData = (object)[
            'id' => $productId,
            'stock_quantity' => 0,
            'serial_numbers' => [],
            'grn_ids' => []
        ];
    }

    return $productData;
}
    public function getProductData_old($productId)
    {
        // Get product data with received quantity sum and issued quantity sum
        $productData = Product::with(['serialNumbers' => function ($query) {
            $query->select('id', 'product_id', 'grn_product_id', 'serial_number')
                ->where('is_active', 1);
        }])
            ->leftJoin('grn_products', 'products.id', '=', 'grn_products.product_id')
            ->select(
                'products.id',
                'grn_products.product_id',
                DB::raw('SUM(grn_products.received_quantity) as received_quantity'),
                DB::raw('SUM(grn_products.out_quantity) as issued_quantity')
            )
            ->where('products.id', $productId)
            ->groupBy('products.id', 'grn_products.product_id')
            ->first();

        // Calculate stock quantity by subtracting issued quantity from received quantity
        if ($productData) {
            $productData->stock_quantity = $productData->received_quantity - $productData->issued_quantity;

            // Fetch associated GRN IDs
            $productData->grn_id = GrnProduct::where('product_id', $productId)
                ->where('issued_status', 1)
                ->pluck('grn_id')
                ->toArray();
        }

        return $productData;
    }







    // public function getProductData($productId)
    // {
    //     return Product::with(['serialNumbers' => function ($query) {
    //         $query->select('id', 'product_id', 'serial_number')
    //         ->where('is_active', 1);
    //     }])
    //         ->select('id', 'stock_quantity')
    //         ->find($productId);
    // }



    public function createIssueNote($attributes)
    {
        return IssueNote::create($attributes->all());
    }

    public function createIssueProducts($issueNoteId, $issueNoteProducts, $userId)
    {
        $createdIssueNoteProducts = [];

        foreach ($issueNoteProducts as $product) {
            $issueProduct = IssueNoteProduct::create([
                'issue_note_id' => $issueNoteId,
                'product_id' => $product['productId'],
                'issued_quantity' => $product['issueQuantity'],
            ]);

            // Check if the product has serial numbers and is not empty
            if (isset($product['serial_numbers']) && is_array($product['serial_numbers']) && !empty($product['serial_numbers'])) {
                foreach ($product['serial_numbers'] as $serialNumber) {
                    // Check if $serialNumber is not empty
                    if (!empty($serialNumber)) {
                        IssueNoteSerialNumber::create([
                            'issue_note_id' => $issueNoteId,
                            'issue_note_product_id' => $issueProduct->id,
                            'product_id' => $product['productId'],
                            'serial_number_id' => $serialNumber['id'], // Use serial number's id
                            'serial_number' => $serialNumber['serial_number'], // Use serial number's serial_number
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]);
                    }
                }
            }

            $createdIssueNoteProducts[] = $issueProduct;
        }

        return $createdIssueNoteProducts;
    }


    public function reduceQuantityByProductId($issueNoteProducts, $userId)
    {
        foreach ($issueNoteProducts as $product) {
            $productId = $product['productId'];
            $quantity = $product['issueQuantity'];

            // Retrieve the Product
            $product = Product::findOrFail($productId);

            $product->update([
                'stock_quantity' => $product->stock_quantity - $quantity,
                'updated_by' => $userId
            ]);
        }

        return true; // Indicate success
    }

    public function changeSerialNumberStatus($issueNoteProducts, $userId)
    {
        foreach ($issueNoteProducts as $product) {
            if (isset($product['serial_numbers']) && is_array($product['serial_numbers']) && !empty($product['serial_numbers'])) {
                foreach ($product['serial_numbers'] as $serialNumber) {
                    // Retrieve the serial number
                    $grnSerialNumber = GrnSerialNumber::find($serialNumber['id']);

                    if ($grnSerialNumber) {
                        // Update the is_active field to 0
                        $grnSerialNumber->update([
                            'is_active' => 0,
                            'updated_by' => $userId
                        ]);
                    }
                }
            }
        }

        return true; // Indicate success
    }

    public function grnProductIssuedCheck($issueNoteProducts, $userId)
{
    $issuedGrnProducts = [];

    foreach ($issueNoteProducts as $product) {
        $productId = $product['productId'];
        $remainingQuantity = $product['issueQuantity'];

     // Fix the query to handle NULL values
$availableGrnProducts = GrnProduct::where('product_id', $productId)
    ->where(function($query) {
        $query->whereRaw('received_quantity > out_quantity')
              ->orWhereNull('out_quantity'); // Include records where out_quantity is NULL
    })
    ->orderBy('created_at', 'asc') // FIFO - oldest first
    ->get();

        if ($availableGrnProducts->isEmpty()) {
            throw new Exception("No available stock for product ID: {$productId}");
        }

        $totalAvailable = 0;
        foreach ($availableGrnProducts as $grnProduct) {
            $totalAvailable += ($grnProduct->received_quantity - $grnProduct->out_quantity);
        }

        if ($totalAvailable < $remainingQuantity) {
            throw new Exception("Insufficient stock for product ID: {$productId}. Available: {$totalAvailable}, Requested: {$remainingQuantity}");
        }

        foreach ($availableGrnProducts as $grnProduct) {
            if ($remainingQuantity <= 0) break;

            $availableQuantity = $grnProduct->received_quantity - $grnProduct->out_quantity;
            $quantityToIssue = min($availableQuantity, $remainingQuantity);

            // Update the GRN product
            $grnProduct->out_quantity += $quantityToIssue;
            
            // Mark as fully issued if no quantity left
            if ($grnProduct->out_quantity >= $grnProduct->received_quantity) {
                $grnProduct->issued_status = 0;
            }
            
            $grnProduct->save();

            $issuedGrnProducts[] = $grnProduct;
            $remainingQuantity -= $quantityToIssue;

            if ($remainingQuantity <= 0) break;
        }
    }

    return $issuedGrnProducts;
}



    public function getAll()
    {
        return IssueNote::orderBy('id', 'desc')->get();
    }

    // public function deleteIssueSerialNumbers($issue_note_id)
    // {
    //     // Retrieve all IssueNoteSerialNumbers associated with the given issue_note_id
    //     $issueNoteSerialNumbers = IssueNoteSerialNumber::where('issue_note_id', $issue_note_id)->get();

    //     foreach ($issueNoteSerialNumbers as $issueNoteSerialNumber) {
    //         // Find the corresponding grn_serial_number record
    //         $grnSerialNumber = GrnSerialNumber::find($issueNoteSerialNumber->serial_number_id);

    //         // Set the is_active column to 1 for corresponding grn_serial_number
    //         if ($grnSerialNumber) {
    //             $grnSerialNumber->is_active = 1;
    //             $grnSerialNumber->save();
    //         }

    //         // Delete the IssueNoteSerialNumber record
    //         $issueNoteSerialNumber->delete();
    //     }
    // }

   public function deleteIssueProducts($issue_note_id)
{
    DB::beginTransaction();

    try {
        // Get the issue note products
        $issueNoteProducts = IssueNoteProduct::where('issue_note_id', $issue_note_id)->get();

        foreach ($issueNoteProducts as $issueProduct) {
            // Restore product stock
            $product = Product::find($issueProduct->product_id);
            if ($product) {
                $product->increment('stock_quantity', $issueProduct->issued_quantity);
            }

            // Restore GRN product quantities (FIFO reversal)
            $remainingQuantity = $issueProduct->issued_quantity;
            $grnProducts = GrnProduct::where('product_id', $issueProduct->product_id)
                ->where('out_quantity', '>', 0)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($grnProducts as $grnProduct) {
                if ($remainingQuantity <= 0) break;

                $quantityToRestore = min($remainingQuantity, $grnProduct->out_quantity);
                $grnProduct->decrement('out_quantity', $quantityToRestore);
                
                if ($grnProduct->out_quantity == 0) {
                    $grnProduct->update(['issued_status' => 1]);
                }
                
                $remainingQuantity -= $quantityToRestore;
            }

            // Restore serial numbers
            $issueSerials = IssueNoteSerialNumber::where('issue_note_product_id', $issueProduct->id)->get();
            foreach ($issueSerials as $issueSerial) {
                GrnSerialNumber::where('id', $issueSerial->serial_number_id)
                    ->update(['is_active' => 1]);
                
                $issueSerial->delete();
            }
        }

        // Delete the issue products
        IssueNoteProduct::where('issue_note_id', $issue_note_id)->delete();

        DB::commit();
        return true;

    } catch (Exception $e) {
        DB::rollback();
        throw $e;
    }
}
    // public function deleteIssueNoteById($issue_note_id)
    // {
    //     return IssueNote::where('id', $issue_note_id)->delete();
    // }



    public function issueNoteProductsView($issue_note_id)
    {
        return IssueNoteProduct::with(['issueSerialNumbers' => function ($query) {
            $query->select('issue_note_product_id', 'serial_number');
        }])
            ->select('issue_note_products.*', 'products.product_name', 'products.product_code', 'products.unit_of_measurement', 'products.serial_number')
            ->leftJoin('products', 'issue_note_products.product_id', '=', 'products.id')
            ->where('issue_note_id', $issue_note_id)
            ->get();
    }

    public function genaratePdfIssueDetails($issue_note_id)
    {
        return IssueNote::where('id', $issue_note_id)->first();
    }


    public function genaratePdfIssueProductDetails($issue_note_id)
    {
        return IssueNoteProduct::join('products', 'products.id', '=', 'issue_note_products.product_id')
            ->where('issue_note_id', $issue_note_id)
            ->select(
                'issue_note_products.*',
                'products.product_name',
                'products.product_code',
                'products.unit_of_measurement',
                'products.serial_number'
            )
            ->get();
    }
}