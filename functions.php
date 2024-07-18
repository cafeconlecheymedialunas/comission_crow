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
add_action('wp_ajax_nopriv_save_agent_profile',  [$commercial_agent,'save_agent_profile']);

add_action('wp_ajax_save_company_profile', [$company,'save_company_profile']);
add_action('wp_ajax_nopriv_save_company_profile',  [$company,'save_company_profile']);


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
    carbon_set_post_meta($current_user->ID, 'location', sanitize_text_field($form_data['location']));
    carbon_set_post_meta($current_user->ID, 'skills', array_map('intval', $form_data['skills']));
    carbon_set_post_meta($current_user->ID, 'industries', array_map('intval', $form_data['industry']));
    carbon_set_post_meta($current_user->ID, 'seller_type', sanitize_text_field($form_data['seller_type']));
    carbon_set_post_meta($current_user->ID, 'selling_methods', array_map('intval', $form_data['selling_methods']));
    carbon_set_post_meta($current_user->ID, 'years_of_experience', sanitize_text_field($form_data['years_of_experience']));

    wp_send_json_success(['message' => 'Profile updated successfully.']);
    wp_die();
}
*/
