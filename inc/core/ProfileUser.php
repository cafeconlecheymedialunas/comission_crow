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
    }

    public function is_company()
    {

    }
    public function is_commercial_agent()
    {

    }

    public function update_user_data()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Debes estar registrado para editar tu perfil.']);
        }

        $user_id = get_current_user_id();

        // Validar y sanitizar los datos del formulario
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $user_email = sanitize_email($_POST['user_email']);
        $password = $_POST['password'];

        // Validar la seguridad de la contraseña si se ha proporcionado
        if (empty($first_name)) {
            wp_send_json_error('First name is required.');
        }
        if (empty($last_name)) {
            wp_send_json_error('Last name is required.');
        }
        if (empty($user_email)) {
            wp_send_json_error('Email is required');
        }
        if (!empty($user_email) && !is_email($user_email)) {
            wp_send_json_error('This email has not a correct format');
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                wp_send_json_error(['message' => 'La contraseña debe tener al menos 8 caracteres.']);
            }
            if (!preg_match('/[A-Z]/', $password)) {
                wp_send_json_error(['message' => 'La contraseña debe contener al menos una letra mayúscula.']);
            }
            if (!preg_match('/[a-z]/', $password)) {
                wp_send_json_error(['message' => 'La contraseña debe contener al menos una letra minúscula.']);
            }
            if (!preg_match('/[0-9]/', $password)) {
                wp_send_json_error(['message' => 'La contraseña debe contener al menos un número.']);
            }
            if (!preg_match('/[\W_]/', $password)) {
                wp_send_json_error(['message' => 'La contraseña debe contener al menos un carácter especial.']);
            }
        }

        $user_data = [
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $user_email,
        ];

        // Hashear la contraseña si se ha proporcionado
        if (!empty($password)) {
            $user_data['user_pass'] = wp_hash_password($password);
        }

        // Actualizar la información del usuario
        $user_id = wp_update_user($user_data);

        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => 'Error updating user: ' . $user_id->get_error_message()]);
        } else {
            wp_send_json_success(['message' => 'Profile updated successfully.']);
        }
    }
}
