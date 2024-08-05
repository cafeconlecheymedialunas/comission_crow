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
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker.php',
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker-footer.php',
    __DIR__ . '/inc/setup/customizer.php',
    __DIR__ . '/inc/setup/setup-theme.php',

    __DIR__ . '/inc/core/Crud.php',

    __DIR__ . '/inc/core/Helper.php',
    __DIR__ . '/inc/data/Auth.php',
     __DIR__ . '/inc/data/Company.php',
     __DIR__ . '/inc/data/CommercialAgent.php',
     __DIR__ . '/inc/data/ProfileUser.php',
     __DIR__ . '/inc/data/Contract.php',
     __DIR__ . '/inc/data/Commissionrequest.php',
     __DIR__ . '/inc/data/StatusManager.php',
     __DIR__ . '/inc/data/Deposit.php',
     __DIR__ . '/inc/data/Dispute.php',
     __DIR__ . '/inc/data/Opportunity.php',
     __DIR__ . '/inc/data/Payment.php',
     __DIR__ . '/inc/core/EmailSender.php',
     __DIR__ . '/inc/core/CustomPostType.php',
     __DIR__ . '/inc/core/CustomTaxonomy.php',
     __DIR__ . '/inc/core/ContainerCustomFields.php',
     __DIR__ . '/inc/core/Admin.php',
     __DIR__ . '/inc/core/Public.php',

     __DIR__ . '/inc/core/Dashboard.php',
   
  
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

$dasboard = new Dashboard();

$containerCustomFields = new ContainerCustomFields($admin);
add_action('carbon_fields_register_fields', [$containerCustomFields,'register_fields']);
























// Función para validar los archivos
function validate_files($files, $allowed_types = ['application/pdf', 'text/plain'], $max_size = 10485760) // 10MB
{
    foreach ($files['name'] as $key => $value) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            $file_type = $files['type'][$key];
            $file_size = $files['size'][$key];

            if (!in_array($file_type, $allowed_types)) {
                return ['error' => 'Invalid file type. Only PDF and text files are allowed.'];
            }

            if ($file_size > $max_size) {
                return ['error' => 'File size exceeds the maximum limit of 10MB.'];
            }
        }
    }
    return ['success' => true];
}

// Función para manejar la carga de múltiples archivos y devolver los IDs de los attachments
function handle_multiple_file_upload($files)
{
    $uploads = [];
    foreach ($files['name'] as $key => $value) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            $file = [
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error'    => $files['error'][$key],
                'size'     => $files['size'][$key],
            ];

            $upload = wp_handle_upload($file, ['test_form' => false]);
            if ($upload && !isset($upload['error'])) {
                $attachment_id = wp_insert_attachment([
                    'guid'           => $upload['url'],
                    'post_mime_type' => $upload['type'],
                    'post_title'     => sanitize_file_name($upload['file']),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                ], $upload['file']);

                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attach_data);

                $uploads[] = $attachment_id;
            } else {
                return ['error' => 'File upload error: ' . $upload['error']];
            }
        }
    }
    return $uploads;
}





function send_email_on_deposit_creation($post_id, $post, $update)
{
    // Asegúrate de que es el tipo de post correcto y que es una creación, no una actualización.
    if ($post->post_type != 'deposit' || $update) {
        return;
    }


}

// Añade el hook para ejecutar la función cuando se guarda un post
add_action('save_post', 'send_email_on_deposit_creation', 10, 3);








function custom_login_redirect($redirect_to, $request, $user)
{
    // Verifica que el usuario esté autenticado y que no sea un objeto WP_Error
    if (!is_wp_error($user) && isset($user->roles) && is_array($user->roles)) {
        // Verifica que el usuario tenga uno de los roles especificados
        if (in_array('company', $user->roles) || in_array('commercial_agent', $user->roles)) {
            // Redirige a la página de inicio personalizada
            return home_url('/auth/');
        }
    }

    // Permite el comportamiento de redirección predeterminado para otros usuarios
    return $redirect_to;
}
add_filter('login_redirect', 'custom_login_redirect', 10, 3);
