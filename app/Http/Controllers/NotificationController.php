<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Dapatkan notifikasi yang belum dibaca untuk user yang sedang login
     */
    public function getUnreadNotifications()
    {
        $user = Auth::user();
        // Only return a small list for the topbar dropdown to keep payload fast
        $notifications = $this->notificationService->getUnreadNotifications($user, 10);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Dapatkan jumlah notifikasi yang belum dibaca
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Dapatkan semua notifikasi dengan pagination
     */
    public function getAllNotifications(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status', '');
        $type = $request->get('type', '');

        $query = Notification::where('notifiable_id', $user->id)
                            ->where('notifiable_type', User::class);

        // Filter by status
        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        // Filter by type
        if ($type) {
            $query->where('type', 'like', '%' . $type . '%');
        }

        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total()
            ]
        ]);
    }

    /**
     * Tampilkan halaman semua notifikasi
     */
    public function index()
    {
        return view('main.notifications.index');
    }
}
