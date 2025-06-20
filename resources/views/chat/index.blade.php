@extends('layouts.app', ['activePage' => 'chat', 'title' => 'Messages', 'navName' => 'Messages', 'activeButton' => 'laravel'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Chats</h4>
                </div>
                <div class="card-body chat-list">
                    @forelse($recentChats as $contact)
                        <a href="{{ route('chat.show', $contact->id) }}" class="chat-contact d-flex align-items-center p-3 border-bottom {{ request()->route('userId') == $contact->id ? 'active' : '' }}">
                            <div class="avatar">
                                <img src="{{ asset('light-bootstrap/img/faces/face-0.jpg') }}" alt="User Avatar" class="rounded-circle" width="50">
                                @if($contact->unreadMessagesCount > 0)
                                    <span class="badge badge-danger badge-pill">{{ $contact->unreadMessagesCount }}</span>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h5 class="mb-0">{{ $contact->name }}</h5>
                                <small class="text-muted">{{ $contact->email }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-4">
                            <p class="mb-0">No conversations yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Select a conversation to start chatting</h4>
                    <p class="text-muted">Choose from your recent conversations or start a new one</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newChatModal">
                        Start New Chat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1" role="dialog" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newChatModalLabel">Start New Chat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newChatForm" action="{{ route('chat.show', ':id') }}" method="GET">
                    <div class="form-group">
                        <label for="user_select">Select User</label>
                        <select class="form-control" id="user_select" required>
                            <option value="">Select a user to chat with</option>
                            @foreach(\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="startNewChat()">Start Chat</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
function startNewChat() {
    const userId = document.getElementById('user_select').value;
    if (userId) {
        window.location.href = "{{ route('chat.show', '') }}/" + userId;
    }
}
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
</style>
@endpush
@endsection 