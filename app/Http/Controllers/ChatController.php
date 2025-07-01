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
}
