@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        Chat with {{ $partnerName ?? 'Partner ID: ' . $partnerId }}
    </div>

    <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto; background-color: #f9f9f9;">
        @forelse($messages as $msg)
            <div class="mb-2 {{ $msg->sender_id === auth()->id() ? 'text-end' : 'text-start' }}">
                <div class="d-inline-block p-2 rounded 
                    {{ $msg->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light text-dark' }}" 
                    style="max-width: 70%;">
                    {{ $msg->message}}
                </div>
            </div>
        @empty
            <p class="text-center text-muted">No messages yet.</p>
        @endforelse
    </div>

    <div class="card-footer">
        <form action="{{ route($sendRouteName) }}" method="POST">
            @csrf
            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </form>
    </div>
</div>

{{-- Auto-scroll to latest message --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection
