<?php
/*
// Shortcode para mostrar el formulario de registro
function kamerpower_registration_form()
{
    if (!is_user_logged_in()) {
     

        $registration_enabled = get_option('users_can_register');
        if ($registration_enabled) {
            $role = isset($_GET['role']) ? sanitize_text_field($_GET['role']) : 'agent';
            $output = kamerpower_registration_form_fields($role);
        } else {
            $output = __('User registration is not enabled');
        }
        return $output;
    }
}
add_shortcode('register_form', 'kamerpower_registration_form');

// Shortcode para mostrar el formulario de inicio de sesión
function kamerpower_login_form()
{
    if (!is_user_logged_in()) {

        $output = kamerpower_login_form_fields();
    } else {
        $output = 'Already Logged-In <a id="kamerpower_logout" href="' . wp_logout_url(get_permalink()) . '" title="Logout">Logout</a>';
    }
    return $output;
}
add_shortcode('login_form', 'kamerpower_login_form');

// Shortcode para mostrar el formulario de recuperación de contraseña
function kamerpower_password_reset_form()
{
    if (!is_user_logged_in()) {
    
        $output = kamerpower_password_reset_form_fields();
        return $output;
    }
}
add_shortcode('password_reset_form', 'kamerpower_password_reset_form');

// Shortcode para mostrar el formulario de restablecimiento de contraseña
function kamerpower_new_password_form()
{
    if (!is_user_logged_in() && isset($_GET['action']) && $_GET['action'] == 'new_password' && isset($_GET['key']) && isset($_GET['login'])) {
        global $kamerpower_load_css;
        $kamerpower_load_css = true;
        $output = kamerpower_new_password_form_fields();
        return $output;
    }
}
add_shortcode('new_password_form', 'kamerpower_new_password_form');

// Campos del formulario de registro
function kamerpower_registration_form_fields($role)
{
    $title = $role === 'business' ? __('Register as a Company') : __('Register as a Commercial Agent');
    ob_start(); ?>
    <h1 class="site-title"><?php echo $title; ?></h1>
    <div id="kamerpower_registration_errors"></div>
    <form id="kamerpower_registration_form">
        <div class="row  gx-1">
            <div class="col-md-6">
                <input name="kamerpower_user_first" placeholder="First Name" id="kamerpower_user_first" class="form-control" type="text"/>
            </div>
            <div class="col-md-6">
                <input name="kamerpower_user_last" placeholder="Last Name" id="kamerpower_user_last" class="form-control" type="text"/>
            </div>
            <div class="col-md-6">
                <input name="kamerpower_user_email" placeholder="Email" id="kamerpower_user_email" class="form-control" type="email"/>
            </div>
            <div class="col-md-6">
                <input name="kamerpower_user_pass" placeholder="Password" id="password" class="form-control" type="password"/>
            </div>
            <div class="col-md-6">
                <input name="kamerpower_user_pass_confirm" placeholder="Repeat Password" id="password_again" class="form-control" type="password"/>
            </div>
        </div>
        <p>
            <input type="hidden" name="role" value="<?php echo $role; ?>"/>
            <input type="hidden" name="security" value="<?php echo wp_create_nonce('kamerpower-register-nonce'); ?>"/>
            <button type="submit"><?php _e('Save'); ?></button>
        </p>
        <p class="another-pages">Already have an account? <a href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a></p>
    </form>
    <?php
    return ob_get_clean();
}

// Campos del formulario de inicio de sesión
function kamerpower_login_form_fields()
{
    ob_start(); ?>
    <h1 class="site-title"><?php _e('Login'); ?></h1>
    <div id="kamerpower_login_errors"></div>
    <form id="kamerpower_login_form" class="kamerpower_form">
        <fieldset>
            <p>
                <input name="kamerpower_user_login" placeholder="Email" id="kamerpower_user_login" class="form-control" type="text"/>
            </p>
            <p>
                <input name="kamerpower_user_pass" placeholder="Password" id="kamerpower_user_pass" class="form-control" type="password"/>
            </p>
            <p>
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('kamerpower-login-nonce'); ?>"/>
                <button id="kamerpower_login_submit" type="submit"><?php _e('Save'); ?></button>
            </p>
            <p class="another-pages"><a href="<?php echo esc_url(home_url("/auth?action=register")); ?>">Register</a> | <a href="<?php echo esc_url(home_url("/auth?action=password_reset")); ?>">Lost your password?</a></p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}


// Campos del formulario de recuperación de contraseña
function kamerpower_password_reset_form_fields()
{
    ob_start(); ?>
    <h1 class="site-title"><?php _e('Reset Password'); ?></h1>
    <div id="kamerpower_reset_errors"></div>
    <form id="kamerpower_reset_form" class="kamerpower_form">
        <fieldset>
            <p>
                <input name="kamerpower_user_email" placeholder="Email" id="kamerpower_user_email" class="form-control" type="email"/>
            </p>
            <p>
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('kamerpower-reset-nonce'); ?>"/>
                <button type="submit"><?php _e('Save'); ?></button>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}

// Campos del formulario de restablecimiento de contraseña
function kamerpower_new_password_form_fields()
{
    ob_start(); ?>
    <h1 class="site-title"><?php _e('Set a New Password'); ?></h1>
    <div id="kamerpower_new_password_errors"></div>
    <form id="kamerpower_new_password_form" class="kamerpower_form">
        <fieldset>
            <p>
                <input name="new_password" placeholder="Password" id="new_password" class="form-control" type="password"/>
            </p>
            <p>
                <input name="confirm_password" placeholder="Confirm Password" id="confirm_password" class="form-control" type="password"/>
            </p>
            <p>
                <input type="hidden" name="reset_key" value="<?php echo esc_attr($_GET['key']); ?>"/>
                <input type="hidden" name="reset_login" value="<?php echo esc_attr($_GET['login']); ?>"/>
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('kamerpower-new-password-nonce'); ?>"/>
                <button type="submit"><?php _e('Save'); ?></button>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}

// Manejo de registro por AJAX
function kamerpower_register_user($role = 'agent')
{
    check_ajax_referer('kamerpower-register-nonce', 'security');

    $first_name = sanitize_text_field($_POST['kamerpower_user_first']);
    $last_name = sanitize_text_field($_POST['kamerpower_user_last']);
    $email = sanitize_email($_POST['kamerpower_user_email']);
    $password = sanitize_text_field($_POST['kamerpower_user_pass']);
    $password_confirm = sanitize_text_field($_POST['kamerpower_user_pass_confirm']);
    $role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : 'agent';

    $errors = new WP_Error();

    if (empty($first_name)) {
        $errors->add('empty_first_name', __('Please enter your first name.'));
    }

    if (empty($last_name)) {
        $errors->add('empty_last_name', __('Please enter your last name.'));
    }

    if (empty($email)) {
        $errors->add('empty_email', __('Please enter your email.'));
    } elseif (!is_email($email)) {
        $errors->add('invalid_email', __('Invalid email address.'));
    } elseif (email_exists($email)) {
        $errors->add('email_exists', __('Email already exists. Please choose another one.'));
    }

    if (empty($password)) {
        $errors->add('empty_password', __('Please enter a password.'));
    } elseif (strlen($password) < 6) {
        $errors->add('password_too_short', __('Password should be at least 6 characters long.'));
    } elseif ($password !== $password_confirm) {
        $errors->add('password_mismatch', __('Passwords do not match.'));
    }

    if (!empty($errors->get_error_messages())) {
        wp_send_json_error($errors);
    }

    $user_id = wp_insert_user(array(
        'user_login' => $email,
        'user_email' => $email,
        'user_pass' => $password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'role' => $role,
    ));

    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_messages());
    }

    $post_type = $role == 'agent' ? 'commercial_agent' : 'company';

    $agent_post_id = wp_insert_post(array(
        'post_title' => $first_name . ' ' . $last_name,
        'post_type' => $post_type,
        'post_status' => 'publish',
        'post_author' => $user_id,
    ));

    if (is_wp_error($agent_post_id)) {
        wp_send_json_error($agent_post_id->get_error_messages());
    } 
    
    carbon_set_post_meta( $agent_post_id, 'agent', $user_id );
    
    

    wp_send_json_success(__('Registration successful.'));

    die();
}

add_action('wp_ajax_nopriv_kamerpower_register_user', 'kamerpower_register_user');

// Iniciar sesión por AJAX
// Iniciar sesión por AJAX
function kamerpower_login_user()
{
    check_ajax_referer('kamerpower-login-nonce', 'security');

    $user_login = sanitize_text_field($_POST['kamerpower_user_login']);
    $user_password = sanitize_text_field($_POST['kamerpower_user_pass']);
    $remember = (isset($_POST['remember_me']) && $_POST['remember_me'] == 'true') ? true : false;

    $login_data = array();
    $login_data['user_login'] = $user_login;
    $login_data['user_password'] = $user_password;
    $login_data['remember'] = $remember;

    $user = wp_signon($login_data, false);

    if (is_wp_error($user)) {
        wp_send_json_error(array('message' => $user->get_error_message()));
    } else {
        wp_send_json_success(__('Login successful.'));
    }

    die();
}
add_action('wp_ajax_nopriv_kamerpower_login_user', 'kamerpower_login_user');


// Resetear contraseña por AJAX
function kamerpower_reset_password()
{
    check_ajax_referer('kamerpower-reset-nonce', 'security');

    $user_email = sanitize_email($_POST['kamerpower_user_email']);
    $user = get_user_by('email', $user_email);

    if (!$user) {
        wp_send_json_error(__('Email not found.'));
    }

    // Generate the password reset key
    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        wp_send_json_error($reset_key->get_error_message());
    }

    // Enviar correo electrónico de restablecimiento de contraseña
    $reset_link = home_url("/auth?action=new_password&key=$reset_key&login=" . rawurlencode($user->user_login));
    $email_subject = __('Password Reset Request');
    $email_content = __('Click on the following link to reset your password:') . "\r\n\r\n" . $reset_link;

    $sent = wp_mail($user_email, $email_subject, $email_content);

    if ($sent) {
        wp_send_json_success(__('Password reset email sent.'));
    } else {
        wp_send_json_error(__('Failed to send password reset email.'));
    }

    die();
}

add_action('wp_ajax_nopriv_kamerpower_reset_password', 'kamerpower_reset_password');

// Establecer nueva contraseña por AJAX
function kamerpower_set_new_password()
{
    check_ajax_referer('kamerpower-new-password-nonce', 'security');

    $reset_key = $_POST['reset_key'];
    $reset_login = $_POST['reset_login'];
    $new_password = sanitize_text_field($_POST['new_password']);
    $confirm_password = sanitize_text_field($_POST['confirm_password']);

    $errors = new WP_Error();

    if (empty($new_password)) {
        $errors->add('empty_password', __('Please enter a password.'));
    } elseif (strlen($new_password) < 6) {
        $errors->add('password_too_short', __('Password should be at least 6 characters long.'));
    } elseif ($new_password !== $confirm_password) {
        $errors->add('password_mismatch', __('Passwords do not match.'));
    }

    if (!empty($errors->get_error_messages())) {
        wp_send_json_error($errors);
    }

    $user = check_password_reset_key($reset_key, $reset_login);

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    }

    reset_password($user, $new_password);

    wp_send_json_success(__('Password reset successfully.'));

    die();
}

add_action('wp_ajax_nopriv_kamerpower_set_new_password', 'kamerpower_set_new_password');


*/




