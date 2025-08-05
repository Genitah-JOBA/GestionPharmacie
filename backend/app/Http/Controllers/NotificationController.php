<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Events\MedicamentNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->get()->map(function ($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->title,
                'message' => $notif->message,
                'type' => $notif->type,
                'link' => $notif->link,
                'read' => $notif->read,
                'date' => $notif->created_at->toIso8601String(),
            ];
        });

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $notif = Notification::create([
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'type' => $request->input('type', 'info'),
            'link' => $request->input('link'),
            'read' => false,
        ]);

        broadcast(new MedicamentNotification($notif))->toOthers();

        return response()->json([
            'id' => $notif->id,
            'title' => $notif->title,
            'message' => $notif->message,
            'type' => $notif->type,
            'link' => $notif->link,
            'read' => $notif->read,
            'date' => $notif->created_at->toIso8601String(),
        ]);
    }

    public function markRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marquée comme lue']);
    }
}

