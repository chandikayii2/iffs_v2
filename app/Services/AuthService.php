<?php

namespace App\Services;

use App\Repository\Interfaces\AuthRepositoryInterface;
use App\Services\Interfaces\AuthServiceInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{

    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }


    public function loginCheck($attributes)
    {
        try {

            $credentials = $attributes->only(['email', 'password']);

            if (Auth::attempt($credentials)) {

                $user = Auth::user();

                return ['status' => 200, 'message' => 'Login Successful', 'data' => $user];
            } else {

                return ['status' => 401, 'message' => 'incorrect username or password!', 'data' => null];
            }
        } catch (Exception $e) {

            return ['status' => 500, 'message' => $e->getMessage(), 'data' => null];
        }
    }
}
