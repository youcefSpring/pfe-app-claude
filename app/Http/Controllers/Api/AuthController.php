<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateUserRequest;
use App\Http\Requests\PFE\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and generate token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'error' => 'Account Disabled',
                'message' => 'Your account has been disabled. Please contact support.'
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'department' => $user->department,
                'student_id' => $user->student_id,
                'avatar_path' => $user->avatar_path
            ],
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }

    /**
     * Register new user
     */
    public function register(CreateUserRequest $request): JsonResponse
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'student_id' => $request->student_id,
            'department' => $request->department,
            'is_active' => true
        ]);

        // Assign default role
        $defaultRole = $request->student_id ? 'student' : 'teacher';
        $user->assignRole($defaultRole);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'department' => $user->department
            ],
            'message' => 'Registration successful'
        ], 201);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'student_id' => $user->student_id,
                'department' => $user->department,
                'avatar_path' => $user->avatar_path,
                'is_active' => $user->is_active
            ],
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->only([
            'first_name',
            'last_name',
            'phone',
            'avatar_path'
        ]));

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'department' => $user->department,
                'avatar_path' => $user->avatar_path
            ],
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Revoke all user tokens
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices successfully'
        ]);
    }
}