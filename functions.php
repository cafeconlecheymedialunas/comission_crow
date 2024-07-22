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
$files_to_require = [
    __DIR__ . '/inc/core/Custom_Post_Type.php',
    __DIR__ . '/inc/core/Custom_Taxonomy.php',
 
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker.php',
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker-footer.php',
    __DIR__ . '/inc/setup/customizer.php',
    __DIR__ . '/inc/setup/setup-theme.php',
    __DIR__ . '/inc/admin.php',
    
    __DIR__ . '/inc/public.php',
     __DIR__ . '/inc/auth.php',
     __DIR__ . '/inc/core/Company.php',
     __DIR__ . '/inc/core/CommercialAgent.php',
  
];
function require_files(array $files)
{
    foreach ($files as $file) {
        if (is_readable($file)) {
            require_once $file;
        }
    }
}

require_files($files_to_require);

$admin = new Admin();
$public = new PublicFront();




function custom_rewrite_rules()
{
    add_rewrite_rule('^dashboard/([^/]+)(/.*)?/?$', 'index.php?pagename=dashboard&role=$matches[1]&subpages=$matches[2]', 'top');
}
add_action('init', 'custom_rewrite_rules');

function add_query_vars($vars)
{
    $vars[] = 'role';
    $vars[] = 'subpages';
    return $vars;
}
add_filter('query_vars', 'add_query_vars');








$company = Company::get_instance();
$commercial_agent = CommercialAgent::get_instance();
add_action('wp_ajax_create_opportunity', [$company,'save_opportunity']);
add_action('wp_ajax_nopriv_create_opportunity', [$company,'save_opportunity']);
add_action('wp_ajax_delete_opportunity', [$company,'delete_opportunity']);
add_action('wp_ajax_nopriv_delete_opportunity', [$company,'delete_opportunity']);

add_action('wp_ajax_save_agent_profile', [$commercial_agent,'save_agent_profile']);
add_action('wp_ajax_nopriv_save_agent_profile', [$commercial_agent,'save_agent_profile']);

add_action('wp_ajax_save_company_profile', [$company,'save_company_profile']);
add_action('wp_ajax_nopriv_save_company_profile', [$company,'save_company_profile']);


// Para usuarios no autenticados
/*add_action('wp_ajax_update_profile_agent', 'update_profile_agent');
add_action('wp_ajax_nopriv_update_profile_agent', 'update_profile_agent'); // Para usuarios no autenticados

function update_profile_agent()
{
    // Verifica el nonce para la seguridad
    check_ajax_referer('update-profile-agent-nonce', 'security');

    // Verifica que el usuario está autenticado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to update your profile.']);
    }

    $current_user = wp_get_current_user();

    parse_str($_POST['form_data'], $form_data);

    // Actualiza los campos del usuario utilizando Carbon Fields
    carbon_set_post_meta($current_user->ID, 'first_name', sanitize_text_field($form_data['first_name']));
    carbon_set_post_meta($current_user->ID, 'last_name', sanitize_text_field($form_data['last_name']));
    carbon_set_post_meta($current_user->ID, 'user_email', sanitize_email($form_data['user_email']));
    carbon_set_post_meta($current_user->ID, 'description', sanitize_textarea_field($form_data['description']));
    carbon_set_post_meta($current_user->ID, 'language', array_map('sanitize_text_field', $form_data['language']));
    carbon_set_post_meta($current_user->ID, 'country', sanitize_text_field($form_data['country']));
    carbon_set_post_meta($current_user->ID, 'skills', array_map('intval', $form_data['skills']));
    carbon_set_post_meta($current_user->ID, 'industries', array_map('intval', $form_data['industry']));
    carbon_set_post_meta($current_user->ID, 'seller_type', sanitize_text_field($form_data['seller_type']));
    carbon_set_post_meta($current_user->ID, 'selling_methods', array_map('intval', $form_data['selling_methods']));
    carbon_set_post_meta($current_user->ID, 'years_of_experience', sanitize_text_field($form_data['years_of_experience']));

    wp_send_json_success(['message' => 'Profile updated successfully.']);
    wp_die();
}
*/


function update_user_data()
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
add_action('wp_ajax_update_user_data', 'update_user_data');
add_action('wp_ajax_nopriv_update_user_data', 'update_user_data');
