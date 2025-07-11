define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    'use strict';

    var ChatManager = {
        contextid: null,
        sessionid: null,
        isOpen: false,
        messages: [],

        init: function(contextid, wwwroot) {
            this.contextid = contextid;
            this.wwwroot = wwwroot;
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            // Chat button click
            $(document).on('click', '#activity-chat-btn', function() {
                if (self.isOpen) {
                    self.closeChat();
                } else {
                    self.openChat();
                }
            });

            // Close button
            $(document).on('click', '#chat-close-btn', function() {
                self.closeChat();
            });

            // Minimize button
            $(document).on('click', '#chat-minimize-btn', function() {
                self.minimizeChat();
            });

            // Send message
            $(document).on('click', '#chat-send-btn', function() {
                self.sendMessage();
            });

            // Enter key to send
            $(document).on('keypress', '#chat-input', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    self.sendMessage();
                }
            });

            // Auto-resize textarea
            $(document).on('input', '#chat-input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            });
        },

        openChat: function() {
            var self = this;

            // Start new session
            this.startSession().then(function(sessionData) {
                self.sessionid = sessionData.sessionid;
                self.messages = [];
                self.isOpen = true;

                $('#activity-chat-container').removeClass('minimized').addClass('open');
                $('#activity-chat-btn').addClass('active');
                $('#chat-input').focus();

                // Add welcome message
                self.addMessage('assistant', 'Hello! I\'m here to help you with this activity. What would you like to know?');
            });
        },

        closeChat: function() {
            this.isOpen = false;
            this.sessionid = null;
            this.messages = [];

            $('#activity-chat-container').removeClass('open minimized');
            $('#activity-chat-btn').removeClass('active');
            $('#chat-messages').empty();
            $('#chat-input').val('').css('height', 'auto');
        },

        minimizeChat: function() {
            $('#activity-chat-container').addClass('minimized').removeClass('open');
        },

        startSession: function() {
            return Ajax.call([{
                methodname: 'local_activitychat_start_session',
                args: { contextid: this.contextid }
            }])[0];
        },

        sendMessage: function() {
            var message = $('#chat-input').val().trim();
            if (!message || !this.sessionid) return;

            var self = this;

            // Clear input and show user message
            $('#chat-input').val('').css('height', 'auto');
            this.addMessage('user', message);
            this.showTyping();

            // Send to server
            Ajax.call([{
                methodname: 'local_activitychat_send_message',
                args: {
                    message: message,
                    sessionid: this.sessionid,
                    contextid: this.contextid
                }
            }])[0].then(function(response) {
                self.hideTyping();
                if (response.success) {
                    self.addMessage('assistant', response.message);
                } else {
                    self.addMessage('error', 'Sorry, I encountered an error. Please try again.');
                }
            }).catch(function(error) {
                self.hideTyping();
                self.addMessage('error', 'Network error. Please check your connection and try again.');
                console.error('Chat error:', error);
            });
        },

        addMessage: function(type, content) {
            var timestamp = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            var messageHtml = this.getMessageTemplate(type, content, timestamp);

            $('#chat-messages').append(messageHtml);
            this.scrollToBottom();

            // Animate new message
            $('#chat-messages .chat-message:last-child').addClass('animate-in');
        },

        getMessageTemplate: function(type, content, timestamp) {
            var iconClass = type === 'user' ? 'fa-user' : (type === 'error' ? 'fa-exclamation-triangle' : 'fa-robot');
            var messageClass = 'chat-message chat-message-' + type;

            return `
                <div class="${messageClass}">
                    <div class="message-avatar">
                        <i class="fa ${iconClass}"></i>
                    </div>
                    <div class="message-content">
                        <div class="message-text">${this.escapeHtml(content)}</div>
                        <div class="message-time">${timestamp}</div>
                    </div>
                </div>
            `;
        },

        showTyping: function() {
            var typingHtml = `
                <div class="chat-message chat-typing" id="typing-indicator">
                    <div class="message-avatar">
                        <i class="fa fa-robot"></i>
                    </div>
                    <div class="message-content">
                        <div class="typing-dots">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            `;
            $('#chat-messages').append(typingHtml);
            this.scrollToBottom();
        },

        hideTyping: function() {
            $('#typing-indicator').remove();
        },

        scrollToBottom: function() {
            var messages = document.getElementById('chat-messages');
            messages.scrollTop = messages.scrollHeight;
        },

        escapeHtml: function(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    return ChatManager;
});