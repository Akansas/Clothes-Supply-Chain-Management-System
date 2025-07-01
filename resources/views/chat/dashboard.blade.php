@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Conversations</h1>
    <div class="list-group">
        @foreach($conversations as $conversation)
            <a href="{{ route('chat.show', $conversation) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $conversation->other_user->name }}</h5>
                    <small>{{ $conversation->updated_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">
                    @if($conversation->messages->isNotEmpty())
                        {{ Str::limit($conversation->messages->last()->body, 50) }}
                    @endif
                </p>
            </a>
        @endforeach
    </div>
</div>
@endsection 