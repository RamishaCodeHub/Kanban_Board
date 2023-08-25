<template>
  <div class="container">
    <div class="chat card">
      <div class="scrollable card-body" ref="scrollableChat">
        <template v-for="message in messages" :key="message.id">
          <div :class="[isMessageFromCurrentUser(message) ? 'message-send' : 'message-receive']">
            <p>
              <strong class="primary-font">{{ message.user.name }} :</strong>
              {{ message.message }}
            </p>
          </div>
        </template>
      </div>

      <div class="chat-form input-group">
        <input
          id="btn-input"
          type="text"
          name="message"
          class="form-control input-sm message-"
          placeholder="Type your message here..."
          v-model="newMessage"
          @keyup.enter="addMessage"
        />

        <span class="input-group-btn">
          <button class="btn btn-primary" id="btn-chat" @click="addMessage">Send</button>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Echo from 'laravel-echo';

export default {
  props: ['user'],
  data() {
    return {
      messages: [], 
      newMessage: '',
    };
  },
  methods: {
    isMessageFromCurrentUser(message) {
      return message.user && message.user.id === this.user.id;
    },

    fetchMessages() {
      return axios.get('api/get-messages')
        .then(response => { 
          this.messages = response.data.messages;
        })
        .catch(error => {
          console.error('Error fetching messages:', error);
        });
    },
    
    addMessage() {
      console.log('addMessage called'); 
      if (!this.newMessage.trim()) return;

      const userMessage = {
        // user: this.user,
        message: this.newMessage,
      };

      console.log('check', userMessage)

      try {
        axios
          .post('/api/send-message',  userMessage)
          .then((response) => {
            this.messages.push(response.data);
            this.newMessage = '';
          })
      } catch (error) {
        console.error('Error sending message:', error);
      }
    },    
  },
 
  created() {
    try {
      this.fetchMessages();
      window.Echo.private('chat-channel')
        .listen('MessageSent', (e) => {
            this.messages.push({
            message: e.message.message,
            user: e.user
            });
        });
      console.log('Messages fetched successfully');
    } catch (error) {
      console.error('Error while fetching messages:', error);
    }
  },
};
</script>