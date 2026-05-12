<?php

namespace App\Repository;

use App\Models\Grn;
use App\Models\Product;
use App\Models\GrnProduct;
use App\Models\GrnSerialNumber;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderProduct;
use App\Repository\Interfaces\GrnRepositoryInterface;

class GrnRepository implements GrnRepositoryInterface
{
    public function createGrn($attributes)
    {
        return Grn::create($attributes->all());
    }

    public function createGrnProducts($grnId, $grnProducts, $userId)
    {
        $createdGrnProducts = [];

        foreach ($grnProducts as $product) {
            $grnProduct = GrnProduct::create([
                'grn_id' => $grnId,
                'purchase_order_product_id' => $product['pop_id'],
                'product_id' => $product['product_id'],
                'received_quantity' => $product['received_quantity'],
                'received_price' => $product['received_price'],
            ]);

            // Check if the product has serial numbers and is not empty
            if (isset($product['serial_numbers']) && is_array($product['serial_numbers']) && !empty($product['serial_numbers'])) {
                foreach ($product['serial_numbers'] as $serialNumber) {
                    // Check if $serialNumber is not empty
                    if (!empty($serialNumber)) {
                        GrnSerialNumber::create([
                            'grn_id' => $grnId,
                            'grn_product_id' => $grnProduct->id,
                            'product_id' => $product['product_id'],
                            'serial_number' => $serialNumber,
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]);
                    }
                }
            }

            $createdGrnProducts[] = $grnProduct;
        }

        return $createdGrnProducts;
    }



    // public function reduceQuantityByPopId($grnProducts, $userId)
    // {
    //     foreach ($grnProducts as $product) {
    //         $popId = $product['pop_id'];
    //         $quantity = $product['received_quantity'];

    //         // Retrieve the Purchase Order Product
    //         $pop = PurchaseOrderProduct::findOrFail($popId);

    //         // Subtract the received quantity from the available quantity
    //         $pop->update([
    //             'quantity' => $pop->quantity - $quantity,
    //             'updated_by' => $userId
    //         ]);
    //     }

    //     return true; // Indicate success

    // }

    public function increaseQuantityByProductId($grnProducts, $userId)
    {
        foreach ($grnProducts as $product) {
            $productId = $product['product_id'];
            $quantity = $product['received_quantity'];

            // Retrieve the Product
            $product = Product::findOrFail($productId);

            // Add the received quantity to the product's stock quantity
            $product->update([
                'stock_quantity' => $product->stock_quantity + $quantity,
                'updated_by' => $userId
            ]);
        }

        return true; // Indicate success
    }


    public function updateGrnStatus($grnProducts, $userId)
    {
        foreach ($grnProducts as $product) {
            $popId = $product['pop_id'];

            // Retrieve the sum of received quantities for the Purchase Order Product
            $grnProductQuantitySum = GrnProduct::where('purchase_order_product_id', $popId)->sum('received_quantity');

            // Retrieve the Purchase Order Product
            $purchaseOrderProduct = PurchaseOrderProduct::find($popId);

            if ($purchaseOrderProduct) {
                // Compare the ordered quantity with the sum of received quantities
                if ($purchaseOrderProduct->quantity == $grnProductQuantitySum) {
                    // If they match, update the GRN status
                    $purchaseOrderProduct->grn_status = 1; // Assuming 1 means fully received
                } else {
                    // If they don't match, update the GRN status accordingly
                    $purchaseOrderProduct->grn_status = 0; // Assuming 0 means partially received
                }

                // Save the changes to the Purchase Order Product
                $purchaseOrderProduct->save();
            }
        }

        return true;
    }





    public function getAll()
    {
        return Grn::join('purchase_orders', 'purchase_orders.id', '=', 'grns.purchase_order_id')
            ->select('grns.*', 'purchase_orders.purchase_order_number as purchase_order_number')
             ->orderBy('grns.id', 'desc')
            ->get();
    }


    public function grnProductsView($grn_id)
    {
        return GrnProduct::with(['grnSerialNumbers' => function ($query) {
            $query->select('grn_product_id', 'serial_number');
        }])
            ->where('grn_id', $grn_id)
            ->join('purchase_order_products', 'grn_products.purchase_order_product_id', '=', 'purchase_order_products.id')
            ->join('products', 'purchase_order_products.product_id', '=', 'products.id')
            ->select('grn_products.*', 'products.product_code', 'products.product_name')
            ->get();
    }

    public function deleteGrnSerialNumbers($grn_id)
    {
        return GrnSerialNumber::where('grn_id', $grn_id)->delete();
    }

    public function deleteGrnProducts($grn_id)
    {
        $grnProducts = GrnProduct::where('grn_id', $grn_id)->get();

        foreach ($grnProducts as $grnProduct) {
            $purchaseOrderProduct = PurchaseOrderProduct::find($grnProduct->purchase_order_product_id);

            // $purchaseOrderProduct->quantity += $grnProduct->received_quantity;
            $purchaseOrderProduct->grn_status = 0;

            $purchaseOrderProduct->save();

            $product = Product::find($purchaseOrderProduct->product_id);
            $product->stock_quantity -= $grnProduct->received_quantity;
            $product->save();
        }

        return GrnProduct::where('grn_id', $grn_id)->delete();
    }


    public function deleteGrnById($grn_id)
    {
        return Grn::where('id', $grn_id)->delete();
    }

    // public function getPurchaseOrderProducts($purchaseOrderId)
    // {
    //     return PurchaseOrderProduct::join('products', 'purchase_order_products.product_id', '=', 'products.id')
    //         ->select('purchase_order_products.*', 'products.product_name', 'products.product_code', 'products.unit_of_measurement', 'products.serial_number')
    //         ->where('purchase_order_id', $purchaseOrderId)
    //         ->where('purchase_order_products.grn_status', '=', 0)
    //         ->get();
    // }

    public function getPurchaseOrderProducts($purchaseOrderId)
    {
        // Fetch all relevant data from the database
        $purchaseOrderProducts = PurchaseOrderProduct::join('products', 'purchase_order_products.product_id', '=', 'products.id')
            ->leftJoin('grn_products', 'purchase_order_products.id', '=', 'grn_products.purchase_order_product_id')
            ->select(
                'purchase_order_products.*',
                'products.product_name',
                'products.product_code',
                'products.unit_of_measurement',
                'products.serial_number',
                'grn_products.received_quantity',
                'grn_products.purchase_order_product_id'
            )
            ->where('purchase_order_id', $purchaseOrderId)
            ->where('purchase_order_products.grn_status', '=', 0)
            ->get();

        // Use Laravel Collection methods to manipulate the data
        $uniqueProducts = collect($purchaseOrderProducts)->groupBy('product_id')->map(function ($groupedProducts) {
            $totalReceivedQuantity = $groupedProducts->sum('received_quantity');
            $remainingQuantity = $groupedProducts->first()->quantity - $totalReceivedQuantity;
            return $groupedProducts->first()->setAttribute('total_received_quantity', $totalReceivedQuantity)
                ->setAttribute('remaining_quantity', $remainingQuantity);
        })->reject(function ($product) {
            return $product['remaining_quantity'] <= 0;
        })->values()->all();

        return $uniqueProducts;
    }

    public function genaratePdfGrnDetails($grn_id)
    {
        return Grn::join('purchase_orders', 'grns.purchase_order_id', '=', 'purchase_orders.id')
            ->select('grns.*', 'purchase_orders.purchase_order_number')
            ->where('grns.id', $grn_id)
            ->first();
    }


    public function genaratePdfGrnProductDetails($grn_id)
    {
        return GrnProduct::join('products', 'products.id', '=', 'grn_products.product_id')
            ->where('grn_id', $grn_id)
            ->select(
                'grn_products.*',
                'products.product_name',
                'products.product_code',
                'products.unit_of_measurement',
                'products.serial_number'
            )
            ->get();
    }
}
