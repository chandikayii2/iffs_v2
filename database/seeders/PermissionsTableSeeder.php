<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define permissions data
        $permissions = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Access to Dashboard',
                'group' => 'Dashboard',
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Order',
                'slug' => 'purchase_order',
                'description' => 'Access to Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'name' => 'Add Purchase Order',
                'slug' => 'add_purchase_order',
                'description' => 'Permission to add Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Purchase Order',
                'slug' => 'edit_purchase_order',
                'description' => 'Permission to edit Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Purchase Order',
                'slug' => 'delete_purchase_order',
                'description' => 'Permission to delete Purchase Order',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Order List',
                'slug' => 'purchase_order_list',
                'description' => 'Permission to view Purchase Order List',
                'group' => 'Purchase',
                'is_active' => true,
            ],
            [
                'name' => 'GRN',
                'slug' => 'grn',
                'description' => 'Access to GRN',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'name' => 'Add GRN',
                'slug' => 'add_grn',
                'description' => 'Permission to add GRN',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'name' => 'GRN List',
                'slug' => 'grn_list',
                'description' => 'Permission to view GRN List',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'name' => 'Delete GRN',
                'slug' => 'delete_grn',
                'description' => 'Permission to delete GRN',
                'group' => 'GRN',
                'is_active' => true,
            ],
            [
                'name' => 'Issue Note',
                'slug' => 'issue_note',
                'description' => 'Access to Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'name' => 'Add Issue Note',
                'slug' => 'add_issue_note',
                'description' => 'Permission to add Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Issue Note',
                'slug' => 'edit_issue_note',
                'description' => 'Permission to edit Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Issue Note',
                'slug' => 'delete_issue_note',
                'description' => 'Permission to delete Issue Note',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'name' => 'Issue Note List',
                'slug' => 'issue_note_list',
                'description' => 'Permission to view Issue Note List',
                'group' => 'Issue Note',
                'is_active' => true,
            ],
            [
                'name' => 'Stock',
                'slug' => 'stock',
                'description' => 'Access to Stock',
                'group' => 'Stock',
                'is_active' => true,
            ],
            [
                'name' => 'Products',
                'slug' => 'products',
                'description' => 'Access to Products',
                'group' => 'Products',
                'is_active' => true,
            ],

            [
                'name' => 'Supplier',
                'slug' => 'supplier',
                'description' => 'Access to Supplier',
                'group' => 'Supplier',
                'is_active' => true,
            ],
          
            [
                'name' => 'Users',
                'slug' => 'users',
                'description' => 'Access to Users',
                'group' => 'Users',
                'is_active' => true,
            ],
        ];

        // Insert permissions into the database
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
