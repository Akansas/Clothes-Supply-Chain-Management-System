@extends('layouts.app', ['activePage' => 'chat_support', 'title' => 'Customer Support', 'navName' => 'Support', 'activeButton' => 'laravel'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="nc-icon nc-chat-33 mr-2" style="font-size: 24px;"></i>
                        <h4 class="card-title mb-0">Customer Support</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chat-messages" id="chat-messages">
                        <div class="text-center mb-4">
                            <p class="text-muted">Welcome to our customer support! How can we help you today?</p>
                        </div>
                        <!-- Messages will be loaded here -->
                    </div>
                    <div id="typing-indicator" class="typing-indicator d-none">
                        <span>Support is typing</span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                    <form id="message-form" class="mt-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" id="message-input" placeholder="Type your message...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('file-input').click()">
                                    <i class="nc-icon nc-paper-2"></i>
                                </button>
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                        </div>
                        <input type="file" id="file-input" class="d-none" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                        <small class="text-muted">Supported files: Images, PDF, DOC, XLS, TXT (max 10MB)</small>
                        <div id="file-preview" class="mt-2 d-none">
                            <div class="selected-file d-flex align-items-center p-2 bg-light rounded">
                                <i class="nc-icon nc-paper-2 mr-2"></i>
                                <span class="file-name"></span>
                                <button type="button" class="btn btn-link text-danger ml-auto" onclick="removeFile()">
                                    <i class="nc-icon nc-simple-remove"></i>
                                </button>
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
let lastMessageId = 0;
let typingTimeout = null;
let selectedFile = null;

// Load existing messages
function loadMessages() {
    fetch('{{ route('chat.support.messages') }}')
        .then(response => response.json())
        .then(data => {
            const messagesContainer = document.getElementById('chat-messages');
            messagesContainer.innerHTML = ''; // Clear existing messages
            
            data.messages.forEach(message => {
                appendMessage(message);
                lastMessageId = Math.max(lastMessageId, message.id);
            });
            
            scrollToBottom();
        });
}

// Check typing status
function checkTypingStatus() {
    fetch('{{ route('chat.support.typing') }}')
        .then(response => response.json())
        .then(data => {
            const typingIndicator = document.getElementById('typing-indicator');
            typingIndicator.classList.toggle('d-none', !data.is_typing);
        });
}

// Send typing status
function sendTypingStatus(isTyping) {
    fetch('{{ route('chat.support.typing.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ is_typing: isTyping })
    });
}

// Handle file input
document.getElementById('file-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) { // 10MB
            alert('File size must be less than 10MB');
            this.value = '';
            return;
        }
        selectedFile = file;
        const preview = document.getElementById('file-preview');
        preview.classList.remove('d-none');
        preview.querySelector('.file-name').textContent = file.name;
    }
});

function removeFile() {
    selectedFile = null;
    document.getElementById('file-input').value = '';
    document.getElementById('file-preview').classList.add('d-none');
}

// Send message
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('message-input');
    const content = input.value.trim();
    
    if (!content && !selectedFile) {
        return;
    }

    const formData = new FormData();
    if (content) {
        formData.append('content', content);
    }
    if (selectedFile) {
        formData.append('attachment', selectedFile);
    }

    fetch('{{ route('chat.support.send') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        appendMessage(data.message);
        input.value = '';
        removeFile();
        scrollToBottom();
    });
});

// Handle typing events
document.getElementById('message-input').addEventListener('input', function() {
    clearTimeout(typingTimeout);
    sendTypingStatus(true);
    
    typingTimeout = setTimeout(() => {
        sendTypingStatus(false);
    }, 3000);
});

function appendMessage(message) {
    const messagesContainer = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    const isCurrentUser = message.sender_id === {{ auth()->id() }};
    
    let attachmentHtml = '';
    if (message.attachment_path) {
        const isImage = message.attachment_type.startsWith('image/');
        if (isImage) {
            attachmentHtml = `
                <div class="message-attachment">
                    <img src="{{ asset('storage') }}/${message.attachment_path}" class="img-fluid rounded" alt="Attachment">
                </div>
            `;
        } else {
            attachmentHtml = `
                <div class="message-attachment">
                    <a href="{{ route('chat.support.download', '') }}/${message.id}" class="btn btn-sm btn-light">
                        <i class="nc-icon nc-cloud-download-93"></i> ${message.attachment_name}
                    </a>
                </div>
            `;
        }
    }
    
    messageDiv.className = `message ${isCurrentUser ? 'sent' : 'received'}`;
    messageDiv.innerHTML = `
        <div class="message-content">
            ${message.content ? `<div class="message-text">${message.content}</div>` : ''}
            ${attachmentHtml}
            <small class="message-time">${formatDate(message.created_at)}</small>
        </div>
    `;
    messagesContainer.appendChild(messageDiv);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
}

function scrollToBottom() {
    const messagesContainer = document.getElementById('chat-messages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Load messages on page load
loadMessages();

// Poll for new messages and typing status
setInterval(() => {
    loadMessages();
    checkTypingStatus();
}, 3000);

// Play notification sound for new messages
let audio = new Audio('{{ asset('notification.mp3') }}');
function playNotification() {
    audio.play().catch(e => console.log('Error playing notification:', e));
}
</script>

<style>
.chat-messages {
    height: calc(100vh - 450px);
    min-height: 300px;
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
.message-attachment {
    margin-top: 5px;
}
.message-attachment img {
    max-width: 200px;
    max-height: 200px;
}
.typing-indicator {
    padding: 10px;
    margin: 10px 0;
    color: #666;
}
.typing-indicator .dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #666;
    margin-left: 3px;
    animation: typing 1s infinite;
}
.typing-indicator .dot:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator .dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
</style>
@endpush
@endsection 