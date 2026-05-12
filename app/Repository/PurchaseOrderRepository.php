<?php

namespace App\Repository;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Repository\Interfaces\PurchaseOrderRepositoryInterface;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function savePurchaseOrder($attributes)
    {
        return PurchaseOrder::create($attributes->all());
    }

    public function savePurchaseOrderProduct($purchaseOrderId, $products)
    {
        foreach ($products as $product) {
            $orderDetails[] = PurchaseOrderProduct::create([
                'purchase_order_id' => $purchaseOrderId,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
            ]);
        }

        return $orderDetails;
    }

    public function getAll()
    {
        return PurchaseOrder::join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select('purchase_orders.*', 'suppliers.name as supplier_name')
              ->orderBy('purchase_orders.id', 'desc') 
            ->get();
    }

    public function purchaseOrderProductView($purchase_order_id)
    {
        return PurchaseOrderProduct::join('products', 'purchase_order_products.product_id', '=', 'products.id')
            ->select('purchase_order_products.*', 'products.product_name', 'products.product_code', 'products.unit_of_measurement', 'products.serial_number')
            ->where('purchase_order_id', $purchase_order_id)
            //  ->where('purchase_order_products.quantity', '!=', 0)
            ->get();
    }

    public function purchaseOrderDetails($purchase_order_id)
    {
        return PurchaseOrder::join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select('purchase_orders.*', 'suppliers.id as supplier_id', 'suppliers.name as supplier_name')
            ->where('purchase_orders.id', $purchase_order_id)
            ->get();
    }

    public function deletePoProduct($po_product_id)
    {
        return PurchaseOrderProduct::find($po_product_id)->delete();
    }

    public function updatePurchaseOrder($attributes, $purchaseOrderId)
    {
        PurchaseOrder::where('id', $purchaseOrderId)
            ->update([
                'supplier_id' => $attributes['supplier_id'],
                'purchase_order_date' => $attributes['purchase_order_date'],
                'reference' => $attributes['reference'],
                'updated_by' => $attributes['updated_by'],
            ]);

        $updatedOrder = PurchaseOrder::find($purchaseOrderId);

        return $updatedOrder;
    }



    public function updateOrCreatePurchaseOrderProduct($purchaseOrderId, $products)
    {
        foreach ($products as $product) {
            // Find the purchase order product by purchase order id and product id
            $purchaseOrderProduct = PurchaseOrderProduct::where('purchase_order_id', $purchaseOrderId)
                ->where('id', $product['po_product_id'])
                ->first();

            if ($purchaseOrderProduct) {
                // Update the quantity and unit price
                $purchaseOrderProduct->update([
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price']
                ]);
            } else {
                // Create a new purchase order product
                $purchaseOrderProduct = PurchaseOrderProduct::create([
                    'purchase_order_id' => $purchaseOrderId,
                    'product_id' => $product['po_product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price']
                ]);
            }
        }

        // Return the collection of updated or created purchase order products
        return $purchaseOrderProduct; // Assuming you want to return the last purchase order product
    }

    public function getPoNum()
    {
        return PurchaseOrder::join('purchase_order_products', 'purchase_orders.id', '=', 'purchase_order_products.purchase_order_id')
            ->where('purchase_order_products.grn_status', '!=', 1)
            ->orderBy('purchase_orders.id', 'desc')
            ->pluck('purchase_orders.purchase_order_number', 'purchase_orders.id');
    }


    public function deletePurchaseOrderProducts($purchase_order_id)
    {
        return PurchaseOrderProduct::where('purchase_order_id', $purchase_order_id)->delete();
    }

    public function deletePurchaseOrderById($purchase_order_id)
    {
        return PurchaseOrder::where('id', $purchase_order_id)->delete();
    }


    public function genaratePdfPurchaseOrderDetails($purchase_order_id)
    {
        return PurchaseOrder::join('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->select('purchase_orders.*', 'suppliers.name as supplier_name', 'suppliers.contact as supplier_contact', 'suppliers.address as supplier_address')
            ->where('purchase_orders.id', $purchase_order_id)
            ->first();
    }

    public function genaratePdfPurchaseOrderProductDetails($purchase_order_id)
    {
        return PurchaseOrderProduct::join('products', 'products.id', '=', 'purchase_order_products.product_id')
            ->where('purchase_order_id', $purchase_order_id)
            ->select(
                'purchase_order_products.*',
                'products.product_name',
                'products.product_code',
                'products.unit_of_measurement',
                'products.serial_number'
            )
            ->get();
    }
}
