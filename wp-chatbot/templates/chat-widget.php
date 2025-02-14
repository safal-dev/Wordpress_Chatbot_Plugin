<div class="wp-chatbot-toggle">
    <div class="wp-chatbot-toggle-image">
        <img src="<?php echo esc_url(get_option('wp_chatbot_image', plugins_url('assets/images/default-avatar.png', dirname(__FILE__)))); ?>" alt="Chat">
    </div>
    <span class="wp-chatbot-toggle-text">Chat</span>
</div>

<div class="wp-chatbot-container wp-chatbot-hidden">
    <div class="wp-chatbot-header">
        <h3><?php echo esc_html(get_option('wp_chatbot_title', 'AI Chat Assistant')); ?></h3>
    </div>
    <div class="wp-chatbot-messages">
        <div class="wp-chatbot-message wp-chatbot-bot-message">
            Hello
        </div>
    </div>
    <form id="wp-chatbot-form" class="wp-chatbot-input">
        <input type="text" id="wp-chatbot-input" placeholder="Type your message...">
        <button type="submit">Send</button>
    </form>
</div>
