<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'ABC Supplies',
                'email' => 'abc_supplies@example.com',
                'contact' => '1234567890',
                'address' => '123 Main Street, City',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XYZ Distributors',
                'email' => 'xyz_distributors@example.com',
                'contact' => '9876543210',
                'address' => '456 Oak Avenue, Town',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quality Suppliers',
                'email' => 'quality_suppliers@example.com',
                'contact' => '5558889999',
                'address' => '789 Pine Street, Village',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Traders',
                'email' => 'global_traders@example.com',
                'contact' => '3334445555',
                'address' => '101 Elm Road, Township',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smart Importers',
                'email' => 'smart_importers@example.com',
                'contact' => '1112223333',
                'address' => '222 Cedar Lane, Borough',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
