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

    __DIR__ . '/inc/core/EmailSender.php',
    __DIR__ . '/inc/core/CustomPostType.php',
    __DIR__ . '/inc/core/CustomTaxonomy.php',
    __DIR__ . '/inc/core/ContainerCustomFields.php',
    __DIR__ . '/inc/core/Crud.php',
    __DIR__ . '/inc/core/Helper.php',
    __DIR__ . '/inc/data/StatusManager.php',
    __DIR__ . '/inc/data/Auth.php',
    __DIR__ . '/inc/data/ProfileUser.php',
    __DIR__ . '/inc/data/Company.php',
    __DIR__ . '/inc/data/CommercialAgent.php',

    __DIR__ . '/inc/data/Contract.php',
    __DIR__ . '/inc/data/CommissionRequest.php',
    __DIR__ . '/inc/data/EmailMetaBox.php',

    __DIR__ . '/inc/data/Deposit.php',
    __DIR__ . '/inc/data/Dispute.php',
    __DIR__ . '/inc/data/Opportunity.php',
    __DIR__ . '/inc/data/Payment.php',
    __DIR__ . '/inc/data/Rating.php',
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
add_action('carbon_fields_register_fields', [$containerCustomFields, 'register_fields']);

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
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key],
            ];

            $upload = wp_handle_upload($file, ['test_form' => false]);
            if ($upload && !isset($upload['error'])) {
                $attachment_id = wp_insert_attachment([
                    'guid' => $upload['url'],
                    'post_mime_type' => $upload['type'],
                    'post_title' => sanitize_file_name($upload['file']),
                    'post_content' => '',
                    'post_status' => 'inherit',
                ], $upload['file']);

                require_once ABSPATH . 'wp-admin/includes/image.php';
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



add_filter('better_messages_search_user_sql_condition', 'only_users_with_contract_search', 10, 4);

function only_users_with_contract_search($sql_array, $user_ids, $search, $user_id) {
    // Obtener IDs de usuarios con los que el usuario actual tiene un contrato activo
    $valid_user_ids = get_users_with_active_contract($user_id);

    if (!empty($valid_user_ids)) {
        $valid_user_ids_string = implode(',', $valid_user_ids);
        $sql_array[] = "AND `ID` IN ($valid_user_ids_string)";
    } else {
        // Si no tiene contratos activos con nadie, excluye a todos
        $sql_array[] = "AND `ID` IN (0)";
    }

    return $sql_array;
}

// Función para obtener los IDs de usuarios con los que se tiene un contrato activo
function get_users_with_active_contract($user_id) {
    // Buscar contratos activos donde el usuario actual es parte
    $post_type = ProfileUser::get_instance()->get_user_associated_post_type();

    $user = get_user_by("ID",$user_id);



    $args = [
        'post_type'   => 'contract',
        'meta_query'  => [
            [
                'key'     => $user->roles[0],
                'value'   => $post_type->ID,
                'compare' => '=',
            ],
        ],
    ];
    $query = new Wp_Query($args);
    
    $contracts = $query->posts;
    

    foreach ($contracts as $contract) {
        $company_id = carbon_get_post_meta($contract->ID, 'company');
     
        $user_company_id = carbon_get_post_meta($company_id ,"user_id");
        $commercial_agent_id = carbon_get_post_meta($contract->ID, 'commercial_agent');
        $user_commercial_agent_id = carbon_get_post_meta($commercial_agent_id ,"user_id");
        if ($user_company_id && $user_company_id != $user_id) {
            $user_ids[] = $user_company_id;
        }

        if ($user_commercial_agent_id  && $user_commercial_agent_id  != $user_id) {
            $user_ids[] = $commercial_agent_id;
        }
    }

    return $user_ids;
}


