<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('userid', Auth::id())
            ->orderBy('time', 'desc')
            ->get();

        return view('notifications.view', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('notificationid', $id)
            ->where('userid', Auth::id())
            ->firstOrFail();

        $notification->isread = true;
        $notification->save();

        return redirect()->back();
    }
}
