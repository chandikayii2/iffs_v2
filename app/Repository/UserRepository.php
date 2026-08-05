<?php

namespace App\Repository;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use App\Repository\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function create($attributes)
    {
        return User::create($attributes->all());
    }

    public function getAll()
    {
        return User::join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.role_name')
            ->get();
    }

    public function getAllUserRole()
    {
        return Role::all();
    }

    public function edit($userId)
    {
        return User::find($userId);
    }

    public function update($attributes, $EditUserId)
    {
        return tap(User::find($EditUserId))->update($attributes);
    }

    public function passwordUpdate($attributes, $userPasId)
    {
        return tap(User::find($userPasId))->update($attributes);
    }

    public function userRoleCreate($attributes)
    {
        return Role::create($attributes->all());
    }

    public function getAllUserPermissions()
    {
        $query = Permission::query();
        if (session('active_system') === 'tyre') {
            $query->where(function($q) {
                $q->where('slug', 'like', 'tyre_%')
                  ->orWhereIn('slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
            });
        } else {
            $query->where('slug', 'not like', 'tyre_%')
                  ->whereNotIn('slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
        }
        return $query->get();
    }

    public function rolePermissionCreate($attributes)
    {
        $permissions = $attributes['permissions'];
        $role_id = $attributes['select_role'];
        $userId = $attributes['created_by'];

        $rolePermissions = [];

        foreach ($permissions as $permissionId) {
            $rolePermissions[] = [
                'role_id' => $role_id,
                'permission_id' => $permissionId,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return RolePermission::insert($rolePermissions);
    }

    public function getAllUserRolePermissions()
    {
        $query = RolePermission::join('roles', 'role_permissions.role_id', '=', 'roles.id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->select('role_permissions.*', 'roles.role_name', 'permissions.name', 'permissions.slug');

        if (session('active_system') === 'tyre') {
            $query->where(function($q) {
                $q->where('permissions.slug', 'like', 'tyre_%')
                  ->orWhereIn('permissions.slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
            });
        } else {
            $query->where('permissions.slug', 'not like', 'tyre_%')
                  ->whereNotIn('permissions.slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
        }

        return $query->get();
    }

    public function editRolePermission($role_id)
    {
        $role = Role::find($role_id);
        if (!$role) {
            return null;
        }

        $query = RolePermission::join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role_id', $role_id)
            ->select('role_permissions.permission_id', 'permissions.slug');

        if (session('active_system') === 'tyre') {
            $query->where(function($q) {
                $q->where('permissions.slug', 'like', 'tyre_%')
                  ->orWhereIn('permissions.slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
            });
        } else {
            $query->where('permissions.slug', 'not like', 'tyre_%')
                  ->whereNotIn('permissions.slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
        }

        $assignedPermissions = $query->get();

        return [
            'role_id' => $role->id,
            'role_name' => $role->role_name,
            'permissions' => $assignedPermissions
        ];
    }


    public function updateRolePermission($attributes, $role_id)
    {
        $permissions = $attributes['permissions'] ?? [];
        $userId = $attributes['updated_by'];

        // Step 1: Retrieve existing role permissions of the active system related to the provided role ID
        $query = RolePermission::where('role_permissions.role_id', $role_id)
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->select('role_permissions.*', 'permissions.slug');

        if (session('active_system') === 'tyre') {
            $query->where(function($q) {
                $q->where('permissions.slug', 'like', 'tyre_%')
                  ->orWhereIn('permissions.slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
            });
        } else {
            $query->where('permissions.slug', 'not like', 'tyre_%')
                  ->whereNotIn('permissions.slug', ['add_tyre', 'issue_tyre', 'issue_tyre_list']);
        }

        $existingRolePermissions = $query->get();

        // Step 2: Delete these specific existing role permissions
        foreach ($existingRolePermissions as $existingRolePermission) {
            RolePermission::where('id', $existingRolePermission->id)->delete();
        }

        // Step 3: Insert new role permissions
        $rolePermissions = [];
        foreach ($permissions as $permissionId) {
            $rolePermissions[] = [
                'role_id' => $role_id,
                'permission_id' => $permissionId,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (!empty($rolePermissions)) {
            RolePermission::insert($rolePermissions);
        }
    }
}
