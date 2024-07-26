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
     __DIR__ . '/inc/core/Contract.php',
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
$contract = Contract::get_instance();
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

add_action('wp_ajax_create_contract', [$contract,'create_contract']);
add_action('wp_ajax_nopriv_create_contract', [$contract,'create_contract']);

add_action('wp_ajax_update_contract_status', [$contract,'update_contract_status']);
add_action('wp_ajax_nopriv_update_contract_status', [$contract,'update_contract_status']);

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


function send_contract_email($to, $subject, $template, $variables)
{
    $body = load_email_template($template, $variables);
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($to, $subject, $body, $headers);
}

define("KEY_TYPE_AGENT", "commercial_agent");
define("USER_TYPE_COMPANY", "company");
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

function schedule_contract_finalization_event()
{
    if (!wp_next_scheduled('finalize_scheduled_contracts')) {
        wp_schedule_event(time(), 'daily', 'finalize_scheduled_contracts');
    }
}
add_action('wp', 'schedule_contract_finalization_event');

function finalize_scheduled_contracts()
{
    $args = [
        'post_type' => 'contract',
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
        foreach ($query->posts as $contract) {
            $contract_id = $contract->ID;

            // Actualizar el estado del acuerdo a "finalizado"
            update_post_meta($contract_id, 'status', 'finished');

            // Actualizar el historial de estados
            $status_history = get_post_meta($contract_id, 'status_history', true);
            if (!is_array($status_history)) {
                $status_history = [];
            }

            $status_history[] = [
                'status' => 'finished',
                'date' => current_time('mysql'),
                'changed_by' => 0, // ID 0 indica que fue un cambio automático
            ];

            update_post_meta($contract_id, 'status_history', $status_history);
        }
    }

    wp_reset_postdata();
}
add_action('finalize_scheduled_contracts', 'finalize_scheduled_contracts');


add_action('wp_ajax_save_message', 'save_message');
add_action('wp_ajax_nopriv_save_message', 'save_message');

function save_message()
{
    check_ajax_referer('save_message_nonce', 'security');

    $post_id = intval($_POST['post_id']);
    $from = intval($_POST['from']);
    $to = intval($_POST['to']);
    $message = wp_kses_post($_POST['message']);

    $messages = carbon_get_post_meta($post_id, 'messages');
    $messages[] = [
        'from' => $from,
        'to' => $to,
        'message' => $message,
    ];

    carbon_set_post_meta($post_id, 'messages', $messages);

    wp_send_json_success(['messages' => $messages]);
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
        


    return  $query->posts[0] ?  $query->posts[0] :false;

}


function get_role_url_link_dashboard_page($route_key)
{
    $current_user = wp_get_current_user();
    $key = in_array("commercial_agent", $current_user->roles) ? "commercial_agent" : "company";

    $routes = [
        "profile" => [
            "company" => "dashboard/company/profile/",
            "commercial_agent" => "dashboard/commercial-agent/profile/"
        ],
        "opportunity_create" => [
            "company" => "dashboard/company/opportunity/create",
            "commercial_agent" => ""
        ],
        "opportunity_list" => [
            "company" => "dashboard/company/opportunity/all",
            "commercial_agent" => ""
        ],
        "chat" => [
            "company" => "dashboard/company/chat/",
            "commercial_agent" => "dashboard/commercial-agent/chat/"
        ],
        "contract_all" => [
            "company" => "dashboard/company/contract/all",
            "commercial_agent" => "dashboard/commercial-agent/contract/all"
        ],
        "contract_ongoing" => [
            "company" => "dashboard/company/contract/ongoing",
            "commercial_agent" => "dashboard/commercial-agent/contract/ongoing"
        ],
        "contract_received" => [
            "company" => "dashboard/company/contract/received",
            "commercial_agent" => "dashboard/commercial-agent/contract/received"
        ],
        "contract_requested" => [
            "company" => "dashboard/company/contract/requested",
            "commercial_agent" => "dashboard/commercial-agent/contract/requested"
        ],
        "payments" => [
            "company" => "dashboard/company/payments/",
            "commercial_agent" => "dashboard/commercial-agent/payments/"
        ],
        "disputes" => [
            "company" => "dashboard/company/dispute/",
            "commercial_agent" => "dashboard/commercial-agent/dispute/"
        ],
        "commission" => [
            "company" => "dashboard/company/commission",
            "commercial_agent" => "dashboard/commercial-agent/commission"
        ],
        "reviews" => [
            "company" => "dashboard/company/reviews/",
            "commercial_agent" => "dashboard/commercial-agent/reviews/"
        ],
    ];

    if (isset($routes[$route_key][$key])) {
        return site_url($routes[$route_key][$key]);
    }

    return '';
}
