<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key constraints to safely truncate
        Schema::disableForeignKeyConstraints();
        DB::table('role_permissions')->truncate();
        DB::table('permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        // Define permissions data
        $permissions = [
            // Core IFFS permissions (IDs 1 to 19 matching live site)
            [
                'id' => 1,
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Access to Dashboard',
                'group' => 'Dashboard',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Purchase Order',
                'slug' => 'purchase_order',
                'description' => 'Access to Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Add Purchase Order',
                'slug' => 'add_purchase_order',
                'description' => 'Permission to add Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Edit Purchase Order',
                'slug' => 'edit_purchase_order',
                'description' => 'Permission to edit Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Delete Purchase Order',
                'slug' => 'delete_purchase_order',
                'description' => 'Permission to delete Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'name' => 'Purchase Order List',
                'slug' => 'purchase_order_list',
                'description' => 'Permission to view Purchase Order List',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'id' => 7,
                'name' => 'GRN',
                'slug' => 'grn',
                'description' => 'Access to GRN',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'id' => 8,
                'name' => 'Add GRN',
                'slug' => 'add_grn',
                'description' => 'Permission to add GRN',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'id' => 9,
                'name' => 'GRN List',
                'slug' => 'grn_list',
                'description' => 'Permission to view GRN List',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'id' => 10,
                'name' => 'Delete GRN',
                'slug' => 'delete_grn',
                'description' => 'Permission to delete GRN',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'id' => 11,
                'name' => 'Issue Note',
                'slug' => 'issue_note',
                'description' => 'Access to Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'id' => 12,
                'name' => 'Add Issue Note',
                'slug' => 'add_issue_note',
                'description' => 'Permission to add Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'id' => 13,
                'name' => 'Edit Issue Note',
                'slug' => 'edit_issue_note',
                'description' => 'Permission to edit Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'id' => 14,
                'name' => 'Delete Issue Note',
                'slug' => 'delete_issue_note',
                'description' => 'Permission to delete Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'id' => 15,
                'name' => 'Issue Note List',
                'slug' => 'issue_note_list',
                'description' => 'Permission to view Issue Note List',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'id' => 16,
                'name' => 'Stock',
                'slug' => 'stock',
                'description' => 'Access to Stock',
                'group' => 'Stock',
                'is_active' => true,
            ],
            [
                'id' => 17,
                'name' => 'Products',
                'slug' => 'products',
                'description' => 'Access to Products',
                'group' => 'Products',
                'is_active' => true,
            ],
            [
                'id' => 18,
                'name' => 'Supplier',
                'slug' => 'supplier',
                'description' => 'Access to Supplier',
                'group' => 'Supplier',
                'is_active' => true,
            ],
            [
                'id' => 19,
                'name' => 'Users',
                'slug' => 'users',
                'description' => 'Access to Users',
                'group' => 'Users',
                'is_active' => true,
            ],

            // Tyre Lifecycle Management permissions (IDs 20 to 29)
            [
                'id' => 20,
                'name' => 'Tyre Dashboard',
                'slug' => 'tyre_dashboard',
                'description' => 'Access to tyre dashboard',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 21,
                'name' => 'Tyre Inventory List',
                'slug' => 'tyre_inventory',
                'description' => 'Access to tyre inventory list',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 22,
                'name' => 'Add Tyre',
                'slug' => 'add_tyre',
                'description' => 'Permission to add new tyres',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 23,
                'name' => 'Issue Tyre',
                'slug' => 'issue_tyre',
                'description' => 'Permission to issue tyres to vehicles',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 24,
                'name' => 'Issue Tyre List',
                'slug' => 'issue_tyre_list',
                'description' => 'Permission to view tyre issue history list',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 25,
                'name' => 'Vehicles',
                'slug' => 'tyre_vehicles',
                'description' => 'Access to vehicles list and details',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 26,
                'name' => 'Refilling Orders',
                'slug' => 'tyre_refilling',
                'description' => 'Access to tyre refilling orders',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 27,
                'name' => 'Manage Vendors',
                'slug' => 'tyre_vendors',
                'description' => 'Access to retreading/refilling vendors management',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 28,
                'name' => 'Scrap Management',
                'slug' => 'tyre_scrap',
                'description' => 'Access to scrapped tyres list and sales',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
            [
                'id' => 29,
                'name' => 'Users',
                'slug' => 'tyre_users',
                'description' => 'Access to Users menu and sub menu',
                'group' => 'Tyre Management',
                'is_active' => true,
            ],
        ];

        // Insert permissions in the database
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(
                array_merge($permission, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }
}
