<?php
class Dashboard
{

    public function __construct()
    {
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('init', [$this, 'custom_rewrite_rules']);
        $this->register_ajax_managers();
    }

    public function custom_rewrite_rules()
    {
        add_rewrite_rule('^dashboard/([^/]+)(/.*)?/?$', 'index.php?pagename=dashboard&role=$matches[1]&subpages=$matches[2]', 'top');
    }

    public function add_query_vars($vars)
    {
        $vars[] = 'role';
        $vars[] = 'subpages';
        return $vars;
    }

    public function register_ajax_managers()
    {

        $company = Company::get_instance();
        $commercial_agent = CommercialAgent::get_instance();
        $profileUser = ProfileUser::get_instance();
        $contract = Contract::get_instance();
        $commission_request = Commissionrequest::get_instance();
        $dispute = Dispute::get_instance();
        $opportunity = Opportunity::get_instance();
        $payment = Payment::get_instance();
        $deposit = Deposit::get_instance();

        add_action('wp_ajax_create_opportunity', [$opportunity, 'save_opportunity']);
        add_action('wp_ajax_nopriv_create_opportunity', [$opportunity, 'save_opportunity']);

        add_action('wp_ajax_delete_opportunity', [$opportunity, 'delete_opportunity']);
        add_action('wp_ajax_nopriv_delete_opportunity', [$opportunity, 'delete_opportunity']);
        add_action('wp_ajax_save_agent_profile', [$commercial_agent, 'save_agent_profile']);
        add_action('wp_ajax_nopriv_save_agent_profile', [$commercial_agent, 'save_agent_profile']);

        add_action('wp_ajax_save_company_profile', [$company, 'save_company_profile']);
        add_action('wp_ajax_nopriv_save_company_profile', [$company, 'save_company_profile']);

        add_action('wp_ajax_update_user_data', [$profileUser, 'update_user_data']);
        add_action('wp_ajax_nopriv_update_user_data', [$profileUser, 'update_user_data']);

        
        add_action('wp_ajax_update_stripe_email', [$profileUser,  'update_stripe_email']);
        add_action('wp_ajax_nopriv_update_stripe_email', [$profileUser, 'update_stripe_email']);

        add_action('wp_ajax_create_contract', [$contract, 'create_contract']);
        add_action('wp_ajax_nopriv_create_contract', [$contract, 'create_contract']);

        add_action('wp_ajax_update_contract_status', [$contract, 'update_contract_status']);
        add_action('wp_ajax_nopriv_update_contract_status', [$contract, 'update_contract_status']);

        add_action('wp_ajax_create_commission_request', [$commission_request, 'create_commission_request']);
        add_action('wp_ajax_nopriv_create_commission_request', [$commission_request, 'create_commission_request']);

        add_action('wp_ajax_delete_commission_request', [$commission_request, 'delete_commission_request']);
        add_action('wp_ajax_nopriv_delete_commission_request', [$commission_request, 'delete_commission_request']);


        add_action('wp_ajax_withdraw_founds', [$deposit,'withdraw_founds']);

        //TO DO

        add_action('wp_ajax_create_dispute', [$dispute, 'handle_create_dispute']);
        add_action('wp_ajax_nopriv_create_dispute', [$dispute, 'handle_create_dispute']);
        add_action('wp_ajax_delete_dispute', [$dispute, 'delete_dispute']);
        add_action('wp_ajax_nopriv_delete_dispute', [$dispute, 'delete_dispute']);
        
        add_action('admin_post_nopriv_create_payment', [$payment , 'create_payment']);
        add_action('admin_post_create_payment', [$payment ,'create_payment']);
       
      

    }

    public function get_role_url_link_dashboard_page($route_key)
    {
        $current_user = wp_get_current_user();
        $key = in_array("commercial_agent", $current_user->roles) ? "commercial_agent" : "company";

        $routes = [
            "dashboard" => [
                "commercial_agent" => "dashboard/commercial-agent/dashboard/",
            ],
            "profile" => [
                "company" => "dashboard/company/profile/",
                "commercial_agent" => "dashboard/commercial-agent/profile/",
            ],
            "opportunity_create" => [
                "company" => "dashboard/company/opportunity/create",
                "commercial_agent" => "",
            ],
            "opportunity_list" => [
                "company" => "dashboard/company/opportunity/all",
                "commercial_agent" => "",
            ],
            "chat" => [
                "company" => "dashboard/company/chat/",
                "commercial_agent" => "dashboard/commercial-agent/chat/",
            ],
            "contract_all" => [
                "company" => "dashboard/company/contract/all",
                "commercial_agent" => "dashboard/commercial-agent/contract/all",
            ],
            "contract_ongoing" => [
                "company" => "dashboard/company/contract/ongoing",
                "commercial_agent" => "dashboard/commercial-agent/contract/ongoing",
            ],
            "contract_received" => [
                "company" => "dashboard/company/contract/received",
                "commercial_agent" => "dashboard/commercial-agent/contract/received",
            ],
            "contract_requested" => [
                "company" => "dashboard/company/contract/requested",
                "commercial_agent" => "dashboard/commercial-agent/contract/requested",
            ],
            "payment_list" => [
                "company" => "dashboard/company/payment/all",
            ],
            "payment_create" => [
                "company" => "dashboard/company/payment/create",
            ],
            "payment_show" => [
                "company" => "dashboard/company/payment/show",
            ],
            "deposit_list" =>[
                "commercial_agent" => "dashboard/commercial-agent/deposit/all",
            ],
            "disputes" => [
                "company" => "dashboard/company/dispute/",
                "commercial_agent" => "dashboard/commercial-agent/dispute/",
            ],
            "commission" => [
                "company" => "dashboard/company/commission",
                "commercial_agent" => "dashboard/commercial-agent/commission",
            ],
            "reviews" => [
                "company" => "dashboard/company/review/",
                "commercial_agent" => "dashboard/commercial-agent/review/",
            ],
        ];

        if (isset($routes[$route_key][$key])) {
            return site_url($routes[$route_key][$key]);
        }

        return '';
    }

}
