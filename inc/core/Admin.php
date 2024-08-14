<?php


class Admin
{


    public function __construct()
    {
        
        add_action('admin_head', [$this, 'custom_admin_css_for_post_types']);
        $this->create_post_types();

        add_action('init', [$this, 'create_roles']);
       
     
        add_filter('use_block_editor_for_post_type', [$this,'disable_block_editor_for_post_type'], 10, 2);
    
    }

    public function create_roles()
    {
        $this->create_role_company();
        $this->create_role_commercial_agent();
    }

   
    public function create_post_types()
    {
        $CustomPostTypes = CustomPostType::get_instance();
        $CustomTaxonomy = CustomTaxonomy::get_instance();
        
       
        //Cpt
        $CustomPostTypes->register('opportunity', 'Opportunity', 'Opportunities', ['menu_icon' => 'dashicons-search']);
        $CustomPostTypes->register('review', 'Review', 'Reviews', ['menu_icon' => 'dashicons-star-empty']);
        $CustomPostTypes->register('company', 'Company', 'Companies', ['menu_icon' => 'dashicons-store']);
        $CustomPostTypes->register('commercial_agent', 'Commercial Agent', 'Commercial Agents', ['menu_icon' => 'dashicons-businessperson']);
        $CustomPostTypes->register('contract', 'Contract', 'Contracts', ['menu_icon' => 'dashicons-heart']);
        $CustomPostTypes->register('payment', 'Payment', 'Payments', ['menu_icon' => 'dashicons-money-alt']);
        $CustomPostTypes->register('commission_request', 'Commission Request', 'Commission Requests', ['menu_icon' => 'dashicons-bank']);
        $CustomPostTypes->register('dispute', 'Dispute', 'Disputes', ['menu_icon' => 'dashicons-warning']);
        $CustomPostTypes->register('deposit', 'Deposit', 'Deposits', ['menu_icon' => 'dashicons-money-alt']);
        //Taxonomies
        $CustomTaxonomy->register('skill', ['commercial_agent'], 'Skill', 'Skills');
        $CustomTaxonomy->register('selling_method', ['commercial_agent'], 'Selling Method', 'Selling Methods');
        $CustomTaxonomy->register('industry', ['commercial_agent','company',"opportunity"], 'Industry', 'Industries');
        $CustomTaxonomy->register('seller_type', ['commercial_agent'], 'Seller Type', 'Seller Types');
        $CustomTaxonomy->register('target_audience', ['opportunity'], 'Target Audience', 'Target Audiences');
        $CustomTaxonomy->register('gender', ['opportunity'], 'Gender', 'Genders');
        $CustomTaxonomy->register('age', ['opportunity'], 'Age', 'Ages');
        $CustomTaxonomy->register('activity', ['company'], 'Activity', 'Activities');
        $CustomTaxonomy->register('type_of_company', ["opportunity","company"], 'Company Type', 'Company Types');
        $CustomTaxonomy->register('location', ['company',"commercial_agent","opportunity"], 'Location', 'Locations');
        $CustomTaxonomy->register('language', ["commercial_agent","opportunity"], 'Language', 'Languages');
        $CustomTaxonomy->register('currency', ["opportunity"], 'Currency', 'Currencies');




    }

    public function create_role_company()
    {
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Comprobar si el rol ya está registrado
        if (!$wp_roles->get_role('company')) {
           
            $subscriber_caps = $wp_roles->get_role('subscriber')->capabilities;

            $wp_roles->add_role('company', __('Company'), $subscriber_caps);
        }
        $role = get_role('company');
        if ($role) {
            $role->remove_cap('read');
            $role->remove_cap('edit_posts');
            $role->remove_cap('delete_posts');
        }
    }

    public function create_role_commercial_agent()
    {
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
       
        if (!$wp_roles->get_role('commercial_agent')) {
            
            $subscriber_caps = $wp_roles->get_role('subscriber')->capabilities;

            $wp_roles->add_role('commercial_agent', __('Commercial Agent'), $subscriber_caps);
        }
    }





   

    public function get_contract_status()
    {
        return [
            'pending' => 'Pending',
            "accepted" => "Accepted",
            "refused" => "Refused",
            "finishing" => "Finishing",
            'finished' => 'Finished',
        ];
    }

    public function get_status_commission_request()
    {
        return [
            'pending' => 'Pending',
            'dispute_pending' => 'Dispute Pending',
            'dispute_accepted' => 'Dispute Accepted',
            'dispute_refused' => 'Dispute Refused',
            'dispute_cancelled' => 'Dispute Cancelled',
            'payment_pending' => 'Pending Payment',
            'payment_completed' => 'Payment Completed',
            'payment_failed' => 'Payment Failed',
            'payment_canceled' => 'Payment Canceled',
        ];
    }

    public function get_statuses_dispute()
    {
        return [
            'dispute_pending' => 'Dispute Pending',
            'dispute_accepted' => 'Dispute Accepted',
            'dispute_refused' => 'Dispute Refused',
            'dispute_cancelled' => 'Dispute Cancelled'
        ];
    }

    public function get_status_payment()
    {
        return [
            'payment_pending' => 'Payment Pending',
            'payment_completed' => 'Payment Completed',
            'payment_failed' => 'Payment Failed',
            'payment_canceled' => 'Payment Canceled'
        ];
    }
    /*public function get_target_audiences()
    {
        return [
            'companies' => 'Companies',
            'individual' => 'Individual',
        ];
    }
    public function get_ages()
    {
        return [
            'over_18' => 'Over 18',
            'over_30' => 'Over 30',
            'over_60' => 'Over 60',
            'any_age' => 'Any Age',
        ];
    }
    public function get_genders()
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'any_gender' => 'Any Gender',
        ];
    }*/
    public function get_agent_users()
    {
        $users = get_users(['role__in' => ['commercial_agent'],]);
    
        $options = [""=>"Select an User"];
    
        if (!empty($users)) {
            
            foreach ($users as $user) {
                $options[$user->ID] =  $user->first_name . " " .$user->last_name;
            }
        }
    
        return $options;
    }


    public function get_users()
    {
        $users = get_users(['role__in' => ['commercial_agent', 'company'],]);
    
        $options = [""=>"Select an User"];
    
        if (!empty($users)) {
            
            foreach ($users as $user) {
                $options[$user->ID] = $user->first_name . " " .$user->last_name;
            }
        }
    
        return $options;
    }

    
    public function get_company_users()
    {
        $users = get_users(['role__in' => ['company'],]);
    
        $options = [""=>"Select an User"];
    
        if (!empty($users)) {
            
            foreach ($users as $user) {
                $options[$user->ID] = $user->first_name . " " .$user->last_name;
            }
        }
    
        return $options;
    }

  
   

    
  
 
   

  
    public function get_opportunities()
    {
        $opportunities = get_posts([
            'post_type' => 'opportunity',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($opportunities)) {
            $options[""]="Select a Opportunity";
            foreach ($opportunities as $opportunity) {
                $options[$opportunity->ID] = $opportunity->post_title;
            }
        }
    
        return $options;
    }

    public function get_commission_requests()
    {
        $commission_requests = get_posts([
            'post_type' => 'commission_request',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($commission_requests)) {
            $options[""]="Select a Commission Request";
            foreach ($commission_requests as $commission_request) {
                $options[$commission_request->ID] = $commission_request->post_title;
            }
        }
    
        return $options;
    }

    
    public function get_companies()
    {
        $companies = get_posts([
            'post_type' => 'company',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($companies)) {
            $options[""]="Select a Company";
            foreach ($companies as $company) {
                $options[$company->ID] = $company->post_title;
            }
        }
    
        return $options;
    }

    public function get_payments()
    {
        $payments = get_posts([
            'post_type' => 'payment',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($payments)) {
            $options[""]="Select a payment";
            foreach ($payments as $payment) {
                $options[$payment->ID] = $payment->post_title;
            }
        }
    
        return $options;
    }

    public function get_deposits()
    {
        $deposits = get_posts([
            'post_type' => 'deposit',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($deposits)) {
            $options[""]="Select a Deposit";
            foreach ($deposits as $deposit) {
                $options[$deposit->ID] = $deposit->post_title;
            }
        }
    
        return $options;
    }

    public function get_contracts()
    {
        $contracts = get_posts([
            'post_type' => 'contract',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($contracts)) {
            $options[""]="Select a contract";
            foreach ($contracts as $contract) {
                $options[$contract->ID] = $contract->post_title;
            }
        }
    
        return $options;
    }

    public function get_commercial_agents()
    {
        $agents = get_posts([
            'post_type' => 'commercial_agent',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($agents)) {
            $options[""]="Select a Commercial Agent";
            foreach ($agents as $agent) {
                $options[$agent->ID] = $agent->post_title;
            }
        }
    
        return $options;
    }

   

    public function custom_admin_css_for_post_types()
    {
        global $typenow;

        // Lista de tipos de publicaciones personalizadas
        $CustomPostTypes = ['opportunity', 'review', 'contract', "company", "commercial_agent", "payment","dispute","commission_request"];

        // Verificar si el tipo de publicación actual está en la lista
        if (in_array($typenow, $CustomPostTypes)) {
            echo '
            <style>
                /* Ejemplo de CSS personalizado */
                .edit-post-meta-boxes-area__container{
                    max-width: 840px;
                    margin:0 auto;
                }
                .postbox-header{
                    border: solid #e2e4e7;
                    border-width: 0 1px 0 1px;
                }
            </style>
            ';
        }
    }
    public function disable_block_editor_for_post_type($use_block_editor, $post_type)
    {
        // Define los post types donde quieres deshabilitar el editor de bloques
        $post_types = ['commercial_agent', 'company',"contract","opportunity","review","dispute","payment","deposit"]; // Reemplaza con tus post types
    
        // Comprueba si el post type actual está en la lista definida
        if (in_array($post_type, $post_types)) {
            return false; // Usa el editor clásico (TinyMCE) para estos post types
        }
    
        return $use_block_editor; // Usa el editor de bloques para todos los demás post types
    }
  

    

}
