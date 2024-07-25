<?php
class Agreement{

    private function __construct()
    {
    }
    private static $instance = null;
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function create_agreement()
    {
        check_ajax_referer('create_agreement_nonce', 'security');
    
        $entity_type = $_POST["entity_type"];
    
        $company = sanitize_text_field($entity_type == "company"?$_POST['company_id']:$_POST['company'][0]);
    
        $commercial_agent = sanitize_text_field($entity_type == "commercial_agent"?($_POST['commercial_agent_id']):$_POST['commercial_agent'][0]);
    
        $opportunity = sanitize_text_field($_POST['opportunity'][0]);
    
        $minimal_price = sanitize_text_field($_POST['minimal_price']);
    
        $commission = sanitize_text_field($_POST['commission']);
    
        $content = wp_kses_post($_POST['content']);
    
    
        if (!isset($company) || empty($company)) {
            wp_send_json_error('Company is required.');
        }
        if (!isset($commercial_agent) || empty($commercial_agent)) {
            wp_send_json_error('Commercial Agent is required.');
        }
        if (!isset($_POST['opportunity'][0]) || empty($_POST['opportunity'][0])) {
            wp_send_json_error('Opportunity is required.');
        }
        if (!isset($_POST['minimal_price']) || empty($_POST['minimal_price'])) {
            wp_send_json_error('Minimal Price is required.');
        }
        if (!isset($_POST['commission']) || empty($_POST['commission'])) {
            wp_send_json_error('Commission is required.');
        }
        
        $args = [
            'post_type'   => 'agreement',
            'meta_query'  => [
                'relation' => 'AND',
                [
                    'key'     => 'opportunity',
                    'value'   => $opportunity,
                    'compare' => '=',
                ],
                [
                    'key'     => 'company',
                    'value'   => $company,
                    'compare' => '=',
                ],
                [
                    'key'     => 'commercial_agent',
                    'value'   => $commercial_agent,
                    'compare' => '=',
                ]
            ],
        ];
    
        $query = new WP_Query($args);
    
        if (is_wp_error($query)) {
            wp_send_json_error('Failed to retrieve agreements.');
        }
       
     
        $status = [];
        foreach($query->posts as $item) {
            $status = carbon_get_post_meta($item->ID, 'status');
    
            if($status == "requested") {
                wp_send_json_error("There is already an requested agreement proposal. Accept or rejet that proposal before starting another one.");
            }
    
            if($status == "accepted") {
                wp_send_json_error("There is already an accepted agreement proposal. Finish that proposal before starting another one.");
            }
    
        }
    
       
        $opportunity_title = get_the_title($opportunity);
        $company_title = get_the_title($company);
        $commercial_agent_title = get_the_title($commercial_agent);
    
        $agreement_data = [
            'post_title' => "Opportunity: $opportunity_title, Company: $company_title, Commercial Agent: $commercial_agent_title",
            "post_content" => $content,
            'post_type' => 'agreement',
            'post_status' => 'publish'
        ];
    
        $agreement_id = wp_insert_post($agreement_data);
    
        if (is_wp_error($agreement_id)) {
            wp_send_json_error('Failed to save agreement.');
        }
        
        $status_history = $this->add_item_to_status_history($agreement_id);
    
        
     
    
        carbon_set_post_meta($agreement_id, 'company', $company);
        carbon_set_post_meta($agreement_id, 'commercial_agent', $commercial_agent);
        carbon_set_post_meta($agreement_id, 'opportunity', $opportunity);
        carbon_set_post_meta($agreement_id, 'minimal_price', $minimal_price);
        carbon_set_post_meta($agreement_id, 'commission', $commission);
        carbon_set_post_meta($agreement_id, 'date', current_time("mysql"));
        carbon_set_post_meta($agreement_id, "status_history", $status_history);
    
    
    
        wp_send_json_success('agreement saved successfully!');
    }


    public function update_agreement_status()
    {
        check_ajax_referer('update-status-agreement-nonce', 'security');

        $agreement_id = intval($_POST['agreement_id']);
        $new_status = sanitize_text_field($_POST['status']);
        $current_user = wp_get_current_user();

        if (!$agreement_id || !$new_status) {
            wp_send_json_error('Invalid request.');
        }

    

        if($new_status === "finished") {
            if (in_array('company', (array) $current_user->roles)) {
            
                $finalization_date = date('Y-m-d H:i:s', strtotime('+30 days'));
                $new_status = "finishing";
                carbon_set_post_meta($agreement_id, 'finalization_date', $finalization_date);
            
            }
        }
        $status_history = $this->add_item_to_status_history($agreement_id, $new_status);
        carbon_set_post_meta($agreement_id, 'status', $new_status);
        carbon_set_post_meta($agreement_id, 'status_history', $status_history);

        wp_send_json_success('Agreement status updated successfully.');
    }

    private function add_item_to_status_history($agreement_id, $status = "requested"){
        $status_history = get_post_meta($agreement_id, 'status_history', true);
        
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


}