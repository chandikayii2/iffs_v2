<?php

namespace App\Http\Controllers\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\UserServiceInterface;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function create(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'name' => 'required|string',
            'role_id' => 'required|integer',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);



        if ($validator->fails()) {
            return redirect()->back()->with(['status' => 500, 'message' =>  $validator->errors()->first(),]);
        }

        $response = $this->userService->create($attributes);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }


    public function getAll()
    {
        $response = $this->userService->getAll();

        if ($response['status'] === 200) {
            return view('users.userList', [
                'users' => $response['data']['users'],
                'userRoles' => $response['data']['userRoles'],
            ]);
        } else {
            return view('error')->with('message', $response['message']);
        }
    }

    public function edit($userId)
    {
        $response = $this->userService->edit($userId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }


    public function update(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'userId' => 'required|integer',
            'name' => 'string',
            'email' => 'string',
            'phone' => 'string',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors(['message' => $validator->errors()->first()]);
        }

        $EditUserId = $attributes['userId'];

        $response = $this->userService->update($attributes->all(), $EditUserId);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }

    public function passwordUpdate(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'userPassId' => 'required|integer',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['status' => 500, 'message' =>  $validator->errors()->first(),]);
        }

        $userId = $attributes['userPassId'];

        $response = $this->userService->passwordUpdate($attributes->all(), $userId);

        return redirect()->back()->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
    }
}
