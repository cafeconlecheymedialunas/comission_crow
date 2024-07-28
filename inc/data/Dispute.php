<?php
class Dispute
{

    private function __construct()
    {
    }
    private static $instance = null;
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function save_message()
    {
        check_ajax_referer('save_message_nonce', 'security');

        $post_id = intval($_POST['post_id']);
        $from = intval($_POST['from']);
        $to = intval($_POST['to']);
        $message = wp_kses_post($_POST['message']);

        $messages = carbon_get_post_meta($post_id, 'messages');
        $messages[] = [
            'from' => $from,
            'to' => $to,
            'message' => $message,
        ];

        carbon_set_post_meta($post_id, 'messages', $messages);

        wp_send_json_success(['messages' => $messages]);
    }
}
