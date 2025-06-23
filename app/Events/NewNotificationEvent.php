<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
class NewNotificationEvent implements ShouldBroadcastNow
{
      public $message;
   

    public function __construct($message)
    {  
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('action-channel');
    }
    public function broadcastWith()
{
    \Log::info('Broadcasting ActionExecuted event', [
        'message' => $this->message,
        'channel' => 'action-channel'
    ]);
    
    return [
        'message' => $this->message,
        'time' => now()->toDateTimeString()
    ];
}
    public function broadcastAs(): string
    {
        return 'ActionExecuted';
    }
}
