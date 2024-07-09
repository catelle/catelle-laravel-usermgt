<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request; 

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

       
        $token = $user->createToken('main')->plainTextToken;
        

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!auth()->attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid login details'
                ], 422);
            }

            $user = auth()->user();
            $token = $user->createToken('main')->plainTextToken;

            return response([
                'user' => $user,
                'token' => $token,
            ], 200);
       
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
