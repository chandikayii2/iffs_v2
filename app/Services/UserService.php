<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\Interfaces\UserServiceInterface;
use App\Repository\Interfaces\UserRepositoryInterface;

class UserService implements UserServiceInterface
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create($attributes)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $response = $this->userRepository->create($attributes);

            DB::commit();
            return ['status' => 200, 'message' => 'User has been successfully created', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getAll()
    {
        try {
            $users = $this->userRepository->getAll();
            $userRoles = $this->userRepository->getAllUserRole();

            return ['status' => 200, 'message' => 'Users retrieved successfully', 'data' => [
                'users' => $users,
                'userRoles' => $userRoles
            ]];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function getAllUserRole()
    {
        try {

            $response = $this->userRepository->getAllUserRole();

            return ['status' => 200, 'message' => 'User role retrieved successfully', 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function edit($userId)
    {
        try {
            $response = $this->userRepository->edit($userId);

            if ($response) {
                return ['status' => 200, 'message' => 'User retrieved successfully', 'data' => $response];
            } else {
                return ['status' => 400, 'message' => 'User not found', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function update($attributes, $EditUserId)
    {
        DB::beginTransaction();
        try {

            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $user = $this->userRepository->edit($EditUserId);

            if (!$user) {
                return ['status' => 400, 'message' => 'user not found', 'data' => null];
            }

            $response = $this->userRepository->update($attributes, $EditUserId);

            DB::commit();
            return ['status' => 200, 'message' => 'User has been successfully Updated', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function passwordUpdate($attributes, $userPasId)
    {
        DB::beginTransaction();
        try {


            $userId = Auth::id();
            $attributes['updated_by'] = $userId;

            $response = $this->userRepository->passwordUpdate($attributes, $userPasId);

            DB::commit();
            return ['status' => 200, 'message' => 'User password been successfully Updated', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function userRoleCreate($attributes)
    {
        // Capitalize the first letter of the role name
        $attributes['role_name'] = ucfirst($attributes['role_name']);

        // Generate slug from role_name
        if (!isset($attributes['slug'])) {
            $attributes['slug'] = Str::slug($attributes['role_name']);
            // Replace hyphens with underscores in the slug
            $attributes['slug'] = str_replace('-', '_', $attributes['slug']);
        }

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $response = $this->userRepository->userRoleCreate($attributes);

            DB::commit();
            return ['status' => 200, 'message' => 'User Role has been successfully created', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function getAllUserPermissions()
    {
        try {

            $response = $this->userRepository->getAllUserPermissions();

            return ['status' => 200, 'message' => 'User permissions retrieved successfully', 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function getAllUserRolePermissions()
    {
        try {

            $user_roles = $this->userRepository->getAllUserRole();
            $user_permissions = $this->userRepository->getAllUserPermissions();
            $user_role_permissions = $this->userRepository->getAllUserRolePermissions();


            return ['status' => 200, 'message' => 'Users roles and permissions retrieved successfully', 'data' => [
                'user_roles' => $user_roles,
                'user_permissions' => $user_permissions,
                'user_role_permissions' => $user_role_permissions

            ]];
        } catch (Exception $e) {
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }


    public function rolePermissionCreate($attributes)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $response = $this->userRepository->rolePermissionCreate($attributes);

            DB::commit();
            return ['status' => 200, 'message' => 'User Role permissions Created successfully', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function editRolePermission($role_id)
    {
        try {
            $response = $this->userRepository->editRolePermission($role_id);

            return ['status' => 200, 'message' => 'User role permission retrieved successfully', 'data' => $response];
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }

    public function updateRolePermission($attributes, $role_id)
    {
        DB::beginTransaction();
        try {

            $userId = Auth::id();

            $attributes['created_by'] = $userId;
            $attributes['updated_by'] = $userId;

            $response = $this->userRepository->updateRolePermission($attributes, $role_id);

            DB::commit();
            return ['status' => 200, 'message' => 'Role permission has been successfully Updated', 'data' => $response];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
}
