<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        unset($user->created_at);
        unset($user->updated_at);
        unset($user->deleted_at);

        // hapus token lama
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;
        return response([
            'data' => $user
        ]);
    }

    public function me()
    {
        return response([
            'data' => auth()->user()
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logged out'
        ]);
    }
}
