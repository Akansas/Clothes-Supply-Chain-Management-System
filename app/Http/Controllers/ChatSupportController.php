<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatSupportController extends Controller
{
    public function index()
    {
        return view('chat.support');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required_without:attachment|string|nullable',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Find or create support admin user
        $supportAdmin = User::firstOrCreate(
            ['email' => 'support@admin.com'],
            [
                'name' => 'Support Admin',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now()
            ]
        );

        $messageData = [
            'sender_id' => Auth::id(),
            'receiver_id' => $supportAdmin->id,
            'content' => $request->content
        ];

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('chat_attachments', $fileName, 'public');

            $messageData['attachment_path'] = $path;
            $messageData['attachment_type'] = $file->getMimeType();
            $messageData['attachment_name'] = $file->getClientOriginalName();
        }

        $message = Message::create($messageData);

        if ($request->ajax()) {
            return response()->json([
                'message' => $message->load('sender')
            ]);
        }

        return back();
    }

    public function getMessages()
    {
        $user = Auth::user();
        $supportAdmin = User::where('email', 'support@admin.com')->first();

        if (!$supportAdmin) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::where(function($query) use ($user, $supportAdmin) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $supportAdmin->id);
            })
            ->orWhere(function($query) use ($user, $supportAdmin) {
                $query->where('sender_id', $supportAdmin->id)
                    ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    public function updateTypingStatus(Request $request)
    {
        $isTyping = $request->input('is_typing', false);
        $userId = Auth::id();
        
        // Store typing status in cache with 5-second expiration
        cache()->put("user.{$userId}.typing", $isTyping, now()->addSeconds(5));
        
        return response()->json(['success' => true]);
    }

    public function getTypingStatus()
    {
        $supportAdmin = User::where('email', 'support@admin.com')->first();
        
        if (!$supportAdmin) {
            return response()->json(['is_typing' => false]);
        }

        $isTyping = cache()->get("user.{$supportAdmin->id}.typing", false);
        
        return response()->json(['is_typing' => $isTyping]);
    }

    public function downloadAttachment($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // Check if user has access to this message
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        return Storage::disk('public')->download(
            $message->attachment_path,
            $message->attachment_name
        );
    }
} 