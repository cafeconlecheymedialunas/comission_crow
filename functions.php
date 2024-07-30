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

  
     __DIR__ . '/inc/data/Company.php',
     __DIR__ . '/inc/data/CommercialAgent.php',
     __DIR__ . '/inc/data/ProfileUser.php',
     __DIR__ . '/inc/data/Contract.php',
     __DIR__ . '/inc/data/Commissionrequest.php',
     __DIR__ . '/inc/data/Dispute.php',
     __DIR__ . '/inc/data/Opportunity.php',
     __DIR__ . '/inc/core/CustomPostType.php',
     __DIR__ . '/inc/core/CustomTaxonomy.php',
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









function add_item_to_status_history($contract_id, $status = "pending")
{
    $status_history = get_post_meta($contract_id, 'status_history', true);
    
    if (!is_array($status_history)) {
        $status_history = [];
    }

    // Agregar el nuevo estado al historial
    $status_history[] = [
        'history_status' => $status,
        'date_status' => current_time('mysql'),
        'changed_by' => get_current_user_id(),
    ];
    return $status_history;
}






function get_commission_requests_for_user()
{
   

    // Obtener el tipo de post asociado al usuario actual
    $post = get_user_associated_post_type();
    if (!$post) {
        return []; // No hay post asociado al usuario
    }

    // Consulta para obtener los contratos asociados al usuario actual
    $contract_query = new WP_Query([
        'post_type'  => 'contract',
        'meta_query' => [
            [
                'key'   => $post->post_type,
                'value' => $post->ID,
                'compare' => '=',
            ]
        ]
    ]);

    // Array para almacenar los IDs de los contratos
    $contract_ids = [];
    foreach ($contract_query->posts as $contract) {
        $contract_ids[] = $contract->ID;
    }

    if (empty($contract_ids)) {
        return []; // No hay contratos asociados
    }

    // Consulta para obtener las solicitudes de comisión asociadas a los contratos
    $commission_request_query = new WP_Query([
        'post_type'  => 'commission_request',
        'meta_query' => [
            [
                'key'   => 'contract_id', // Asegúrate de que el meta_key sea correcto
                'value' => $contract_ids,
                'compare' => 'IN',
            ]
        ]
    ]);

    // Devolver los resultados
    return $commission_request_query->posts;
}

function get_disputes_for_user()
{
    $commission_requests = get_commission_requests_for_user();
    // Consulta para obtener las disputas asociadas a los contratos

    $commission_request_ids = [];
    foreach ($commission_requests as $commission_request) {
        $commission_request_ids[] = $commission_request->ID;
    }
    $dispute_query = new WP_Query([
        'post_type'  => 'dispute',
        'meta_query' => [
            [
                'key'   => 'commission_request_id', // Asegúrate de que el meta_key sea correcto
                'value' => $commission_request_ids,
                'compare' => 'IN',
            ]
        ]
    ]);

    // Devolver los resultados
    return $dispute_query->posts;
}



function get_user_associated_post_type()
{
    $current_user = wp_get_current_user();

    $query = new WP_Query([
        'post_type'  => $current_user->roles[0],
        'meta_query' => [
            [
                'key'   => 'user_id',
                'value' => $current_user->ID,
                'compare' => '=',
            ]
        ]
    ]);
        


    return  $query->posts[0] ?? false;

}





function get_another_part_of_contract($post_id)
{
    $current_user = wp_get_current_user();
    $counterparter_key = in_array("company", $current_user->roles)?"commercial_agent":"company";
                                
    $counterparty_id = carbon_get_post_meta($post_id, $counterparter_key);

    $counterparty = get_post($counterparty_id);

    return $counterparty;

}

function get_user_another_part_of_contract($post_id)
{
    $current_user = wp_get_current_user();
    $counterparter_key = in_array("company", $current_user->roles)?"commercial_agent":"company";
                                
    $counterparty_id = carbon_get_post_meta($post_id, $counterparter_key);
                                
    $user_id = carbon_get_post_meta($counterparty_id, 'user_id');

    return get_user_by("ID", $user_id);

}





// Función para validar los archivos
function validate_files($files, $allowed_types = ['application/pdf', 'text/plain'], $max_size = 10485760) // 10MB
{foreach ($files['name'] as $key => $value) {
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
