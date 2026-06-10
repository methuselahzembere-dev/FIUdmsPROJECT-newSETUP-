<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $notifications = DB::table('notifications')
            ->where(function ($query) use ($user) {
                $query->where('notifiable_id', $user?->id)
                    ->orWhere('user_id', $user?->id);
            })
            ->latest('created_at')
            ->paginate(20);

        $view = $user?->role === 'institution_representative'
            ? 'institution.notifications.index'
            : 'fiu.notifications.index';

        return view($view, compact('notifications'));
    }

    public function markAsRead(Request $request, int|string $notification): RedirectResponse
    {
        $user = $request->user();

        DB::table('notifications')
            ->where('id', $notification)
            ->where(function ($query) use ($user) {
                $query->where('notifiable_id', $user?->id)
                    ->orWhere('user_id', $user?->id);
            })
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Notification marked as read.');
    }
}
