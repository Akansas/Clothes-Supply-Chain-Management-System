<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Message;
use App\Models\User;

class NewMessageNotification extends Notification
{
    use Queueable;

    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $sender = User::find($this->message->sender_id);
        return [
            'message_id' => $this->message->id,
            'from_user_id' => $this->message->sender_id,
            'from_user_name' => $sender->name,
            'message' => "You have a new message from {$sender->name}.",
            'url' => route('chat.show', $this->message->sender_id),
        ];
    }
}
