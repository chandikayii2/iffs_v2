<?php

namespace App\Http\Controllers\V1\User;

use Illuminate\Http\Request;
use App\Models\RolePermission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\UserServiceInterface;

class UserRolePermisionController extends Controller
{

    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function getAllUserPermissions()
    {
        $response = $this->userService->getAllUserPermissions();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $user_permissions = $response['data'];
        } else {
            $user_permissions = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('users.userPermission', compact('user_permissions'));
    }

    public function getAllUserRolePermissions()
    {
        $response = $this->userService->getAllUserRolePermissions();

        if ($response['status'] === 200) {
            return view('users.userRolePermission', [
                'user_roles' => $response['data']['user_roles'],
                'user_permissions' => $response['data']['user_permissions'],
                'user_role_permissions' => $response['data']['user_role_permissions'],

            ]);
        } else {
            return view('error')->with('message', $response['message']);
        }
    }

    public function create(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'select_role' => 'required|integer',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required|integer|exists:permissions,id',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->with(['status' => 500, 'message' =>  $validator->errors()->first()]);
        }

        $response = $this->userService->rolePermissionCreate($attributes);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }


    public function checkRoleExists(Request $attributes)
    {
        $role_id = $attributes->input('select_role');

        // Check if the role permission already exists
        $existingRole = RolePermission::where('role_id', $role_id)
            //->whereIn('permission_id', $permissions)
            ->exists();

        return response()->json(['exists' => $existingRole]);
    }

    public function edit($role_id)
    {
        $response = $this->userService->editRolePermission($role_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function update(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'role_id' => 'required|integer',
            'permissions' => 'array|min:1',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors(['message' => $validator->errors()->first()]);
        }

        $role_id = $attributes['role_id'];

        $response = $this->userService->updateRolePermission($attributes->all(), $role_id);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }
}
