<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($partnerId)
    {
        $conversation = ChatConversation::where(function ($query) use ($partnerId) {
        $query->where('participant_one_id', Auth::id())
              ->where('participant_two_id', $partnerId);
    })->orWhere(function ($query) use ($partnerId) {
        $query->where('participant_one_id', $partnerId)
              ->where('participant_two_id', Auth::id());
    })->first();

if (!$conversation) {
    $conversation = ChatConversation::create([
        'participant_one_id' => Auth::id(),
        'participant_two_id' => $partnerId
    ]);
}


        $messages = ChatMessage::where('conversation_id', $conversation->id)
                        ->with('sender')
                        ->orderBy('created_at')
                        ->get();

        return view('retailer.chat', compact('conversation', 'messages', 'partnerId'))
       ->with('sendRouteName', 'retailer.chat.send');

    }

    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'message' => 'required',
        ]);

        ChatMessage::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'message' => $request->message,

        ]);

        return back();
    }
}
