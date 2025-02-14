<?php
class AI_Models {
    public static function process_chat($message) {
        $api_key = get_option('wp_chatbot_gemini_key');

        if (!$api_key) {
            return new WP_Error('missing_key', 'API key not configured');
        }

        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . $api_key, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'contents' => [
                    ['parts' => [['text' => $message]]]
                ]
            ])
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not process your request.';
    }
}
