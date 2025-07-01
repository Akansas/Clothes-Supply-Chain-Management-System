<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('orders.' . $this->order->vendor_id);
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'vendor_id' => $this->order->vendor_id,
            'status' => $this->order->status,
            'total_amount' => $this->order->total_amount,
        ];
    }
} 