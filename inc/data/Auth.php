<?php
// Shortcode para mostrar el formulario de registro

class Auth
{
    public function __construct()
    {
        add_shortcode('register_form', [$this,'registration_form']);
        add_shortcode('login_form', [$this,'login_form']);
        add_shortcode('password_reset_form', [$this,'password_reset_form']);
        add_shortcode('new_password_form', [$this,'new_password_form']);
        add_action('wp_ajax_nopriv_register_user', [$this,'register_user']);
        add_action('wp_ajax_nopriv_login_user', [$this,'login_user']);
        add_action('wp_ajax_nopriv_reset_password', [$this,'reset_password']);
        add_action('wp_ajax_nopriv_set_new_password', [$this,'set_new_password']);
    }

    public function registration_form()
    {
        if (!is_user_logged_in()) {

            $registration_enabled = get_option('users_can_register');
            if ($registration_enabled) {
               
               
                ob_start();
                $role = isset($_GET['role']) && !empty($_GET['role']) ? sanitize_text_field($_GET['role']):"";

                $title = ($role == "commercial_agent")?"Register as a Commercial Agent":"Register your company";
                
                $template_path = 'templates/dashboard/form-register.php';
              
                require locate_template($template_path);
                return ob_get_clean();
            } else {
                $output = __('User registration is not enabled');
            }
            return $output;
        }
    }

    public function login_form()
    {
        if (!is_user_logged_in()) {

            $output = $this->login_form_fields();
        } else {
            $output = 'Already Logged-In <a id="logout" href="' . wp_logout_url(get_permalink()) . '" title="Logout">Logout</a>';
        }
        return $output;
    }

    public function password_reset_form()
    {
        if (!is_user_logged_in()) {

            $output = $this->password_reset_form_fields();
            return $output;
        }
    }
    public function new_password_form()
    {
        if (!is_user_logged_in() && isset($_GET['action']) && $_GET['action'] == 'new_password' && isset($_GET['key']) && isset($_GET['login'])) {
            global $load_css;
            $load_css = true;
            $output = $this->new_password_form_fields();
            return $output;
        }
    }

    public function registration_form_fields($role)
    {
       
    }
    public function login_form_fields()
    {
        ob_start();
        $template_path = 'templates/dashboard/form-login.php';
        
        require locate_template($template_path);
        
        return ob_get_clean();
    }
    
    public function password_reset_form_fields()
    {
        ob_start();
        $template_path = 'templates/dashboard/form-reset-password.php';
     
        require locate_template($template_path);
        
        
        return ob_get_clean();
    }
    public function new_password_form_fields()
    {
        ob_start();
        $template_path = 'templates/dashboard/form-new-password.php';
     
        require locate_template($template_path);
        
        return ob_get_clean();
    }
    public function register_user()
    {
        check_ajax_referer('register-nonce', 'security');
    
        $errors = [];
        $field_errors = []; // Array to store errors for each field
    
        // Sanitize and validate form data
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['user_email']);
        $password = sanitize_text_field($_POST['user_pass']);
        $password_confirm = sanitize_text_field($_POST['user_pass_confirm']);
        $company_name = sanitize_text_field($_POST['company_name']);
        $role = sanitize_text_field($_POST['role']);
    
        // Validate first name
        if (empty($first_name)) {
            $field_errors['first_name'][] = __('Please enter your first name.');
        }
    
        // Validate last name
        if (empty($last_name)) {
            $field_errors['last_name'][] = __('Please enter your last name.');
        }
    
        // Validate email
        if (empty($email)) {
            $field_errors['user_email'][] = __('Please enter your email.');
        } elseif (!is_email($email)) {
            $field_errors['user_email'][] = __('Invalid email address.');
        } elseif (email_exists($email)) {
            $field_errors['user_email'][] = __('Email already exists. Please choose another one.');
        }
    
        // Validate password
        if (empty($password)) {
            $field_errors['user_pass'][] = __('Please enter a password.');
        }
        if (strlen($password) < 8) {
            $field_errors['user_pass'][] = __('Password must be at least 8 characters long.');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $field_errors['user_pass'][] = __('Password must contain at least one uppercase letter.');
        }
        if (!preg_match('/[a-z]/', $password)) {
            $field_errors['user_pass'][] = __('Password must contain at least one lowercase letter.');
        }
        if (!preg_match('/[0-9]/', $password)) {
            $field_errors['user_pass'][] = __('Password must contain at least one number.');
        }
        if (!preg_match('/[\W_]/', $password)) {
            $field_errors['user_pass'][] = __('Password must contain at least one special character.');
        }
    
        if (!empty($password_confirm) && $password !== $password_confirm) {
            $field_errors['user_pass_confirm'][] = __('Passwords do not match.');
        }
    
        // Validate company name if role is 'company'
        if ($role === 'company') {
            if (empty($company_name)) {
                $field_errors['company_name'][] = __('Please enter your company name.');
            } else {
                // Check if a company with the same name already exists
                $company = new WP_Query([
                    'post_type' => 'company',
                    'title' => $company_name,
                    'posts_per_page' => 1,
                ]);
    
                if ($company->have_posts()) {
                    $field_errors['company_name'][] = __('A company with this name already exists.');
                }
            }
        }
    
        // Check if there are any field-specific errors
        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            die();
        }
    
        // Create the user
        $user_id = wp_insert_user([
            'user_login' => $email,
            'user_email' => $email,
            'user_pass' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role' => $role,
            "display_name" => $first_name . " " . $last_name
        ]);
    
        if (is_wp_error($user_id)) {
            wp_send_json_error(['general' => ['There was an error creating the user.']]);
            die();
        }
    
        // Create post type based on role
        $post_id = 0;
        if ($role === 'company') {
            if (!empty($company_name)) {
                $post_id = wp_insert_post([
                    'post_title' => $company_name,
                    'post_type' => 'company',
                    'post_status' => 'publish',
                    'post_author' => $user_id,
                ]);
    
                // Verify the post creation
                if (is_wp_error($post_id)) {
                    wp_send_json_error(['general' => ['There was an error creating the company post.']]);
                    die();
                }
    
                carbon_set_post_meta($post_id, 'user_id', $user_id);
                carbon_set_post_meta($post_id, 'company_name', $company_name);
            } else {
                wp_send_json_error(['general' => ['Company name cannot be empty.']]);
                die();
            }
        } elseif ($role === 'commercial_agent') {
            $post_id = wp_insert_post([
                'post_title' => $first_name . ' ' . $last_name,
                'post_type' => 'commercial_agent',
                'post_status' => 'publish',
                'post_author' => $user_id,
            ]);
    
            // Verify the post creation
            if (is_wp_error($post_id)) {
                wp_send_json_error(['general' => ['There was an error creating the commercial agent post.']]);
                die();
            }
    
            carbon_set_post_meta($post_id, 'user_id', $user_id);
        }
    
        // Return the user information as JSON response
        $user = get_user_by('id', $user_id);
    
        wp_send_json_success($user);
        die();
    }
    
    


    public function login_user()
    {
        check_ajax_referer('login-nonce', 'security');
    
        $errors = [];
        $field_errors = []; // Array to store errors for each field
    
        // Sanitize form data
        $user_login = sanitize_text_field($_POST['user_login']);
        $user_password = sanitize_text_field($_POST['user_pass']);
        $remember = (isset($_POST['remember_me']) && $_POST['remember_me'] == 'true') ? true : false;
    
        // Validate username (email)
        if (empty($user_login)) {
            $field_errors['user_login'][] = __('Please enter your email address.');
        } elseif (!is_email($user_login)) {
            $field_errors['user_login'][] = __('Invalid email address.');
        }
    
        // Validate password
        if (empty($user_password)) {
            $field_errors['user_pass'][] = __('Please enter your password.');
        }
    
        // Check for any field-specific errors
        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            die();
        }
    
        // Attempt to sign in the user
        $login_data = [
            'user_login' => $user_login,
            'user_password' => $user_password,
            'remember' => $remember,
        ];
    
        $user = wp_signon($login_data, false);
    
        if (is_wp_error($user)) {
            wp_send_json_error(['general' => [strip_tags($user->get_error_message())]]);
        } else {
            wp_send_json_success($user);
        }
    
        die();
    }
    
    public function reset_password()
    {
        check_ajax_referer('reset-nonce', 'security');
    
        $errors = [];
        $field_errors = []; // Array para almacenar errores específicos de cada campo
    
        // Sanitize form data
        $user_email = sanitize_email($_POST['user_email']);
    
        // Validar el campo del email
        if (empty($user_email)) {
            $field_errors['user_email'][] = __('Please enter your email.');
        } elseif (!is_email($user_email)) {
            $field_errors['user_email'][] = __('Invalid email address.');
        } else {
            // Verificar si el usuario existe
            $user = get_user_by('email', $user_email);
            if (!$user) {
                $field_errors['user_email'][] = __('Email not found.');
            }
        }
    
        // Verificar si hay errores específicos de campos
        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            die();
        }
    
        // Generar la clave de restablecimiento de contraseña
        $reset_key = get_password_reset_key($user);
    
        if (is_wp_error($reset_key)) {
            wp_send_json_error(['general' => [$reset_key->get_error_message()]]);
            die();
        }
    
        // Enviar el correo electrónico de restablecimiento de contraseña
        $reset_link = home_url("/auth?action=new_password&key=$reset_key&login=" . rawurlencode($user->user_login));
        $email_subject = __('Password Reset Request');
        $email_content = __('Click on the following link to reset your password:') . "\r\n\r\n" . $reset_link;
    
        $sent = wp_mail($user_email, $email_subject, $email_content);
    
        if ($sent) {
            wp_send_json_success(__('Password reset email sent.'));
        } else {
            wp_send_json_error(['general' => [__('Failed to send password reset email.')]]);
        }
    
        die();
    }
    
    public function set_new_password()
    {
        check_ajax_referer('new-password-nonce', 'security');
    
        $errors = [];
        $field_errors = []; // Array para almacenar errores específicos de cada campo
    
        // Obtener y sanitizar los datos del formulario
        $reset_key = sanitize_text_field($_POST['reset_key']);
        $reset_login = sanitize_text_field($_POST['reset_login']);
        $new_password = sanitize_text_field($_POST['new_password']);
        $confirm_password = sanitize_text_field($_POST['confirm_password']);
    
        // Validar nueva contraseña
        if (empty($new_password)) {
            $field_errors['new_password'][] = __('Please enter a password.');
        } elseif (strlen($new_password) < 8) {
            $field_errors['new_password'][] = __('Password should be at least 8 characters long.');
        } elseif (!preg_match('/[A-Z]/', $new_password)) {
            $field_errors['new_password'][] = __('Password must contain at least one uppercase letter.');
        } elseif (!preg_match('/[a-z]/', $new_password)) {
            $field_errors['new_password'][] = __('Password must contain at least one lowercase letter.');
        } elseif (!preg_match('/[0-9]/', $new_password)) {
            $field_errors['new_password'][] = __('Password must contain at least one number.');
        } elseif (!preg_match('/[\W_]/', $new_password)) {
            $field_errors['new_password'][] = __('Password must contain at least one special character.');
        } elseif ($new_password !== $confirm_password) {
            $field_errors['confirm_password'][] = __('Passwords do not match.');
        }
    
        // Verificar si hay errores específicos de campos
        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            die();
        }
    
        // Verificar la clave de restablecimiento de contraseña
        $user = check_password_reset_key($reset_key, $reset_login);
    
        if (is_wp_error($user)) {
            wp_send_json_error(['general' => [$user->get_error_message()]]);
            die();
        }
    
        // Restablecer la contraseña
        $reset = reset_password($user, $new_password);
    
        if (is_wp_error($reset)) {
            wp_send_json_error(['general' => [$reset->get_error_message()]]);
            die();
        }
    
        wp_send_json_success(__('Password reset successfully.'));
    
        die();
    }
}






new Auth();
// Iniciar sesión por AJAX
// Iniciar sesión por AJAX
