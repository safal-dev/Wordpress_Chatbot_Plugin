jQuery(document).ready(function($) {
    const chatContainer = $('.wp-chatbot-container');
    const chatToggle = $('.wp-chatbot-toggle');
    const chatMessages = $('.wp-chatbot-messages');
    const chatForm = $('#wp-chatbot-form');
    const chatInput = $('#wp-chatbot-input');

    // Toggle chat widget
    chatToggle.on('click', function() {
        chatContainer.toggleClass('wp-chatbot-hidden');
        chatToggle.toggleClass('wp-chatbot-hidden');
        if (!chatContainer.hasClass('wp-chatbot-hidden')) {
            chatInput.focus();
        }
    });

    // Handle message submission
    chatForm.on('submit', function(e) {
        e.preventDefault();
        const message = chatInput.val().trim();
        
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        chatInput.val('').focus();

        // Show typing indicator
        const typingIndicator = $('<div class="wp-chatbot-message wp-chatbot-bot-message wp-chatbot-typing">Thinking...</div>');
        chatMessages.append(typingIndicator);
        scrollToBottom();

        // Send message to server
        fetch(wpChatbot.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': wpChatbot.nonce
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Remove typing indicator
            $('.wp-chatbot-typing').remove();
            
            if (data.error) {
                throw new Error(data.error.message || 'An error occurred');
            }
            
            // Add bot response
            addMessage(data.response, 'bot');
        })
        .catch(error => {
            console.error('Error:', error);
            // Remove typing indicator
            $('.wp-chatbot-typing').remove();
            // Add error message
            addMessage('Sorry, I encountered an error. Please try again or contact support if the issue persists.', 'bot error');
        });
    });

    function addMessage(message, type) {
        const messageDiv = $('<div>')
            .addClass('wp-chatbot-message')
            .addClass(`wp-chatbot-${type}-message`)
            .text(message);
        
        chatMessages.append(messageDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // Handle Enter key in input
    chatInput.on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            chatForm.submit();
        }
    });

    // Close chat when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.wp-chatbot-container, .wp-chatbot-toggle').length) {
            chatContainer.addClass('wp-chatbot-hidden');
            chatToggle.removeClass('wp-chatbot-hidden');
        }
    });

    // Prevent clicks inside chat container from closing it
    chatContainer.on('click', function(e) {
        e.stopPropagation();
    });

    // Prevent toggle button clicks from triggering document click
    chatToggle.on('click', function(e) {
        e.stopPropagation();
    });
});
