<?php
namespace App\Services;

use App\Repositories\Repository\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $user = $this->userRepository->add([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => null, // Assuming email verification is required
        ]);
        // Send verification email
        #$user->sendEmailVerificationNotification();
        return $user;
    }

    public function login(array $loginUserData)
    {
        $user = $this->userRepository->findByAttributes(['email' => $loginUserData['email']]);
        if(!$user || !Hash::check($loginUserData['password'], $user->password)) return false;
        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        return [
            'user' => $user->only('id', 'name', 'email'),
            'token' => $token
        ];
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
    }
}
