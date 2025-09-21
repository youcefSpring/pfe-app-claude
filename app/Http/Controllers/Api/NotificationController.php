<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PfeNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of notifications
     */
    public function index(Request $request): JsonResponse
    {
        $query = PfeNotification::where('user_id', $request->user()->id);

        // Apply filters
        if ($request->has('read') && $request->read !== null) {
            if ($request->boolean('read')) {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate($request->get('per_page', 20));

        // Get unread count
        $unreadCount = PfeNotification::where('user_id', $request->user()->id)
                                     ->whereNull('read_at')
                                     ->count();

        return response()->json([
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'last_page' => $notifications->lastPage()
            ],
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(PfeNotification $notification): JsonResponse
    {
        $this->authorize('update', $notification);

        $notification->markAsRead();

        return response()->json([
            'notification' => $notification->fresh(),
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead($request->user()->id);

        return response()->json([
            'marked_count' => $count,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(PfeNotification $notification): JsonResponse
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = PfeNotification::where('user_id', $request->user()->id)
                               ->whereNull('read_at')
                               ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Get recent notifications
     */
    public function recent(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getUnreadNotifications(
            $request->user()->id,
            $request->get('limit', 10)
        );

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Get notification statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $stats = $this->notificationService->getNotificationStats($request->user()->id);

        return response()->json([
            'stats' => $stats
        ]);
    }

    /**
     * Create a new notification (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', PfeNotification::class);

        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'type' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $count = $this->notificationService->createBulkNotifications(
            $request->user_ids,
            $request->type,
            $request->title,
            $request->message,
            $request->data ?? []
        );

        return response()->json([
            'created_count' => $count,
            'message' => 'Notifications created successfully'
        ], 201);
    }
}