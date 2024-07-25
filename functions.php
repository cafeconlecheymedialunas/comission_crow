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
     __DIR__ . '/inc/core/ProfileUser.php',
     __DIR__ . '/inc/core/Agreement.php',
     __DIR__ . '/inc/core/Commissionrequest.php',
  
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
$profileUser = ProfileUser::get_instance();
$agreement = Agreement::get_instance();
$commission_request = Commissionrequest::get_instance();
add_action('wp_ajax_create_opportunity', [$company,'save_opportunity']);
add_action('wp_ajax_nopriv_create_opportunity', [$company,'save_opportunity']);
add_action('wp_ajax_delete_opportunity', [$company,'delete_opportunity']);
add_action('wp_ajax_nopriv_delete_opportunity', [$company,'delete_opportunity']);

add_action('wp_ajax_save_agent_profile', [$commercial_agent,'save_agent_profile']);
add_action('wp_ajax_nopriv_save_agent_profile', [$commercial_agent,'save_agent_profile']);

add_action('wp_ajax_save_company_profile', [$company,'save_company_profile']);
add_action('wp_ajax_nopriv_save_company_profile', [$company,'save_company_profile']);

add_action('wp_ajax_update_user_data', [$profileUser,'update_user_data']);
add_action('wp_ajax_nopriv_update_user_data', [$profileUser, 'update_user_data']);
add_action('wp_ajax_create_agreement', [$agreement,'create_agreement']);
add_action('wp_ajax_nopriv_create_agreement', [$agreement,'create_agreement']);
add_action('wp_ajax_update_agreement_status', [$agreement,'update_agreement_status']);
add_action('wp_ajax_nopriv_update_agreement_status', [$agreement,'update_agreement_status']);

add_action('wp_ajax_create_commission_request', [$commission_request,'create_commission_request']);
add_action('wp_ajax_nopriv_create_commission_request', [$commission_request,'create_commission_request']);

function load_email_template($template_name, $variables = [])
{
    extract($variables);
    $template_path = get_template_directory() . '/page-template/emails/' . $template_name;
    if (!file_exists($template_path)) {
        return '';
    }
    ob_start();
    include $template_path;
    return ob_get_clean();
}


function send_agreement_email($to, $subject, $template, $variables)
{
    $body = load_email_template($template, $variables);
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($to, $subject, $body, $headers);
}












function schedule_agreement_finalization_event()
{
    if (!wp_next_scheduled('finalize_scheduled_agreements')) {
        wp_schedule_event(time(), 'daily', 'finalize_scheduled_agreements');
    }
}
add_action('wp', 'schedule_agreement_finalization_event');

function finalize_scheduled_agreements()
{
    $args = [
        'post_type' => 'agreement',
        'meta_query' => [
            [
                'key' => 'status',
                'value' => 'finishing',
                'compare' => '=',
            ],
            [
                'key' => 'finalization_date',
                'value' => time(),
                'compare' => '<=',
                'type' => 'NUMERIC',
            ],
        ],
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $agreement) {
            $agreement_id = $agreement->ID;

            // Actualizar el estado del acuerdo a "finalizado"
            update_post_meta($agreement_id, 'status', 'finished');

            // Actualizar el historial de estados
            $status_history = get_post_meta($agreement_id, 'status_history', true);
            if (!is_array($status_history)) {
                $status_history = [];
            }

            $status_history[] = [
                'status' => 'finished',
                'date' => current_time('mysql'),
                'changed_by' => 0, // ID 0 indica que fue un cambio automático
            ];

            update_post_meta($agreement_id, 'status_history', $status_history);
        }
    }

    wp_reset_postdata();
}
add_action('finalize_scheduled_agreements', 'finalize_scheduled_agreements');
