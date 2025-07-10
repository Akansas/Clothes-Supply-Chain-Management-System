<template>
  <div class="chat-widget">
    <h3>Chat with Manufacturer</h3>
    <div v-if="contacts.length === 0">No manufacturers available.</div>
    <div v-else>
      <select v-model="selectedContactId">
        <option v-for="contact in contacts" :key="contact.id" :value="contact.id">
          {{ contact.name }}
        </option>
      </select>
      <div v-if="selectedContactId">
        <div class="messages" style="height:200px;overflow-y:auto;">
          <div v-for="msg in messages" :key="msg.id">
            <b>{{ msg.sender_id === userId ? 'You' : contactName }}:</b>
            {{ msg.message_text }}
            <span style="font-size:0.8em;color:gray;">({{ new Date(msg.created_at).toLocaleTimeString() }})</span>
          </div>
        </div>
        <div v-if="typing" style="color:gray;">{{ contactName }} is typing...</div>
        <input v-model="newMessage" @input="sendTyping" @keyup.enter="sendMessage" placeholder="Type a message..." />
        <button @click="sendMessage">Send</button>
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
      contacts: [],
      selectedContactId: null,
      messages: [],
      newMessage: '',
      typing: false,
      contactName: '',
    };
  },
  watch: {
    selectedContactId(newId) {
      if (newId) {
        this.fetchMessages();
        this.getContactName();
        this.listenForMessages();
      }
    }
  },
  mounted() {
    this.fetchContacts();
  },
  methods: {
    fetchContacts() {
      axios.get('/chat/contacts').then(res => {
        // Only manufacturers
        this.contacts = res.data.filter(c => c.role && c.role.name === 'manufacturer');
      });
    },
    fetchMessages() {
      axios.get(`/chat/messages/${this.selectedContactId}`).then(res => {
        this.messages = res.data;
        this.markAsRead();
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
    },
    listenForMessages() {
      if (!window.Echo) return;
      window.Echo.private('chat.' + this.userId)
        .listen('MessageSent', (e) => {
          if (e.message.sender_id == this.selectedContactId) {
            this.messages.push(e.message);
            this.markAsRead();
          }
        })
        .listen('UserTyping', (e) => {
          if (e.sender_id == this.selectedContactId) {
            this.typing = true;
            setTimeout(() => { this.typing = false; }, 2000);
          }
        });
    },
    getContactName() {
      const contact = this.contacts.find(c => c.id == this.selectedContactId);
      this.contactName = contact ? contact.name : '';
    }
  }
};
</script> 