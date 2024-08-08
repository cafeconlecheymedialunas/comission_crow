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

     __DIR__ . '/inc/data/Deposit.php',
     __DIR__ . '/inc/data/Dispute.php',
     __DIR__ . '/inc/data/Opportunity.php',
     __DIR__ . '/inc/data/Payment.php',
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


// Añadir el meta box para 'dispute' y 'deposit'
add_action('add_meta_boxes', function() {
    $post_types = ['dispute', 'deposit'];
    foreach ($post_types as $post_type) {
        add_meta_box(
            'custom_actions_meta_box', // ID del meta box
            __('Actions'), // Título del meta box
            'render_custom_actions_meta_box', // Función de renderizado
            $post_type, // Tipo de post
            'side', // Contexto (posición)
            'high' // Prioridad
        );
    }
});

// Renderizar el meta box
function render_custom_actions_meta_box($post) {
    // Verifica nonce para seguridad
    wp_nonce_field('custom_actions_meta_box_nonce', 'custom_actions_meta_box_nonce');

    // Obtener el valor seleccionado previamente si existe
    $selected_action = get_post_meta($post->ID, '_selected_action', true);

    // Opciones del select
    $email_options = [];
    if ($post->post_type === 'dispute') {
        $email_options = [
            'dispute_approval_email_agent' => 'Send Dispute Approval Email to Commercial Agent',
            'dispute_approval_email_company' => 'Send Dispute Approval Email to Company',
            'dispute_rejected_email_agent' => 'Send Dispute Rejected Email to Commercial Agent',
            'dispute_rejected_email_company' => 'Send Dispute Rejected Email to Company',
        ];
    } elseif ($post->post_type === 'deposit') {
        $email_options = [
            'deposit_approval_email_agent' => 'Send Deposit Approval Email to Agent',
            'deposit_approval_email_company' => 'Send Deposit Approval Email to Company',
            'deposit_rejected_email_agent' => 'Send Deposit Rejected Email to Agent',
            'deposit_rejected_email_company' => 'Send Deposit Rejected Email to Company',
        ];
    }

    // Mostrar el campo select y botón
    echo '<p>';
    echo '<label for="action_select">' . __('Select Email Action:') . '</label>';
    echo '<select id="action_select" name="action_select">';
    foreach ($email_options as $key => $label) {
        $selected = $key === $selected_action ? 'selected' : '';
        echo "<option value=\"$key\" $selected>$label</option>";
    }
    echo '</select>';
    echo '</p>';
    echo '<p>';
    echo '<input type="button" class="button button-primary" id="send_email" value="' . __('Send Email') . '">';
    echo '</p>';
}

// Guardar el valor seleccionado en el meta box
add_action('save_post', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['custom_actions_meta_box_nonce']) || !wp_verify_nonce($_POST['custom_actions_meta_box_nonce'], 'custom_actions_meta_box_nonce')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['action_select'])) {
        update_post_meta($post_id, '_selected_action', sanitize_text_field($_POST['action_select']));
    }
});

// Añadir el script de AJAX al pie del administrador
add_action('admin_footer', function() {
    global $post;
    if (!in_array($post->post_type, ['dispute', 'deposit'])) return;

    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#send_email').on('click', function() {
            var post_id = <?php echo $post->ID; ?>;
            var action = $('#action_select').val();
            var data = {
                action: 'send_custom_email',
                post_id: post_id,
                selected_action: action,
                security: '<?php echo wp_create_nonce('send_custom_email_nonce'); ?>'
            };

            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    alert('Email sent successfully.');
                } else {
                    alert('Failed to send email: ' + response.data.message);
                }
            });
        });
    });
    </script>
    <?php
});

// Manejar el envío de correos electrónicos mediante AJAX
add_action('wp_ajax_send_custom_email', function() {
    check_ajax_referer('send_custom_email_nonce', 'security');

    $post_id = intval($_POST['post_id']);
    $selected_action = sanitize_text_field($_POST['selected_action']);
    
    if (!$post_id || !$selected_action) {
        wp_send_json_error(['message' => 'Invalid post ID or action.']);
    }

    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error(['message' => 'Post not found.']);
    }

    $success = false;
    $message = '';

    if ($post->post_type === 'dispute') {
        $dispute = Dispute::get_instance();

        switch ($selected_action) {
            case 'dispute_approval_email_agent':
                $success = $dispute->send_dispute_approval_email_to_agent($post_id);
                $message = 'Dispute Approval Email sent to Agent.';
                break;
            case 'dispute_approval_email_company':
                $success = $dispute->send_dispute_approval_email_to_company($post_id);
                $message = 'Dispute Approval Email sent to Company.';
                break;
            case 'dispute_rejected_email_agent':
                $success = $dispute->send_dispute_rejection_email_to_agent($post_id);
                $message = 'Dispute Rejected Email sent to Agent.';
                break;
            case 'dispute_rejected_email_company':
                $success = $dispute->send_dispute_rejection_email_to_company($post_id);
                $message = 'Dispute Rejected Email sent to Company.';
                break;
            default:
                wp_send_json_error(['message' => 'Invalid email type selected.']);
                return;
        }
    } elseif ($post->post_type === 'deposit') {
        $deposit = Deposit::get_instance();

        switch ($selected_action) {
            case 'deposit_approval_email_agent':
                $success = $deposit->send_deposit_approval_email_to_agent($post_id);
                $message = 'Deposit Approval Email sent to Agent.';
                break;
            case 'deposit_approval_email_company':
                $success = $deposit->send_deposit_approval_email_to_company($post_id);
                $message = 'Deposit Approval Email sent to Company.';
                break;
            case 'deposit_rejected_email_agent':
                $success = $deposit->send_deposit_rejection_email_to_agent($post_id);
                $message = 'Deposit Rejected Email sent to Agent.';
                break;
            case 'deposit_rejected_email_company':
                $success = $deposit->send_deposit_rejection_email_to_company($post_id);
                $message = 'Deposit Rejected Email sent to Company.';
                break;
            default:
                wp_send_json_error(['message' => 'Invalid email type selected.']);
                return;
        }
    }

    if ($success) {
        wp_send_json_success(['message' => $message]);
    } else {
        wp_send_json_error(['message' => 'Failed to send email.']);
    }
});
function handle_ajax_requests() {
    ob_start();

    // Recuperar filtros de la solicitud AJAX
    $industry_filter = isset($_GET['industry']) ? $_GET['industry'] : [];
    $language_filter = isset($_GET['language']) ? $_GET['language'] : [];
    $country_filter = isset($_GET['country']) ? $_GET['country'] : [];
    $currency_filter = isset($_GET['currency']) ? $_GET['currency'] : [];
    $target_audience_filter = isset($_GET['target_audience']) ? $_GET['target_audience'] : [];
    $age_filter = isset($_GET['age']) ? $_GET['age'] : [];
    $gender_filter = isset($_GET['gender']) ? $_GET['gender'] : [];
    $type_of_company_filter = isset($_GET['type_of_company']) ? $_GET['type_of_company'] : [];
    $deliver_leads_filter = isset($_GET['deliver_leads']) ? $_GET['deliver_leads'] : null;
    $min_price = isset($_GET['minimum_price']) ? floatval($_GET['minimum_price']) : null;
    $max_price = isset($_GET['maximum_price']) ? floatval($_GET['maximum_price']) : null;
    $commission = isset($_GET['commission']) ? intval($_GET['commission']) : null;


    // Configuración básica de la consulta
    $query_args = array(
        'post_type' => 'opportunity',
        'posts_per_page' => -1,
    );

    // Agregar meta_query si se especifica
    if ($min_price || $max_price || $commission) {
        $meta_query = array('relation' => 'AND');

        // Convertir min_price y max_price a texto para la comparación
        if ($min_price !== null) {
            $meta_query[] = array(
                'key' => 'price',
                'value' => $min_price,
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }

        // Comparar precio máximo
        if ($max_price !== null) {
            $meta_query[] = array(
                'key' => 'price',
                'value' => $max_price,
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }

        if (is_numeric($commission) && $commission > 0) {
            $meta_query[] = array(
                'key' => 'commission',
                'value' => $commission,
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }

        $query_args['meta_query'] = $meta_query;
    }

    var_dump($query_args);

    // Agregar tax_query si se especifica
    if ($industry_filter || $language_filter || $country_filter || $currency_filter || $type_of_company_filter || $target_audience_filter || $age_filter || $gender_filter) {
        $tax_query = array('relation' => 'AND');

        if ($industry_filter) {
            $tax_query[] = array(
                'taxonomy' => 'industry',
                'field'    => 'term_id',
                'terms'    => $industry_filter,
                'operator' => 'IN'
            );
        }

        if ($language_filter) {
            $tax_query[] = array(
                'taxonomy' => 'language',
                'field'    => 'term_id',
                'terms'    => $language_filter,
                'operator' => 'IN'
            );
        }

        if ($country_filter) {
            $tax_query[] = array(
                'taxonomy' => 'country',
                'field'    => 'term_id',
                'terms'    => $country_filter,
                'operator' => 'IN'
            );
        }

        if ($currency_filter) {
            $tax_query[] = array(
                'taxonomy' => 'currency',
                'field'    => 'term_id',
                'terms'    => $currency_filter,
                'operator' => 'IN'
            );
        }

        if ($type_of_company_filter) {
            $tax_query[] = array(
                'taxonomy' => 'type_of_company',
                'field'    => 'term_id',
                'terms'    => $type_of_company_filter,
                'operator' => 'IN'
            );
        }

        if ($target_audience_filter) {
            $tax_query[] = array(
                'taxonomy' => 'target_audience',
                'field'    => 'term_id',
                'terms'    => $target_audience_filter,
                'operator' => 'IN'
            );
        }

        if ($gender_filter) {
            $tax_query[] = array(
                'taxonomy' => 'gender',
                'field'    => 'term_id',
                'terms'    => $gender_filter,
                'operator' => 'IN'
            );
        }

        if ($age_filter) {
            $tax_query[] = array(
                'taxonomy' => 'age',
                'field'    => 'term_id',
                'terms'    => $age_filter,
                'operator' => 'IN'
            );
        }

        $query_args['tax_query'] = $tax_query;
    }

    // Agregar meta_query para deliver_leads si se especifica
    if ($deliver_leads_filter === 'yes') {
        $meta_query = isset($query_args['meta_query']) ? $query_args['meta_query'] : array();
        $meta_query[] = array(
            'key' => 'deliver_leads',
            'value' => 'yes',
            'compare' => '=',
        );
        $query_args['meta_query'] = $meta_query;
    }



    $opportunities = new WP_Query($query_args);

    // Preparar la respuesta
    if ($opportunities->have_posts()) {
        while ($opportunities->have_posts()) : $opportunities->the_post();
            $company_id = carbon_get_post_meta(get_the_ID(), "company");
            $target_audience = carbon_get_post_meta(get_the_ID(), "target_audience");
            $company_logo = get_the_post_thumbnail($company_id, [50, 50], [
                'class' => 'rounded-circle company-logo',
                'width' => '50',
                'height' => '50',
            ]);
            $industry = wp_get_post_terms(get_the_ID(), "industry")[0];
            ?>
            <div class="card result-item d-flex flex-row align-items-center mb-4">
                <?php if ($company_logo) {
                    echo $company_logo;
                } ?>
                <div class="detail">
                    <h3 class="title"><?php the_title(); ?></h3>
                    <p class="skill"><?php echo esc_html($industry->name); ?></p>
                    <p class="category"><?php echo esc_html($target_audience); ?></p>
                </div>
                <div class="detail">
                    <h5 class="price"><?php echo Helper::format_price(carbon_get_post_meta(get_the_ID(), "price")); ?></h5>
                    <p class="commissions"><?php echo carbon_get_post_meta(get_the_ID(), "commission"); ?> %</p>
                </div>
                <a href="<?php the_permalink(); ?>" class="btn btn-primary">Detail</a>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    } else {
        echo '<p>No opportunities found.</p>';
    }

    wp_die(); // Termina la ejecución para solicitudes AJAX
}
add_action('wp_ajax_my_ajax_filter', 'handle_ajax_requests');
add_action('wp_ajax_nopriv_my_ajax_filter', 'handle_ajax_requests');


