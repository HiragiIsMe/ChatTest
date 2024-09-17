<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);

        return response()->json([
            "message" => "User Success Registered",
            "data" => new UserResource($user)
        ], 201);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('username', $data['username'])->first();
 
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('rahasia')->plainTextToken;
        $user->token = $token;

        return response()->json([
            "message" => "Login Success",
            "data" => new UserResource($user)
        ], 200);

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response([
            "message" => "Logout Success"
        ], 200);
    }
}
