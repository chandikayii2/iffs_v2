<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert default roles
        DB::table('roles')->insert([
            ['role_name' => 'Super Admin', 'slug' => 'super_admin', 'is_active' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['role_name' => 'Admin', 'slug' => 'admin', 'is_active' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['role_name' => 'Manager', 'slug' => 'manager', 'is_active' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['role_name' => 'Supervisor', 'slug' => 'supervisor', 'is_active' => 1, 'created_by' => 1, 'updated_by' => 1],
            // Add more roles as needed
        ]);
    }
}
