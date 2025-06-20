<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;
use App\Notifications\NewMessageNotification;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recentChats = $user->recentChats;
        return view('chat.index', compact('recentChats'));
    }

    public function show($userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'is_read' => true]);

        // Get messages between the two users
        $messages = Message::where(function($query) use ($user, $userId) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $userId);
            })
            ->orWhere(function($query) use ($user, $userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $recentChats = $user->recentChats;
        
        return view('chat.show', compact('messages', 'otherUser', 'recentChats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content
        ]);
        
        // Send notification to the recipient
        $recipient = User::find($request->receiver_id);
        if ($recipient) {
            $recipient->notify(new NewMessageNotification($message));
        }

        broadcast(new NewChatMessage($message));

        return response()->json($message);
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->receivedMessages()->whereNull('read_at')->count()
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id'
        ]);

        $message = Message::findOrFail($request->message_id);
        
        if ($message->receiver_id === Auth::id()) {
            $message->update(['read_at' => now(), 'is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
} 