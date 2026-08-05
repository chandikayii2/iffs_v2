<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing role_permissions to prevent duplicates
        DB::table('role_permissions')->truncate();

        // Get all permissions
        $permissions = DB::table('permissions')->get();

        // Get all roles
        $roles = DB::table('roles')->get();

        $rolePermissions = [];
        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                $rolePermissions[] = [
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        DB::table('role_permissions')->insert($rolePermissions);
    }
}
