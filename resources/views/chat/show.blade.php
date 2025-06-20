@extends('layouts.app', ['activePage' => 'chat', 'title' => 'Chat with ' . $otherUser->name, 'navName' => 'Messages', 'activeButton' => 'laravel'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Chats</h4>
                </div>
                <div class="card-body chat-list">
                    @foreach($recentChats as $contact)
                        <a href="{{ route('chat.show', $contact->id) }}" class="chat-contact d-flex align-items-center p-3 border-bottom {{ $contact->id == $otherUser->id ? 'active' : '' }}">
                            <div class="avatar">
                                <img src="{{ asset('light-bootstrap/img/faces/face-0.jpg') }}" alt="User Avatar" class="rounded-circle" width="50">
                                @if($contact->unreadMessagesCount > 0 && $contact->id != $otherUser->id)
                                    <span class="badge badge-danger badge-pill">{{ $contact->unreadMessagesCount }}</span>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h5 class="mb-0">{{ $contact->name }}</h5>
                                <small class="text-muted">{{ $contact->email }}</small>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('light-bootstrap/img/faces/face-0.jpg') }}" alt="User Avatar" class="rounded-circle" width="40">
                        <h4 class="card-title mb-0 ml-3">{{ $otherUser->name }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chat-messages" id="chat-messages">
                        @foreach($messages as $message)
                            <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                                <div class="message-content">
                                    {{ $message->content }}
                                    <small class="message-time">{{ $message->created_at->format('H:i') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form id="message-form" class="mt-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" id="message-input" placeholder="Type your message...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('message-input');
    const content = input.value.trim();
    
    if (content) {
        fetch('{{ route('chat.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                receiver_id: {{ $otherUser->id }},
                content: content
            })
        })
        .then(response => response.json())
        .then(data => {
            appendMessage(data);
            input.value = '';
            scrollToBottom();
        });
    }
});

function appendMessage(message) {
    const messagesContainer = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message sent';
    messageDiv.innerHTML = `
        <div class="message-content">
            ${message.content}
            <small class="message-time">${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false })}</small>
        </div>
    `;
    messagesContainer.appendChild(messageDiv);
}

function scrollToBottom() {
    const messagesContainer = document.getElementById('chat-messages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Scroll to bottom on page load
scrollToBottom();
</script>

<style>
.chat-list {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}
.chat-contact {
    text-decoration: none !important;
    color: inherit;
    transition: background-color 0.2s;
}
.chat-contact:hover, .chat-contact.active {
    background-color: #f8f9fa;
}
.avatar {
    position: relative;
}
.avatar .badge {
    position: absolute;
    top: -5px;
    right: -5px;
}
.chat-messages {
    height: calc(100vh - 450px);
    overflow-y: auto;
    padding: 20px;
}
.message {
    margin-bottom: 20px;
    display: flex;
}
.message.sent {
    justify-content: flex-end;
}
.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 15px;
    position: relative;
}
.message.sent .message-content {
    background-color: #007bff;
    color: white;
    border-bottom-right-radius: 5px;
}
.message.received .message-content {
    background-color: #f8f9fa;
    border-bottom-left-radius: 5px;
}
.message-time {
    display: block;
    font-size: 0.75rem;
    margin-top: 5px;
    opacity: 0.8;
}
</style>
@endpush
@endsection 