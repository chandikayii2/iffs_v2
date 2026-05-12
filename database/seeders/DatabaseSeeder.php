<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Seed roles
        $this->call(RolesTableSeeder::class);

        // Seed users
        $this->call(UsersTableSeeder::class);

        // Seed suppliers
        $this->call(SuppliersTableSeeder::class);

        // Seed products
        $this->call(ProductsTableSeeder::class);

        // Seed permissions
        $this->call(PermissionsTableSeeder::class);
    }
}
