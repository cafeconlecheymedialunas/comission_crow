<?php

/**
 * Include Theme Customizer.
 *
 * @since v1.0
 */
define('THEME_URL', get_template_directory_uri());
require_once get_template_directory() . '/vendor/autoload.php';
\Carbon_Fields\Carbon_Fields::boot();

// Array de rutas de archivos
$files_to_require = array(
    __DIR__ . '/inc/core/Custom_Post_Type.php',
   
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker.php',
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker-footer.php',
    __DIR__ . '/inc/setup/customizer.php',
    __DIR__ . '/inc/setup/setup-theme.php',
    __DIR__ . '/inc/admin.php',

);
function require_files(array $files)
{
    foreach ($files as $file) {
        if (is_readable($file)) {
            require_once $file;
        }
    }
}

require_files($files_to_require);

new Admin();



function my_theme_enqueue_styles() {

 wp_enqueue_style('my-theme-extra-style', get_stylesheet_directory_uri(). "/extra.css" );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );



add_action('wp_ajax_nopriv_user_register', 'user_register');
add_action('wp_ajax_user_register', 'user_register');

function user_register() {
    $user_login = $_POST['user_login'];
    $user_email = $_POST['user_email'];
    $user_pass  = $_POST['user_pass'];

    if (username_exists($user_login) || email_exists($user_email)) {
        wp_send_json_error('Username or email already exists.');
    } else {
        $user_id = wp_create_user($user_login, $user_pass, $user_email);
        if (is_wp_error($user_id)) {
            wp_send_json_error($user_id->get_error_message());
        } else {
            wp_send_json_success('User registered successfully.');
        }
    }
}

add_action('wp_ajax_nopriv_user_login', 'user_login');
add_action('wp_ajax_user_login', 'user_login');

function user_login() {
    $creds = array(
        'user_login'    => $_POST['log'],
        'user_password' => $_POST['pwd'],
        'remember'      => true,
    );

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    } else {
        wp_send_json_success('Login successful.');
    }
}


function redirect_non_logged_users() {
    if (is_page('dashboard') && !is_user_logged_in()) {
        wp_redirect(home_url('/login'));
        exit;
    }
}
add_action('template_redirect', 'redirect_non_logged_users');