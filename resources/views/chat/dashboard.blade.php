@extends('layouts.app')

@section('content')
<style>
.floating-chat-btn {
    position: fixed !important;
    bottom: 30px !important;
    right: 30px !important;
    width: 60px !important;
    height: 60px !important;
    font-size: 1.5em !important;
    z-index: 9999 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
    transition: all 0.3s ease !important;
}

.floating-chat-btn:hover {
    transform: scale(1.1) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.4) !important;
}
</style>
<div class="container py-4">
    <h2 class="mb-4">Your Conversations</h2>
    <div class="list-group">
        @forelse($conversations as $conversation)
            @php
                $other = $conversation->participants->where('id', '!=', auth()->id())->first();
                $last = $conversation->messages->first();
            @endphp
            <a href="{{ route('chat.show', $conversation) }}" class="list-group-item list-group-item-action">
                <strong>{{ $other ? $other->name : 'Unknown' }}</strong>
                <div class="small text-muted">
                    {{ $last ? Str::limit($last->body, 40) : 'No messages yet.' }}
                </div>
            </a>
        @empty
            <div class="text-muted">No conversations yet.</div>
        @endforelse
    </div>
</div>

<!-- Floating New Message Button -->
<button type="button" class="btn btn-primary rounded-circle floating-chat-btn" data-bs-toggle="modal" data-bs-target="#newMessageModal">
    <i class="fas fa-plus"></i>
</button>

<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newMessageModalLabel">Start New Conversation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="startChatForm" method="GET">
        <div class="modal-body">
          <label for="contactSelect" class="form-label">Select Contact</label>
          <select class="form-select" id="contactSelect" name="user_id" required>
            <option value="">Choose...</option>
            @php
                $user = auth()->user();
                if ($user->hasRole('manufacturer')) {
                    $contacts = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'raw_material_supplier'); })->where('id', '!=', $user->id)->get();
                } elseif ($user->hasRole('raw_material_supplier')) {
                    $contacts = \App\Models\User::whereHas('role', function($q) { $q->where('name', 'manufacturer'); })->where('id', '!=', $user->id)->get();
                } else {
                    $contacts = collect();
                }
            @endphp
            @foreach($contacts as $contact)
                <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->role->name }})</option>
            @endforeach
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Start Chat</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('startChatForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var userId = document.getElementById('contactSelect').value;
        if (userId) {
            window.location.href = '/chat/with/' + userId;
        }
    });
});
</script>
@endsection 