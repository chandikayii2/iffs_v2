<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now(); // Capture the current timestamp

        // Insert Super admin
        DB::table('users')->insert([
            'role_id' => 1,
            'name' => 'Mr. Sharala Ranawana',
            'email' => 'sharalaranawana@gmail.com',
            'password' => Hash::make('Sharalaranawana@123'), // Capitalize first letter of email and name and concatenate with password
            'phone' => '0771234567', // Set phone number for super admin
            'is_active' => 1,
            'is_blocked' => 0,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Insert Admin
        DB::table('users')->insert([
            'role_id' => 2,
            'name' => 'Mr. Anjana Ruklan',
            'email' => 'anjanaiffstransport@gmail.com',
            'password' => Hash::make('Anjanaiffstransport@123'), // Capitalize first letter of email and name and concatenate with password
            'phone' => '0771234568', // Set phone number for admin
            'is_active' => 1,
            'is_blocked' => 0,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Insert Manager
        DB::table('users')->insert([
            'role_id' => 3,
            'name' => 'Mr. Priyantha Jayasingha',
            'email' => 'interfreighttransport@gmail.com',
            'password' => Hash::make('Interfreighttransport@123'), // Capitalize first letter of email and name and concatenate with password
            'phone' => '0771234569', // Set phone number for manager
            'is_active' => 1,
            'is_blocked' => 0,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Insert Supervisor
        DB::table('users')->insert([
            'role_id' => 4,
            'name' => 'Mr Lahiru Nisansala',
            'email' => 'lahiruiffstransport@gmail.com',
            'password' => Hash::make('Lahiruiffstransport@123'), // Capitalize first letter of email and name and concatenate with password
            'phone' => '0771234560', // Set phone number for supervisor
            'is_active' => 1,
            'is_blocked' => 0,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
