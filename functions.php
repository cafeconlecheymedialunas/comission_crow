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
    __DIR__ . '/inc/auth.php',
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

$admin = new Admin();




// Función para manejar la actualización del perfil del vendedor
add_action('wp_ajax_update_seller_profile', 'update_seller_profile');
add_action('wp_ajax_nopriv_update_seller_profile', 'update_seller_profile');  // Para usuarios no autenticados

function update_seller_profile() {
    check_ajax_referer('update_seller_profile', 'update_seller_profile_nonce');

    if (!current_user_can('edit_user', $_POST['user_id'])) {
        wp_send_json_error(__('Permission denied.', 'textdomain'));
    }

    $user_id = intval($_POST['user_id']);
    $errors = array();

    // Validaciones
    if (empty($_POST['first_name'])) {
        $errors['first_name'] = __('First name is required.', 'textdomain');
    }

    if (empty($_POST['last_name'])) {
        $errors['last_name'] = __('Last name is required.', 'textdomain');
    }

    if (empty($_POST['user_email']) || !is_email($_POST['user_email'])) {
        $errors['user_email'] = __('Valid email address is required.', 'textdomain');
    }

    if (empty($_POST['description'])) {
        $errors['description'] = __('Description is required.', 'textdomain');
    }

    if (empty($_POST['location'])) {
        $errors['location'] = __('Location is required.', 'textdomain');
    }

    if (empty($_POST['seller_type'])) {
        $errors['seller_type'] = __('Seller type is required.', 'textdomain');
    }

    if (empty($_POST['language'])) {
        $errors['language'] = __('At least one language must be selected.', 'textdomain');
    }

    if (empty($_POST['selling_methods'])) {
        $errors['selling_methods'] = __('At least one selling method must be selected.', 'textdomain');
    }

    if (!empty($errors)) {
        wp_send_json_error($errors);
    }

    // Actualización si no hay errores
    $args = array(
        'post_type'   => 'commercial_agent',
        'meta_query'  => array(
            array(
                'key'     => 'agent',
                'value'   => $user_id,
                'compare' => '=',
            ),
        ),
    );

    $agent_query = new WP_Query($args);

    if (!$agent_query->have_posts()) {
        wp_send_json_error(__('Commercial agent not found for this user.', 'textdomain'));
    }

    while ($agent_query->have_posts()) {
        $agent_query->the_post();
        $agent_id = get_the_ID();

        // Actualizar los campos de Carbon Fields
        carbon_set_post_meta($agent_id, 'description', sanitize_textarea_field($_POST['description']));
        carbon_set_post_meta($agent_id, 'language', $_POST['language']);  // Asegúrate de que $_POST['language'] es un array
        carbon_set_post_meta($agent_id, 'location', sanitize_text_field($_POST['location']));
        carbon_set_post_meta($agent_id, 'seller_type', sanitize_text_field($_POST['seller_type']));
        carbon_set_post_meta($agent_id, 'selling_methods', $_POST['selling_methods']);  // Asegúrate de que $_POST['selling_methods'] es un array

        // Actualizar los campos de usuario
        $update_user_data = array(
            'ID'           => $user_id,
            'first_name'   => sanitize_text_field($_POST['first_name']),
            'last_name'    => sanitize_text_field($_POST['last_name']),
            'user_email'   => sanitize_email($_POST['user_email']),
        );

        wp_update_user($update_user_data);

        wp_send_json_success(__('Profile updated successfully.', 'textdomain'));
    }

    wp_reset_postdata();
    wp_die();
}
