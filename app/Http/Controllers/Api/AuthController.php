<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'       => ['required','email'],
            'password'    => ['required','string'],
            'device_name' => ['sometimes','string','max:100'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $abilities = ($user->role === 'admin') ? ['*'] : ['read'];
        $tokenName = $data['device_name'] ?? 'api';
        $plainText = $user->createToken($tokenName, $abilities)->plainTextToken;

        return response()->json([
            'token'      => $plainText,
            'token_type' => 'Bearer',
            'abilities'  => $abilities,
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 200);
    }

    public function me(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'id'    => $u->id,
            'name'  => $u->name,
            'email' => $u->email,
            'role'  => $u->role,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    public function logoutAll(Request $request)
    {
        $request->user()?->tokens()->delete();
        return response()->json(['message' => 'Logged out from all devices'], 200);
    }
}
