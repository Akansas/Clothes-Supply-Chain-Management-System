<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * Get the conversation this message belongs to.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
    ];
}
