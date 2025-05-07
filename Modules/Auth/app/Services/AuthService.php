<?php

namespace Modules\Auth\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Admin;

class AuthService
{

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email', null),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'address' => $request->input('address', null),
            'gender' => $request->input('gender', null),
        ]);

        $token = $user->createToken('User Access Token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function login(Request $request)
    {
        if ($request->input('role') == 'user') {
            return $this->loginUser($request);
        } else {
            return $this->loginAdmin($request);
        }
    }

    private function loginUser(LoginRequest $request)
    {
        $user = User::where('phone', $request->input('phone'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return null;
        }

        $token = $user->createToken('User Access Token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
            'role' => 'user',
        ];
    }

    private function loginAdmin(LoginRequest $request)
    {
        $user = Admin::where('phone', $request->input('phone'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return null;
        }

        $token = $user->createToken('Admin Access Token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
            'role' => 'admin',
        ];
    }

    public function logout($user)
    {
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }

    public function resetPassword($user, $password)
    {

        $user->update([
            'password' => Hash::make($password),
            'otp' => null,
        ]);

        $user->tokens()->delete();
    }

    public function createForgetPasswordToken($user)
    {
        $user->tokens()->delete();
        $token = $user->createToken('token');

        $token->accessToken->save();

        return $token->plainTextToken;
    }
}
