<?php

namespace App\Repository;

use App\Models\Product;
use App\Repository\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{

    public function create($attributes)
    {
        return Product::create($attributes->all());
    }

    public function getAll()
    {
         return Product::orderBy('id', 'desc')->get(); 
    }

    public function edit($productId)
    {
        return Product::find($productId);
    }


    public function update($attributes, $productId)
    {
        return tap(Product::find($productId))->update($attributes);
    }

    public function deleteProduct($productId)
    {
        return Product::where('id', $productId)->delete();
    }
    
     public function genaratePdfStockDetails()
    {
        return Product::where('stock_quantity', '!=', 0) // Filter products where stock_quantity is not zero
            ->orderBy('id', 'desc') // Order by 'id' in descending order
            ->get();
    }
}
