<?php
class ProfileUser
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

    public function is_company()
    {

    }
    public function is_commercial_agent()
    {

    }

    public function update_user_data()
    {
        // Verifica el nonce de seguridad
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'update-userdata')) {
            wp_send_json_error(['message' => 'Invalid security token.']);
        }
    
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in to update your profile.']);
        }
    
        $user_id = get_current_user_id();
        
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $user_email = sanitize_email($_POST['user_email']);
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
    
       
        if (empty($first_name)) {
            wp_send_json_error(['message' => 'First name is required.']);
        }
        if (empty($last_name)) {
            wp_send_json_error(['message' => 'Last name is required.']);
        }
        if (empty($user_email)) {
            wp_send_json_error(['message' => 'Email is required.']);
        }
        if (!is_email($user_email)) {
            wp_send_json_error(['message' => 'This email has an incorrect format.']);
        }
    
        if (!empty($password)) {
            if (strlen($password) < 8) {
                wp_send_json_error(['message' => 'Password must be at least 8 characters long.']);
            }
            if (!preg_match('/[A-Z]/', $password)) {
                wp_send_json_error(['message' => 'Password must contain at least one uppercase letter.']);
            }
            if (!preg_match('/[a-z]/', $password)) {
                wp_send_json_error(['message' => 'Password must contain at least one lowercase letter.']);
            }
            if (!preg_match('/[0-9]/', $password)) {
                wp_send_json_error(['message' => 'Password must contain at least one number.']);
            }
            if (!preg_match('/[\W_]/', $password)) {
                wp_send_json_error(['message' => 'Password must contain at least one special character.']);
            }
        }
    
        $user_data = [
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $user_email,
        ];
    
       
        if (!empty($password)) {
            $user_data['user_pass'] = $password;
        }
    
        $user_id = wp_update_user($user_data);

        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => 'Error updating user: ' . $user_id->get_error_message()]);
        }

        $user = get_user_by("ID", $user_id);
    
        wp_send_json_success($user);
        
    }
    

}
