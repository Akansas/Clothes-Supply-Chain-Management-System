<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $conversations = $user->conversations()->with('messages.user', 'participants')->get()->map(function ($conversation) use ($user) {
            $conversation->other_user = $conversation->participants->where('id', '!=', $user->id)->first();
            return $conversation;
        });
        return view('chat.dashboard', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $conversation->load('messages.user', 'participants');
        $conversation->other_user = $conversation->participants->where('id', '!=', auth()->id())->first();
        return view('chat.conversation', compact('conversation'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'body' => 'required|string',
        ]);

        $conversation->messages()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return back();
    }

    public function editMessage(Conversation $conversation, Message $message)
    {
        $this->authorize('update', $message);
        return view('chat.edit-message', compact('conversation', 'message'));
    }

    public function updateMessage(Request $request, Conversation $conversation, Message $message)
    {
        $this->authorize('update', $message);
        $request->validate(['body' => 'required|string']);
        $message->update(['body' => $request->body]);
        return redirect()->route('chat.show', $conversation)->with('success', 'Message updated!');
    }

    public function destroyMessage(Conversation $conversation, Message $message)
    {
        $this->authorize('delete', $message);
        $message->delete();
        return redirect()->route('chat.show', $conversation)->with('success', 'Message deleted!');
    }

    /**
     * List allowed contacts for direct chat.
     */
    public function contacts()
    {
        $user = auth()->user();
        $role = $user->role->name;
        $contacts = collect();
        if ($role === 'manufacturer') {
            $contacts = User::whereHas('role', function($q) {
                $q->whereIn('name', ['supplier', 'retailer']);
            })->get();
        } elseif ($role === 'supplier') {
            $contacts = User::whereHas('role', function($q) {
                $q->where('name', 'manufacturer');
            })->get();
        } elseif ($role === 'retailer') {
            $contacts = User::whereHas('role', function($q) {
                $q->where('name', 'manufacturer');
            })->get();
        }
        // Enforce policy: only return contacts user is allowed to chat with
        $contacts = $contacts->filter(function($contact) use ($user) {
            return app(\Illuminate\Contracts\Auth\Access\Gate::class)->forUser($user)->allows('canChat', [\App\Models\User::class, $contact]);
        })->values();
        // Eager load role for frontend display
        $contacts->load('role');
        return response()->json($contacts);
    }

    /**
     * Show messages between the authenticated user and another user.
     */
    public function messages(User $other)
    {
        $user = auth()->user();
        $this->authorize('canChat', [User::class, $other]);
        $messages = \App\Models\Message::where(function($q) use ($user, $other) {
            $q->where('sender_id', $user->id)->where('receiver_id', $other->id);
        })->orWhere(function($q) use ($user, $other) {
            $q->where('sender_id', $other->id)->where('receiver_id', $user->id);
        })->orderBy('created_at')->get();
        return response()->json($messages);
    }

    /**
     * Send a message to another user.
     */
    public function send(Request $request, User $other)
    {
        $user = auth()->user();
        $this->authorize('canChat', [User::class, $other]);
        $request->validate(['message_text' => 'required|string']);
        $message = \App\Models\Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $other->id,
            'message_text' => $request->message_text,
        ]);
        event(new \App\Events\MessageSent($message));
        return response()->json($message);
    }

    /**
     * Get unread message notifications for the authenticated user.
     */
    public function notifications()
    {
        $user = auth()->user();
        $unread = \App\Models\Message::where('receiver_id', $user->id)->whereNull('read_at')->count();
        return response()->json(['unread' => $unread]);
    }

    /**
     * Mark messages as read between the authenticated user and another user.
     */
    public function markAsRead(User $other)
    {
        $user = auth()->user();
        $this->authorize('canChat', [User::class, $other]);
        \App\Models\Message::where('sender_id', $other->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['status' => 'success']);
    }

    /**
     * Broadcast typing indicator to the other user.
     */
    public function typing(User $other)
    {
        $user = auth()->user();
        $this->authorize('canChat', [User::class, $other]);
        broadcast(new \App\Events\UserTyping($user, $other))->toOthers();
        return response()->json(['status' => 'typing']);
    }
}
