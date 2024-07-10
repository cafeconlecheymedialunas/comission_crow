<?php
// Shortcode para mostrar el formulario de registro
function kamerpower_registration_form() {
    if(!is_user_logged_in()) {
        global $kamerpower_load_css;
        $kamerpower_load_css = true;

        $registration_enabled = get_option('users_can_register');
        if($registration_enabled) {
            $output = kamerpower_registration_form_fields();
        } else {
            $output = __('User registration is not enabled');
        }
        return $output;
    }
}
add_shortcode('register_form', 'kamerpower_registration_form');


// Shortcode para mostrar el formulario de inicio de sesión
function kamerpower_login_form() {
    if(!is_user_logged_in()) {
        global $kamerpower_load_css;
        $kamerpower_load_css = true;
        $output = kamerpower_login_form_fields();
    } else {
        $output = 'Already Logged-In <a id="kamerpower_logout" href="'. wp_logout_url( get_permalink() ) .'" title="Logout">Logout</a>';
    }
    return $output;
}
add_shortcode('login_form', 'kamerpower_login_form');

function kamerpower_password_reset_form() {
    if (!is_user_logged_in()) {
        global $kamerpower_load_css;
        $kamerpower_load_css = true;
        $output = kamerpower_password_reset_form_fields();
        return $output;
    }
}
add_shortcode('password_reset_form', 'kamerpower_password_reset_form');

function kamerpower_new_password_form() {
    if (!is_user_logged_in() && isset($_GET['action']) && $_GET['action'] == 'reset_password' && isset($_GET['key']) && isset($_GET['login'])) {
        global $kamerpower_load_css;
        $kamerpower_load_css = true;
        $output = kamerpower_new_password_form_fields();
        return $output;
    }
}
add_shortcode('new_password_form', 'kamerpower_new_password_form');
// Campos del formulario de registro
function kamerpower_registration_form_fields() {
    ob_start(); ?>
    <h3 class="kamerpower_header"><?php _e('Register New Account'); ?></h3>
    <div id="kamerpower_registration_errors"></div>
    <form id="kamerpower_registration_form" class="kamerpower_form">
        <fieldset>
            <p>
                <label for="kamerpower_user_login"><?php _e('Username'); ?></label>
                <input name="kamerpower_user_login" id="kamerpower_user_login" class="required" type="text"/>
            </p>
            <p>
                <label for="kamerpower_user_email"><?php _e('Email'); ?></label>
                <input name="kamerpower_user_email" id="kamerpower_user_email" class="required" type="email"/>
            </p>
            <p>
                <label for="kamerpower_user_first"><?php _e('First Name'); ?></label>
                <input name="kamerpower_user_first" id="kamerpower_user_first" class="required" type="text"/>
            </p>
            <p>
                <label for="kamerpower_user_last"><?php _e('Last Name'); ?></label>
                <input name="kamerpower_user_last" id="kamerpower_user_last" class="required" type="text"/>
            </p>
            <p>
                <label for="password"><?php _e('Password'); ?></label>
                <input name="kamerpower_user_pass" id="password" class="required" type="password"/>
            </p>
            <p>
                <label for="password_again"><?php _e('Password Again'); ?></label>
                <input name="kamerpower_user_pass_confirm" id="password_again" class="required" type="password"/>
            </p>
            <p>
                <input type="hidden" name="kamerpower_register_nonce" value="<?php echo wp_create_nonce('kamerpower-register-nonce'); ?>"/>
                <input type="submit" value="<?php _e('Register Your Account'); ?>"/>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}

// Campos del formulario de inicio de sesión
function kamerpower_login_form_fields() {
    ob_start(); ?>
    <h3 class="kamerpower_header"><?php _e('Login'); ?></h3>
    <div id="kamerpower_login_errors"></div>
    <form id="kamerpower_login_form" class="kamerpower_form">
        <fieldset>
            <p>
                <label for="kamerpower_user_login">Username</label>
                <input name="kamerpower_user_login" id="kamerpower_user_login" class="required" type="text"/>
            </p>
            <p>
                <label for="kamerpower_user_pass">Password</label>
                <input name="kamerpower_user_pass" id="kamerpower_user_pass" class="required" type="password"/>
            </p>
            <p>
                <input type="hidden" name="kamerpower_login_nonce" value="<?php echo wp_create_nonce('kamerpower-login-nonce'); ?>"/>
                <input id="kamerpower_login_submit" type="submit" value="Login"/>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}


// Campos del formulario de recuperación de contraseña
function kamerpower_password_reset_form_fields() {
    ob_start(); ?>
    <h3 class="kamerpower_header"><?php _e('Reset Password'); ?></h3>
    <div id="kamerpower_reset_errors"></div>
    <form id="kamerpower_reset_form" class="kamerpower_form">
        <fieldset>
            <p>
                <label for="kamerpower_user_email"><?php _e('Email'); ?></label>
                <input name="kamerpower_user_email" id="kamerpower_user_email" class="required" type="email"/>
            </p>
            <p>
                <input type="hidden" name="kamerpower_reset_nonce" value="<?php echo wp_create_nonce('kamerpower-reset-nonce'); ?>"/>
                <input type="submit" value="<?php _e('Reset Password'); ?>"/>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}


// Campos del formulario de restablecimiento de contraseña
function kamerpower_new_password_form_fields() {
    ob_start(); ?>
    <h3 class="kamerpower_header"><?php _e('Set a New Password'); ?></h3>
    <div id="kamerpower_new_password_errors"></div>
    <form id="kamerpower_new_password_form" class="kamerpower_form">
        <fieldset>
            <p>
                <label for="new_password"><?php _e('New Password'); ?></label>
                <input name="new_password" id="new_password" class="required" type="password"/>
            </p>
            <p>
                <label for="confirm_password"><?php _e('Confirm Password'); ?></label>
                <input name="confirm_password" id="confirm_password" class="required" type="password"/>
            </p>
            <p>
                <input type="hidden" name="reset_key" value="<?php echo esc_attr($_GET['key']); ?>"/>
                <input type="hidden" name="reset_login" value="<?php echo esc_attr($_GET['login']); ?>"/>
                <input type="hidden" name="kamerpower_new_password_nonce" value="<?php echo wp_create_nonce('kamerpower-new-password-nonce'); ?>"/>
                <input type="submit" value="<?php _e('Reset Password'); ?>"/>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}

// Manejo de registro por AJAX
function kamerpower_register_user() {
    check_ajax_referer('kamerpower-register-nonce', 'kamerpower_register_nonce');

    $user_login = sanitize_text_field($_POST['kamerpower_user_login']);
    $user_email = sanitize_email($_POST['kamerpower_user_email']);
    $user_first = sanitize_text_field($_POST['kamerpower_user_first']);
    $user_last = sanitize_text_field($_POST['kamerpower_user_last']);
    $user_pass = $_POST['kamerpower_user_pass'];
    $pass_confirm = $_POST['kamerpower_user_pass_confirm'];

    $errors = new WP_Error();

    if(username_exists($user_login)) {
        $errors->add('username_unavailable', __('Username already taken'));
    }
    if(!validate_username($user_login)) {
        $errors->add('username_invalid', __('Invalid username'));
    }
    if($user_login == '') {
        $errors->add('username_empty', __('Please enter a username'));
    }
    if(!is_email($user_email)) {
        $errors->add('email_invalid', __('Invalid email'));
    }
    if(email_exists($user_email)) {
        $errors->add('email_used', __('Email already registered'));
    }
    if($user_pass == '') {
        $errors->add('password_empty', __('Please enter a password'));
    }
    if($user_pass != $pass_confirm) {
        $errors->add('password_mismatch', __('Passwords do not match'));
    }

    if(empty($errors->errors)) {
        $new_user_id = wp_insert_user(array(
            'user_login'    => $user_login,
            'user_pass'     => $user_pass,
            'user_email'    => $user_email,
            'first_name'    => $user_first,
            'last_name'     => $user_last,
            'role'          => 'subscriber'
        ));

        if(!is_wp_error($new_user_id)) {
            wp_new_user_notification($new_user_id);
            wp_set_current_user($new_user_id);
            wp_set_auth_cookie($new_user_id);
            echo json_encode(array('registered' => true));
        } else {
            $errors->add('registration_failed', __('Registration failed'));
        }
    }

    if(!empty($errors->errors)) {
        $error_messages = '';
        foreach($errors->get_error_messages() as $error) {
            $error_messages .= '<p>' . $error . '</p>';
        }
        echo json_encode(array('registered' => false, 'message' => $error_messages));
    }

    wp_die();
}
add_action('wp_ajax_nopriv_kamerpower_register_user', 'kamerpower_register_user');


// Manejo de inicio de sesión por AJAX
function kamerpower_login_user() {
    check_ajax_referer('kamerpower-login-nonce', 'kamerpower_login_nonce');

    $user_login = sanitize_text_field($_POST['kamerpower_user_login']);
    $user_pass = $_POST['kamerpower_user_pass'];

    $creds = array(
        'user_login' => $user_login,
        'user_password' => $user_pass,
        'remember' => true
    );

    $user = wp_signon($creds, false);

    if(is_wp_error($user)) {
        echo json_encode(array('loggedin' => false, 'message' => __('Invalid login credentials')));
    } else {
        echo json_encode(array('loggedin' => true, 'message' => __('Login successful')));
    }

    wp_die();
}
add_action('wp_ajax_nopriv_kamerpower_login_user', 'kamerpower_login_user');


// Manejo de recuperación de contraseña por AJAX
function kamerpower_reset_password() {
    check_ajax_referer('kamerpower-reset-nonce', 'kamerpower_reset_nonce');

    $user_email = sanitize_email($_POST['kamerpower_user_email']);

    if (!is_email($user_email)) {
        echo json_encode(array('reset' => false, 'message' => __('Invalid email address')));
        wp_die();
    }

    $user = get_user_by('email', $user_email);
    if (!$user) {
        echo json_encode(array('reset' => false, 'message' => __('Email address not found')));
        wp_die();
    }

    $reset_key = get_password_reset_key($user);
    if (is_wp_error($reset_key)) {
        echo json_encode(array('reset' => false, 'message' => __('An error occurred, please try again')));
        wp_die();
    }

    $reset_url = add_query_arg(array(
        'action' => 'reset_password',
        'key' => $reset_key,
        'login' => rawurlencode($user->user_login)
    ), site_url('/'));

    $message = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
    $message .= network_site_url() . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
    $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
    $message .= '<' . $reset_url . ">\r\n";

    if (wp_mail($user_email, __('Password Reset'), $message)) {
        echo json_encode(array('reset' => true, 'message' => __('Check your email for the confirmation link')));
    } else {
        echo json_encode(array('reset' => false, 'message' => __('Email could not be sent, please try again')));
    }

    wp_die();
}
add_action('wp_ajax_nopriv_kamerpower_reset_password', 'kamerpower_reset_password');

// Manejo del restablecimiento de contraseña por AJAX
function kamerpower_set_new_password() {
    check_ajax_referer('kamerpower-new-password-nonce', 'kamerpower_new_password_nonce');

    $reset_key = sanitize_text_field($_POST['reset_key']);
    $reset_login = sanitize_text_field($_POST['reset_login']);
    $new_password = sanitize_text_field($_POST['new_password']);
    $confirm_password = sanitize_text_field($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        echo json_encode(array('reset' => false, 'message' => __('Passwords do not match')));
        wp_die();
    }

    $user = check_password_reset_key($reset_key, $reset_login);

    if (is_wp_error($user)) {
        echo json_encode(array('reset' => false, 'message' => __('Invalid reset key')));
        wp_die();
    }

    reset_password($user, $new_password);

    echo json_encode(array('reset' => true, 'message' => __('Password has been reset')));
    wp_die();
}
add_action('wp_ajax_nopriv_kamerpower_set_new_password', 'kamerpower_set_new_password');



function enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('authjs', get_template_directory_uri() . '/assets/auth.js', array('jquery'), null, true);

    wp_localize_script('authjs', 'authjs_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');