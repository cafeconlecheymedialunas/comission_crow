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
        
        check_ajax_referer('update-userdata', 'security');
    
        $user_id = get_current_user_id();
    
        // Sanitiza y valida los datos
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $user_email = sanitize_email($_POST['user_email']);
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';

        $errors = [];

        // Validaciones
        if (empty($first_name)) {
            $errors['first_name'] = __('First name is required.');
        }
        if (empty($last_name)) {
            $errors['last_name'] = __('Last name is required.');
        }
        if (empty($user_email)) {
            $errors['user_email'] = __('Email is required.');
        } elseif (!is_email($user_email)) {
            $errors['user_email'] = __('This email has an incorrect format.');
        }
        
        if (!empty($password)) {
            if (strlen($password) < 8) {
                $errors['password'][] = __('Password must be at least 8 characters long.');
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors['password'][] = __('Password must contain at least one uppercase letter.');
            }
            if (!preg_match('/[a-z]/', $password)) {
                $errors['password'][] = __('Password must contain at least one lowercase letter.');
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors['password'][] = __('Password must contain at least one number.');
            }
            if (!preg_match('/[\W_]/', $password)) {
                $errors['password'][] = __('Password must contain at least one special character.');
            }
        }

        // Si hay errores, envía respuesta de error
        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            die();
        }

        // Actualiza los datos del usuario
        $user_data = [
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $user_email,
        ];

        if (!empty($password)) {
            $user_data['user_pass'] = $password;
        }

        $updated_user_id = wp_update_user($user_data);

        if (is_wp_error($updated_user_id)) {
            wp_send_json_error(['message' => __('Error updating user: ') . $updated_user_id->get_error_message()]);
        }

        $user = get_user_by("ID", $updated_user_id);

        wp_send_json_success($user);
        
    }
    

}
