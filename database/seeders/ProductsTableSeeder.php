<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'product_code' => 'P000001',
                'product_name' => 'Widget A',
                // 'description' => 'Description for Widget A',
                // 'stock_quantity' => 50,
                // 'unit_price' => 12.99,
                'unit_of_measurement' => 'pieces',
                // 'image_file' => 'path/to/widgetA.jpg',
                'serial_number' => 1,
                'created_by' => 1, // Replace with actual user ID
                'updated_by' => 1, // Replace with actual user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_code' => 'P000002',
                'product_name' => 'Gadget X',
                // 'description' => 'Description for Gadget X',
                // 'stock_quantity' => 75,
                // 'unit_price' => 24.99,
                'unit_of_measurement' => 'units',
                // 'image_file' => 'path/to/gadgetX.jpg',
                'serial_number' => 0,
                'created_by' => 1, // Replace with actual user ID
                'updated_by' => 1, // Replace with actual user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_code' => 'P000003',
                'product_name' => 'Tool 123',
                // 'description' => 'Description for Tool 123',
                // 'stock_quantity' => 30,
                // 'unit_price' => 19.99,
                'unit_of_measurement' => 'sets',
                // 'image_file' => 'path/to/tool123.jpg',
                'serial_number' => 1,
                'created_by' => 1, // Replace with actual user ID
                'updated_by' => 1, // Replace with actual user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_code' => 'P000004',
                'product_name' => 'Equipment 456',
                // 'description' => 'Description for Equipment 456',
                // 'stock_quantity' => 100,
                // 'unit_price' => 39.99,
                'unit_of_measurement' => 'pieces',
                // 'image_file' => 'path/to/equipment456.jpg',
                'serial_number' => 0,
                'created_by' => 1, // Replace with actual user ID
                'updated_by' => 1, // Replace with actual user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_code' => 'P000005',
                'product_name' => 'Device DEF',
                // 'description' => 'Description for Device DEF',
                // 'stock_quantity' => 50,
                // 'unit_price' => 29.99,
                'unit_of_measurement' => 'units',
                // 'image_file' => 'path/to/deviceDEF.jpg',
                'serial_number' => 1,
                'created_by' => 1, // Replace with actual user ID
                'updated_by' => 1, // Replace with actual user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data into the products table
        DB::table('products')->insert($products);
    }
}
