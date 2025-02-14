<?php
/*
Plugin Name: WP AI Chatbot
Plugin URI: https://github.com/YOUR-USERNAME/wp-chatbot
Description: A WordPress chatbot plugin powered by Google Gemini
Version: 1.0.0
Author: Your Name
Author URI: https://github.com/YOUR-USERNAME
GitHub Plugin URI: YOUR-USERNAME/wp-chatbot
Primary Branch: main
*/

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-ai-models.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-plugin-updater.php';

class WP_Chatbot {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('rest_api_init', array($this, 'register_endpoints'));
        add_action('wp_footer', array($this, 'add_chat_widget'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('wp-chatbot', plugins_url('assets/css/chat.css', __FILE__));
        wp_enqueue_script('wp-chatbot', plugins_url('assets/js/chat.js', __FILE__), array('jquery'), '1.0', true);
        
        // Add custom CSS for color customization
        $primary_color = get_option('wp_chatbot_primary_color', '#007bff');
        $custom_css = "
            .wp-chatbot-header { background-color: {$primary_color}; }
            .wp-chatbot-user-message { background-color: {$primary_color}; }
            .wp-chatbot-input button { background-color: {$primary_color}; }
            .wp-chatbot-input button:hover { background-color: " . $this->adjust_brightness($primary_color, -20) . "; }
            .wp-chatbot-input input:focus { border-color: {$primary_color}; }
        ";
        wp_add_inline_style('wp-chatbot', $custom_css);

        wp_localize_script('wp-chatbot', 'wpChatbot', array(
            'ajaxUrl' => rest_url('wp-chatbot/v1/chat'),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }

    private function adjust_brightness($hex, $steps) {
        // Convert hex to rgb
        $rgb = array_map('hexdec', str_split(ltrim($hex, '#'), 2));
        
        // Adjust brightness
        foreach ($rgb as &$color) {
            $color = max(0, min(255, $color + $steps));
        }
        
        // Convert back to hex
        return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }

    public function register_endpoints() {
        register_rest_route('wp-chatbot/v1', '/chat', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_chat_request'),
            'permission_callback' => '__return_true'
        ));
    }

    public function handle_chat_request($request) {
        $message = $request->get_param('message');
        
        $response = AI_Models::process_chat($message);
        
        if (is_wp_error($response)) {
            return new WP_Error('chat_error', $response->get_error_message());
        }
        
        return rest_ensure_response(array('response' => $response));
    }

    public function add_chat_widget() {
        include plugin_dir_path(__FILE__) . 'templates/chat-widget.php';
    }

    public function add_admin_menu() {
        add_menu_page(
            'AI Chatbot Settings',
            'AI Chatbot',
            'manage_options',
            'wp-chatbot-settings',
            array($this, 'render_settings_page'),
            'dashicons-format-chat'
        );
    }

    public function register_settings() {
        register_setting('wp-chatbot-settings-group', 'wp_chatbot_image');
        register_setting('wp-chatbot-settings-group', 'wp_chatbot_gemini_key');
        register_setting('wp-chatbot-settings-group', 'wp_chatbot_title', array(
            'default' => 'AI Chat Assistant'
        ));
        register_setting('wp-chatbot-settings-group', 'wp_chatbot_primary_color', array(
            'default' => '#007bff'
        ));

        add_settings_section(
            'wp_chatbot_main_section',
            'Chatbot Settings',
            null,
            'wp-chatbot-settings'
        );

        add_settings_field(
            'wp_chatbot_title',
            'Chat Window Title',
            array($this, 'render_title_field'),
            'wp-chatbot-settings',
            'wp_chatbot_main_section'
        );

        add_settings_field(
            'wp_chatbot_primary_color',
            'Primary Color',
            array($this, 'render_color_field'),
            'wp-chatbot-settings',
            'wp_chatbot_main_section'
        );

        add_settings_field(
            'wp_chatbot_image',
            'Chat Button Image',
            array($this, 'render_image_field'),
            'wp-chatbot-settings',
            'wp_chatbot_main_section'
        );

        add_settings_field(
            'wp_chatbot_gemini_key',
            'Google Gemini API Key',
            array($this, 'render_api_key_field'),
            'wp-chatbot-settings',
            'wp_chatbot_main_section'
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h2>AI Chatbot Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('wp-chatbot-settings-group');
                do_settings_sections('wp-chatbot-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_title_field() {
        $title = get_option('wp_chatbot_title', 'AI Chat Assistant');
        ?>
        <input type="text" name="wp_chatbot_title" value="<?php echo esc_attr($title); ?>" class="regular-text">
        <p class="description">Enter the title to be displayed in the chat window header</p>
        <?php
    }

    public function render_color_field() {
        $color = get_option('wp_chatbot_primary_color', '#007bff');
        ?>
        <input type="color" name="wp_chatbot_primary_color" value="<?php echo esc_attr($color); ?>">
        <input type="text" value="<?php echo esc_attr($color); ?>" class="regular-text" readonly>
        <p class="description">Choose the primary color for the chat interface</p>
        <script>
        jQuery(document).ready(function($) {
            $('input[type="color"]').on('input', function() {
                $(this).next('input[type="text"]').val($(this).val());
            });
        });
        </script>
        <?php
    }

    public function render_image_field() {
        $image = get_option('wp_chatbot_image');
        ?>
        <div>
            <input type="text" name="wp_chatbot_image" value="<?php echo esc_attr($image); ?>" class="regular-text">
            <input type="button" class="button button-secondary" value="Upload Image" id="upload-btn">
            <p class="description">Upload or enter the URL of the image to be used for the chat button (recommended size: 60x60 pixels)</p>
            <?php if ($image): ?>
                <div style="margin-top: 10px;">
                    <img src="<?php echo esc_url($image); ?>" style="max-width: 60px; height: auto;">
                </div>
            <?php endif; ?>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('#upload-btn').click(function(e) {
                e.preventDefault();
                var image = wp.media({ 
                    title: 'Upload Image',
                    multiple: false
                }).open()
                .on('select', function(e){
                    var uploaded_image = image.state().get('selection').first();
                    var image_url = uploaded_image.toJSON().url;
                    $('input[name="wp_chatbot_image"]').val(image_url);
                });
            });
        });
        </script>
        <?php
    }

    public function render_api_key_field() {
        $api_key = get_option('wp_chatbot_gemini_key');
        ?>
        <input type="password" 
               name="wp_chatbot_gemini_key" 
               value="<?php echo esc_attr($api_key); ?>" 
               class="regular-text">
        <p class="description">Enter your Google Gemini API key</p>
        <?php
    }
}

// Initialize the plugin
add_action('plugins_loaded', array('WP_Chatbot', 'get_instance'));

// Add media uploader scripts to admin
function wp_chatbot_admin_scripts() {
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'wp_chatbot_admin_scripts');
