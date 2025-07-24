@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h4>Chat with {{ $conversation->participants->where('id', '!=', auth()->id())->first()->name }}</h4>
    <div class="card mb-3">
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            @foreach($conversation->messages as $message)
                <div class="mb-2">
                    <strong>{{ $message->user->id == auth()->id() ? 'You' : $message->user->name }}:</strong>
                    {{ $message->body }}
                    <span class="text-muted small">{{ $message->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <form action="{{ route('chat.store', $conversation) }}" method="POST" class="d-flex align-items-center">
                @csrf
                <input type="text" name="body" class="form-control me-2" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
    <a href="{{ route('chat.index') }}" class="btn btn-link">Back to Conversations</a>
</div>
@endsection 