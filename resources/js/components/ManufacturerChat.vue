<template>
  <div>
    <!-- Floating Chat Button -->
    <div v-if="!open" class="floating-chat-btn" @click="open = true">
      <i class="fas fa-comments"></i>
      <span v-if="totalUnread > 0" class="unread-badge">{{ totalUnread }}</span>
    </div>
    <!-- Chat Window -->
    <div v-if="open" class="chat-window">
      <div class="chat-header">
        <span>Chat</span>
        <button class="close-btn" @click="open = false">&times;</button>
      </div>
      <div class="chat-body">
        <div class="contact-list">
          <div v-for="contact in contacts" :key="contact.id" :class="['contact', {active: selectedContactId === contact.id}]" @click="selectContact(contact.id)">
            <span>{{ contact.name }} <small>({{ contact.role ? contact.role.name : '' }})</small></span>
            <span v-if="unread[contact.id]" class="unread-badge">{{ unread[contact.id] }}</span>
          </div>
        </div>
        <div class="chat-content" v-if="selectedContactId">
          <div class="messages" ref="messagesContainer">
            <div v-for="msg in messages" :key="msg.id" :class="['message', msg.sender_id === userId ? 'sent' : 'received']">
              <div class="msg-text">{{ msg.message_text }}</div>
              <div class="msg-meta">
                <span>{{ msg.sender_id === userId ? 'You' : contactName }}</span>
                <span class="timestamp">{{ new Date(msg.created_at).toLocaleTimeString() }}</span>
              </div>
            </div>
          </div>
          <div v-if="typing" class="typing-indicator">{{ contactName }} is typing...</div>
          <div class="input-row">
            <input v-model="newMessage" @input="sendTyping" @keyup.enter="sendMessage" placeholder="Type a message..." />
            <button @click="sendMessage">Send</button>
          </div>
        </div>
        <div v-else class="select-contact-msg">Select a contact to start chatting.</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['userId'],
  data() {
    return {
      open: false,
      contacts: [],
      selectedContactId: null,
      messages: [],
      newMessage: '',
      typing: false,
      contactName: '',
      unread: {},
    };
  },
  computed: {
    totalUnread() {
      return Object.values(this.unread).reduce((a, b) => a + b, 0);
    }
  },
  watch: {
    selectedContactId(newId) {
      if (newId) {
        this.fetchMessages();
        this.getContactName();
        this.listenForMessages();
        this.markAsRead();
        this.unread[newId] = 0;
        this.$nextTick(this.scrollToBottom);
      }
    },
    messages() {
      this.$nextTick(this.scrollToBottom);
    }
  },
  mounted() {
    this.fetchContacts();
    this.fetchUnread();
    if (window.Echo) {
      window.Echo.private('chat.' + this.userId)
        .listen('MessageSent', (e) => {
          if (e.message.sender_id !== this.userId) {
            if (this.selectedContactId === e.message.sender_id) {
              this.messages.push(e.message);
              this.markAsRead();
            } else {
              this.unread[e.message.sender_id] = (this.unread[e.message.sender_id] || 0) + 1;
            }
          }
        })
        .listen('UserTyping', (e) => {
          if (e.sender_id == this.selectedContactId) {
            this.typing = true;
            setTimeout(() => { this.typing = false; }, 2000);
          }
        });
    }
  },
  methods: {
    fetchContacts() {
      axios.get('/chat/contacts').then(res => {
        this.contacts = res.data.filter(c => c.role && (c.role.name === 'supplier' || c.role.name === 'retailer'));
      });
    },
    fetchMessages() {
      axios.get(`/chat/messages/${this.selectedContactId}`).then(res => {
        this.messages = res.data;
      });
    },
    fetchUnread() {
      axios.get('/chat/notifications').then(res => {
        if (res.data && res.data.unread) {
          // Optionally, you can fetch per-contact unread counts from backend if available
        }
      });
    },
    sendMessage() {
      if (!this.newMessage.trim()) return;
      axios.post(`/chat/send/${this.selectedContactId}`, { message_text: this.newMessage }).then(res => {
        this.messages.push(res.data);
        this.newMessage = '';
      });
    },
    sendTyping() {
      axios.post(`/chat/typing/${this.selectedContactId}`);
    },
    markAsRead() {
      axios.post(`/chat/mark-as-read/${this.selectedContactId}`);
      this.unread[this.selectedContactId] = 0;
    },
    selectContact(id) {
      this.selectedContactId = id;
    },
    getContactName() {
      const contact = this.contacts.find(c => c.id == this.selectedContactId);
      this.contactName = contact ? contact.name : '';
    },
    scrollToBottom() {
      const container = this.$refs.messagesContainer;
      if (container) {
        container.scrollTop = container.scrollHeight;
      }
    }
  }
};
</script>

<style scoped>
.floating-chat-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: #007bff;
  color: #fff;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2em;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  cursor: pointer;
  z-index: 1000;
}
.unread-badge {
  background: #dc3545;
  color: #fff;
  border-radius: 50%;
  padding: 2px 8px;
  font-size: 0.8em;
  margin-left: 5px;
}
.chat-window {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 350px;
  max-height: 500px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 16px rgba(0,0,0,0.25);
  z-index: 1001;
  display: flex;
  flex-direction: column;
}
.chat-header {
  background: #007bff;
  color: #fff;
  padding: 10px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.close-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.2em;
  cursor: pointer;
}
.chat-body {
  display: flex;
  height: 400px;
}
.contact-list {
  width: 120px;
  border-right: 1px solid #eee;
  overflow-y: auto;
  padding: 10px 0;
}
.contact {
  padding: 8px 12px;
  cursor: pointer;
  border-radius: 5px;
  margin-bottom: 5px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.contact.active, .contact:hover {
  background: #f0f8ff;
}
.chat-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 10px;
}
.messages {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 10px;
}
.message {
  margin-bottom: 8px;
  max-width: 80%;
}
.message.sent {
  align-self: flex-end;
  background: #e6f7ff;
  border-radius: 10px 10px 0 10px;
  padding: 6px 12px;
}
.message.received {
  align-self: flex-start;
  background: #f8f9fa;
  border-radius: 10px 10px 10px 0;
  padding: 6px 12px;
}
.msg-meta {
  font-size: 0.75em;
  color: #888;
  display: flex;
  justify-content: space-between;
}
.input-row {
  display: flex;
  gap: 5px;
}
.input-row input {
  flex: 1;
  padding: 6px;
  border-radius: 5px;
  border: 1px solid #ccc;
}
.input-row button {
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 6px 12px;
  cursor: pointer;
}
.typing-indicator {
  font-size: 0.9em;
  color: #888;
  margin-bottom: 5px;
}
.select-contact-msg {
  color: #888;
  text-align: center;
  margin-top: 40px;
}
</style> 