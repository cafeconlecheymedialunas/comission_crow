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
     __DIR__ . '/inc/data/Company.php',
     __DIR__ . '/inc/data/CommercialAgent.php',
     __DIR__ . '/inc/data/ProfileUser.php',
     __DIR__ . '/inc/data/Contract.php',
     __DIR__ . '/inc/data/Commissionrequest.php',
     __DIR__ . '/inc/data/Dispute.php',
     __DIR__ . '/inc/data/Opportunity.php',
     __DIR__ . '/inc/data/Payment.php',
     __DIR__ . '/inc/core/CustomPostType.php',
     __DIR__ . '/inc/core/CustomTaxonomy.php',
     __DIR__ . '/inc/core/ContainerCustomFields.php',
     __DIR__ . '/inc/core/Admin.php',
     __DIR__ . '/inc/core/Public.php',
     __DIR__ . '/inc/core/Auth.php',
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







function calculate_agent_price($commission_request_id)
{
    $post = get_post($commission_request_id);
    if (!$post) {
        return;
    }

    $contract_id = carbon_get_post_meta($commission_request_id, "contract_id");
    $total_cart = floatval(carbon_get_post_meta($commission_request_id, "total_cart"));
    $commission = floatval(carbon_get_post_meta($contract_id, "commission"));

    if (empty($total_cart) || !is_numeric($total_cart)) {
        return;
    }
    if (empty($commission) || !is_numeric($commission)) {
        return;
    }

    $discount = $total_cart * $commission / 100;

    return $discount;
}

function calculate_tax_stripe_price($commission_request_id)
{
    $post = get_post($commission_request_id);
    if (!$post) {
        return;
    }

    $total_cart = floatval(carbon_get_post_meta($commission_request_id, "total_cart"));
    if (empty($total_cart) || !is_numeric($total_cart)) {
        return;
    }

    $tax = $total_cart * 3 / 100;

    return $tax;
}

function calculate_platform_price($commission_request_id)
{
    $post = get_post($commission_request_id);
    if (!$post) {
        return;
    }

    $total = floatval(carbon_get_post_meta($commission_request_id, "total_cart"));
    if (empty($total) || !is_numeric($total)) {
        return;
    }

    // Calcular la tasa del 5%
    $tax = $total * 5 / 100;

    return $tax;
}

function calculate_total($commission_request_id)
{
    $post = get_post($commission_request_id);
    if (!$post) {
        return;
    }

    $total = floatval(carbon_get_post_meta($commission_request_id, "total_cart"));
    if (empty($total) || !is_numeric($total)) {
        return;
    }
    $agent_price = calculate_agent_price($commission_request_id);
    $tax_price = calculate_tax_stripe_price($commission_request_id);
    $platform_price = calculate_platform_price($commission_request_id);

    $total_final = $agent_price + $tax_price + $platform_price;

    return $total_final;
}
















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
