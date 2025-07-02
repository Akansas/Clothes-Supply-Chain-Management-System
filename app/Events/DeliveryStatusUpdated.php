<?php

namespace App\Events;

use App\Models\Delivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeliveryStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $delivery;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }

    public function broadcastOn()
    {
        return new Channel('deliveries');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->delivery->id,
            'status' => $this->delivery->status,
        ];
    }
} 