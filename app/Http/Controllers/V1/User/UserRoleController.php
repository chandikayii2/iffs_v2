<?php

namespace App\Http\Controllers\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\UserServiceInterface;

class UserRoleController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function create(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'role_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['status' => 500, 'message' =>  $validator->errors()->first()]);
        }

        $response = $this->userService->userRoleCreate($attributes);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }


    public function getAllUserRole()
    {
        $response = $this->userService->getAllUserRole();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $user_role = $response['data'];
        } else {
            $user_role = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('users.userRoleList', compact('user_role'));
    }
}
