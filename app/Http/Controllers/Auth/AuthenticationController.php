<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    /**
     * Summary of register
     * @param \App\Http\Requests\RegisterRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $request->validated();
        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        $user = User::create($userData);
        $token = $user->createToken('blub')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Summary of login
     * @param \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $request->validated();
        $user = User::whereUsername($request->username)->first();
        if (!$user || Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid credentials',
            ], 422);
        }

        $token = $user->createToken('blub')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}