<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected WorkflowService $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        // Get user workflow status
        $workflowStatus = $this->workflowService->getWorkflowStatus($user);

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => $user->only([
                    'id', 'name', 'email', 'role', 'department',
                    'matricule', 'grade', 'title', 'speciality'
                ]),
                'token' => $token,
                'workflow_status' => $workflowStatus,
            ],
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current user information.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        // Load additional relationships based on role
        $user->load($this->getUserRelationships($user->role));

        // Get workflow status
        $workflowStatus = $this->workflowService->getWorkflowStatus($user);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'workflow_status' => $workflowStatus,
            ],
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $rules = [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:500',
        ];

        // Add role-specific validation rules
        if ($user->role === 'student') {
            $rules['enrollment_year'] = 'sometimes|integer|min:2020|max:' . (date('Y') + 1);
        } elseif (in_array($user->role, ['teacher', 'department_head'])) {
            $rules['office_location'] = 'sometimes|nullable|string|max:255';
            $rules['speciality'] = 'sometimes|string|max:255';
        } elseif ($user->role === 'external_supervisor') {
            $rules['company'] = 'sometimes|string|max:255';
            $rules['position'] = 'sometimes|string|max:255';
            $rules['expertise_areas'] = 'sometimes|nullable|string';
        }

        $validated = $request->validate($rules);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Get user notifications.
     */
    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);

        $notifications = $user->notifications()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(Request $request, $notificationId): JsonResponse
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($notificationId);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get relationships to load based on user role.
     */
    private function getUserRelationships(string $role): array
    {
        switch ($role) {
            case 'student':
                return ['activeTeam.members.student', 'activeTeam.subject', 'activeTeam.project'];

            case 'teacher':
                return ['subjects', 'supervisedProjects.team', 'validatedSubjects'];

            case 'department_head':
                return ['validatedSubjects', 'resolvedConflicts'];

            case 'admin':
                return [];

            case 'external_supervisor':
                return ['supervisedProjects.team'];

            default:
                return [];
        }
    }
}
