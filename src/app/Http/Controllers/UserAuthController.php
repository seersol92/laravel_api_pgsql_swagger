<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected UserService $userService ){
    }

    public function register(UserRequest $UserRequest){
        $user = $this->userService->register($UserRequest->validated());
        return $this->successResponse($user, 'User registered. Please verify your email.', 200);
    }

    public function login(Request $request){
        $loginUserData = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
        $user = $this->userService->login($loginUserData);
        if ($user)
            return $this->successResponse($user, 'Ok', 200);
        return $this->errorResponse('Invalid Credentials.', 401);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return $this->successResponse([], 'User logged out.', 200);
    }

    public function profile()
    {
        return $this->successResponse(auth()->user(), 'Ok', 200);
    }
}
