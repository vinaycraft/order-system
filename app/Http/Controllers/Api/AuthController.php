<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|size:10|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'role' => 'customer'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'role' => $user->role
            ]
        ]);
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'mobile' => 'required|string|size:10',
                'password' => 'required|string',
            ]);

            Log::info('Login attempt', ['mobile' => $request->mobile]);

            if (!Auth::attempt($request->only('mobile', 'password'))) {
                Log::info('Auth failed for user', ['mobile' => $request->mobile]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'The provided credentials are incorrect.'
                ], 401);
            }

            $user = User::where('mobile', $request->mobile)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            $response = [
                'status' => 'success',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'role' => $user->role
                ]
            ];

            Log::info('Login successful', ['user_id' => $user->id, 'role' => $user->role]);
            
            return response()->json($response)
                ->header('Content-Type', 'application/json');
                
        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during login',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->save();

        return response()->json($user);
    }
}
