<?php

namespace App\Policies;

use App\Models\User;

class ChatPolicy
{
    /**
     * Determine if two users can chat.
     */
    public function canChat(User $user, User $other)
    {
        $roles = [$user->role->name, $other->role->name];
        sort($roles);
        if ($roles === ['manufacturer', 'retailer']) return true;
        if ($roles === ['manufacturer', 'supplier']) return true;
        return false;
    }
} 