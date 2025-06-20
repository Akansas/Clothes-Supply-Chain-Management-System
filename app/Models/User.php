<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'address',
        'bio',
        'notification_preferences'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_preferences' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that have default values.
     *
     * @var array
     */
    protected $attributes = [
        'notification_preferences' => '{
            "email": true,
            "push": true,
            "chat": true
        }',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar)
            : asset('light-bootstrap/img/default-avatar.png');
    }

    public function hasUnreadMessages()
    {
        return $this->receivedMessages()
            ->whereNull('read_at')
            ->exists();
    }

    public function markMessagesAsRead()
    {
        $this->receivedMessages()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getRecentChatsAttribute()
    {
        $sentTo = $this->messages()
            ->select('receiver_id as user_id')
            ->distinct();

        $receivedFrom = $this->receivedMessages()
            ->select('sender_id as user_id')
            ->distinct();

        $allChats = $sentTo->union($receivedFrom)->get();
        
        $users = $allChats->map(function ($chat) {
            $user = User::find($chat->user_id);
            if ($user && $user->id !== $this->id) {
                // Add unread message count for this user
                $user->unreadMessagesCount = $this->receivedMessages()
                    ->where('sender_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
                return $user;
            }
            return null;
        })->filter()->unique('id');
        
        return $users;
    }

    public function getUnreadMessagesCountAttribute()
    {
        return $this->receivedMessages()->whereNull('read_at')->count();
    }
}
