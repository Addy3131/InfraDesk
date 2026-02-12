<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Auth\TokenService;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request, TokenService $tokenService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $accessToken = $tokenService->generateAccessToken($user);
        $refreshToken = $tokenService->generateRefreshToken($user);

        return response()
            ->json(['access_token' => $accessToken])
            ->cookie('refresh_token', $refreshToken, 60 * 24 * 7, null, null, true, true);
    }
    public function login(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $accessToken = $tokenService->generateAccessToken($user);
        $refreshToken = $tokenService->generateRefreshToken($user);

        return response()
            ->json(['access_token' => $accessToken])
            ->cookie('refresh_token', $refreshToken, 60 * 24 * 7, null, null, true, true);
    }

    public function refresh(Request $request, TokenService $tokenService)
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token missing'], 401);
        }

        $result = $tokenService->refreshAccessToken($refreshToken);

        if (!$result) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        return response()
            ->json(['access_token' => $result['access_token']])
            ->cookie('refresh_token', $result['refresh_token'], 60 * 24 * 7, null, null, true, true);
    }

}
