<?php

namespace App\Repository;

use App\Models\Supplier;
use App\Repository\Interfaces\SupplierRepositoryInterface;

class SupplierRepository implements SupplierRepositoryInterface
{

    public function create($attributes)
    {
        return Supplier::create($attributes->all());
    }


    public function getAll()
    {
        return Supplier::orderBy('id', 'desc')->get(); 
    }

    public function edit($supplierId)
    {
        return Supplier::find($supplierId);
    }

    public function update($attributes, $supplierId)
    {
        return tap(Supplier::find($supplierId))->update($attributes);
    }
}
