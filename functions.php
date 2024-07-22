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
add_action('wp_ajax_create_opportunity', [$company,'save_opportunity']);
add_action('wp_ajax_nopriv_create_opportunity', [$company,'save_opportunity']);
add_action('wp_ajax_delete_opportunity', [$company,'delete_opportunity']);
add_action('wp_ajax_nopriv_delete_opportunity', [$company,'delete_opportunity']);

add_action('wp_ajax_save_agent_profile', [$commercial_agent,'save_agent_profile']);
add_action('wp_ajax_nopriv_save_agent_profile', [$commercial_agent,'save_agent_profile']);

add_action('wp_ajax_save_company_profile', [$company,'save_company_profile']);
add_action('wp_ajax_nopriv_save_company_profile', [$company,'save_company_profile']);

add_action('wp_ajax_update_user_data', [$profileUser,'update_user_data']);
add_action('wp_ajax_nopriv_update_user_data',[$profileUser, 'update_user_data']);








add_action('wp_ajax_save_deal', 'save_deal');
add_action('wp_ajax_nopriv_save_deal', 'save_deal');

function save_deal() {
    check_ajax_referer('save_deal_nonce', 'security');

    $entity_type = sanitize_text_field($_POST['entity_type']);

    // Validar según el tipo de entidad
    if ($entity_type === 'deal') {
        // Validar campos para Deal
        if (!isset($_POST['company']) || empty($_POST['company'])) {
            wp_send_json_error('Company is required.');
        }
        if (!isset($_POST['commercial_agent']) || empty($_POST['commercial_agent'])) {
            wp_send_json_error('Commercial Agent is required.');
        }
        if (!isset($_POST['opportunity']) || empty($_POST['opportunity'])) {
            wp_send_json_error('Opportunity is required.');
        }
        if (!isset($_POST['minimal_price']) || empty($_POST['minimal_price'])) {
            wp_send_json_error('Minimal Price is required.');
        }
        if (!isset($_POST['commission']) || empty($_POST['commission'])) {
            wp_send_json_error('Commission is required.');
        }

        $company = sanitize_text_field($_POST['company']);
        $commercial_agent = sanitize_text_field($_POST['commercial_agent']);
        $opportunity = sanitize_text_field($_POST['opportunity']);
        $minimal_price = sanitize_text_field($_POST['minimal_price']);
        $commission = sanitize_text_field($_POST['commission']);

        $deal_data = [
            'ID' => intval($_POST['company_id']),
            'post_title' => 'New Deal',
            'post_type' => 'deal',
            'post_status' => 'publish'
        ];

        $deal_id = wp_update_post($deal_data);

        if (is_wp_error($deal_id)) {
            wp_send_json_error('Failed to save deal.');
        }

        carbon_set_post_meta($deal_id, 'company', $company);
        carbon_set_post_meta($deal_id, 'commercial_agent', $commercial_agent);
        carbon_set_post_meta($deal_id, 'opportunity', $opportunity);
        carbon_set_post_meta($deal_id, 'minimal_price', $minimal_price);
        carbon_set_post_meta($deal_id, 'commission', $commission);
        carbon_set_post_meta($deal_id, 'date', current_datetime());

        wp_send_json_success('Deal saved successfully!');
    } elseif ($entity_type === 'commercial_agent') {
        // Validar campos para Commercial Agent
        if (!isset($_POST['company']) || empty($_POST['company'])) {
            wp_send_json_error('Company is required.');
        }

        $company = sanitize_text_field($_POST['company']);

        $agent_data = [
            'ID' => intval($_POST['commercial_agent_id']),
            'post_title' => 'New Commercial Agent',
            'post_type' => 'commercial_agent',
            'post_status' => 'publish'
        ];

        $agent_id = wp_update_post($agent_data);

        if (is_wp_error($agent_id)) {
            wp_send_json_error('Failed to save commercial agent.');
        }

        carbon_set_post_meta($agent_id, 'company', $company);

        wp_send_json_success('Commercial Agent saved successfully!');
    } else {
        wp_send_json_error('Invalid entity type.');
    }
}



