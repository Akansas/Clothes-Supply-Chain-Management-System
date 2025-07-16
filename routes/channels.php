<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    // Only allow if the user is the intended receiver or is allowed to chat with the receiver
    return (int) $user->id === (int) $receiverId || 
        app(\Illuminate\Contracts\Auth\Access\Gate::class)->forUser($user)->allows('canChat', [\App\Models\User::class, \App\Models\User::find($receiverId)]);
});
