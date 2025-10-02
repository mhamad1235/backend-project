<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getAccountNotifications(Request $request)
    {

        try {
        
        $account = auth('account')->user();
        $notifications = $account->notifications()->with('user')->orderBy('created_at', 'desc')->get();

    $data=$notifications->map(function($notification) {
        return [
            'id' => $notification->id,
            'type' => $notification->notifiable_type,
            'is_read' => $notification->is_read == 1 ? true : false,
            'message' => $notification->message,
            'title' => $notification->title,
            'created_at' => $notification->created_at,
            'user' => $notification->user ? [
                'id' => $notification->user->id,
                'name' => $notification->user->name,
                
            ] : null,
        ];
    })->toArray();
         return $this->jsonResponse(true, 'get Notifications ✅', 200, $data);
        } catch (\Throwable $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
       
       
    }

    public function getUserNotifications(Request $request)
    {   
       
        $user = auth()->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        $data=$notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->notifiable_type,
                'is_read' => $notification->is_read == 1 ? true : false,
                'message' => $notification->message,
                'title' => $notification->title,
                'created_at' => $notification->created_at,
                'account' => $notification->account ? [
                    'id' => $notification->account->id,
                    'name' => $notification->account->name,
                    
                ] : null,
            ];
        })->toArray();
         return $this->jsonResponse(true, 'get Notifications ✅', 200, $data);
       
    }
}
