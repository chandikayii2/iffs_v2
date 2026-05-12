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
        return Permission::all();
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
                'updated_by' => $userId
            ];
        }

        return RolePermission::insert($rolePermissions);
    }

    public function getAllUserRolePermissions()
    {
        return RolePermission::join('roles', 'role_permissions.role_id', '=', 'roles.id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->select('role_permissions.*', 'roles.role_name', 'permissions.name')
            ->get();
    }

    public function editRolePermission($role_id)
    {
        return RolePermission::join('roles', 'role_permissions.role_id', '=', 'roles.id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->select('role_permissions.*', 'roles.role_name', 'permissions.name')
            ->where('roles.id', $role_id)
            ->get();
    }


    public function updateRolePermission($attributes, $role_id)
    {
        $permissions = $attributes['permissions'];
        $userId = $attributes['updated_by'];

        // Step 1: Retrieve existing role permissions related to the provided role ID
        $existingRolePermissions = RolePermission::where('role_id', $role_id)->get();

        // Step 2: Delete existing role permissions
        foreach ($existingRolePermissions as $existingRolePermission) {
            $existingRolePermission->delete();
        }

        // Step 3: Insert new role permissions
        $rolePermissions = [];
        foreach ($permissions as $permissionId) {
            $rolePermissions[] = [
                'role_id' => $role_id,
                'permission_id' => $permissionId,
                'created_by' => $userId,
                'updated_by' => $userId
            ];
        }

        RolePermission::insert($rolePermissions);
    }
}
