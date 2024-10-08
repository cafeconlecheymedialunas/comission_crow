<?php
class Contract
{

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

    public function create_contract()
    {
        check_ajax_referer('create_contract_nonce', 'security');

        $errors = [];
        $general_errors = [];

        $entity_type = sanitize_text_field($_POST["entity_type"]);

        $company_id = sanitize_text_field($_POST['company_id']);
        $commercial_agent_id = sanitize_text_field($_POST['commercial_agent_id']);
        $opportunity_id = sanitize_text_field($_POST['opportunity_id']);
        $minimal_price = sanitize_text_field($_POST['minimal_price']);
        $commission = sanitize_text_field($_POST['commission']);
        $content = wp_kses_post($_POST['content']);
        
        // Validations
        if (empty($company_id)) {
            $errors['company_id'][] = 'Company is required.';
        }
        if (empty($commercial_agent_id)) {
            $errors['commercial_agent_id'][] = 'Commercial Agent is required.';
        }
        if (empty($opportunity_id)) {
            $errors['opportunity_id'][] = 'Opportunity is required.';
        }
        if (empty($minimal_price)) {
            $errors['minimal_price'][] = 'Minimal Price is required.';
        }
        if (empty($commission)) {
            $errors['commission'][] = 'Commission is required.';
        }

        if (!is_numeric($minimal_price)) {
            $errors["price"][] = __("Price must be a number.");
        }

        if (!is_numeric($commission)) {
            $errors["commission"][] = __("Commission must be a number.");
        } elseif ($commission <= 0 || $commission > 100) {
            $errors["commission"][] = __("Commission must be between 1 and 100.");
        }

        // Check if there are any validation errors
        if (!empty($errors)) {
            wp_send_json_error([
                'fields' => $errors,
            ]);
        }

        $opportunity = get_post($opportunity_id);
        $company = get_post($company_id);
        $commercial_agent = get_post($commercial_agent_id);

        if (!$opportunity) {
            wp_send_json_error(["general"=>'Opportunity not found.']);
        }

        if (!$company) {
            wp_send_json_error(["general"=>'Company not found.']);
        }

        if (!$commercial_agent) {
            wp_send_json_error(["general"=>'Commercial Agent not found.']);
        }

        $opportunity_price = carbon_get_post_meta($opportunity_id,"price");

        if($minimal_price < $opportunity_price ){
            wp_send_json_error(["general"=>'Minimal price cannot be less than the average deal value of the opportunity.']);
        }

      

        $args = [
            'post_type' => 'contract',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'opportunity',
                    'value' => $opportunity->ID,
                    'compare' => '=',
                ],
                [
                    'key' => 'company',
                    'value' => $company->ID,
                    'compare' => '=',
                ],
                [
                    'key' => 'commercial_agent',
                    'value' => $commercial_agent->ID,
                    'compare' => '=',
                ],
            ],
        ];

        $query = new WP_Query($args);

        if (is_wp_error($query)) {
            wp_send_json_error(["general"=> 'Failed to retrieve contracts.']);
        }

        foreach ($query->posts as $item) {
            $status = carbon_get_post_meta($item->ID, 'status');

            if ($status == "pending") {
                wp_send_json_error(["general"=> "There is already a requested contract proposal. Accept or reject that proposal before starting another one."]);
            } elseif ($status == "accepted") {
                wp_send_json_error(["general"=>"There is already an accepted contract proposal. Finish that proposal before starting another one."]);
            }
        }

        // Check if there are any general errors
        if (!empty($general_errors)) {
            wp_send_json_error([
                'general' => $general_errors,
            ]);
        }
        $timestamp = current_time('timestamp'); // Obtiene el timestamp actual en segundos
        $sku = $opportunity->ID . "-" . $company->ID . "-" . $commercial_agent->ID . "-" . $timestamp;
        $contract_data = [
            'post_title' => "Sku: #" . $sku,
            'post_content' => $content,
            'post_type' => 'contract',
            'post_status' => 'publish',
        ];

        $contract_id = wp_insert_post($contract_data);

        if (is_wp_error($contract_id)) {
            $general_errors[] = 'Failed to save contract.';
            wp_send_json_error([
                'general' => $general_errors,
            ]);
        }

        $status_history = Helper::add_item_to_status_history($contract_id);
        $minimal_price  = Helper::convert_to_usd($minimal_price);
        if (is_wp_error($minimal_price)) {
          
            wp_send_json_error([
                'general' => $minimal_price->get_error_message(),
            ]);
        }
        carbon_set_post_meta($contract_id, 'company', $company->ID);
        carbon_set_post_meta($contract_id, 'commercial_agent', $commercial_agent->ID);
        carbon_set_post_meta($contract_id, 'opportunity', $opportunity->ID);
        carbon_set_post_meta($contract_id, 'minimal_price', $minimal_price);
        carbon_set_post_meta($contract_id, 'commission', $commission);
        carbon_set_post_meta($contract_id, 'date', current_time("mysql"));
        carbon_set_post_meta($contract_id, "status_history", $status_history);
        carbon_set_post_meta($contract_id, "sku", $sku);
        carbon_set_post_meta($contract_id, "initiating_user", get_current_user_id());

        $this->send_contract_creation_email_to_agent($contract_id);
        $this->send_contract_creation_email_to_company($contract_id);
        $user = wp_get_current_user();
        $role = $user->roles[0];
        $role = $role === "commercial_agent" ? "commercial-agent" : "company";

        $redirect_url = site_url("dashboard/$role/contract/all");

        wp_send_json_success(["redirect_url" => $redirect_url]);
    }

    public function send_contract_creation_email_to_agent($contract_id)
    {
        $commercial_agent_id = carbon_get_post_meta($contract_id, 'commercial_agent');
        $company_id = carbon_get_post_meta($contract_id, 'company');
        $company_name = carbon_get_post_meta($company_id, 'company_name');
        $user_id = carbon_get_post_meta($commercial_agent_id, 'user');
        $user = get_user_by('ID', $user_id);
        $sku = carbon_get_post_meta($contract_id, 'sku');
    
        $email_sender = new EmailSender();
        $to = $user->user_email;
        $subject = 'New Contract Created';
        $message = "<p>Hello, {$user->first_name},</p>
            <p>A new contract has been created with the following details:</p>
            <p><strong>Company:</strong> {$company_name}</p>
            <p><strong>Contract SKU:</strong> {$sku}</p>
            <p>Thank you for your continued partnership.</p>";
    
        $sent = $email_sender->send_email($to, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to agent: ' . $error_message);
            }
        }
    }
    

    public function send_contract_creation_email_to_company($contract_id)
    {
        $company_id = carbon_get_post_meta($contract_id, 'company');
        $user_company_id = carbon_get_post_meta($company_id, 'user');
        $user_company = get_user_by('ID', $user_company_id);
        $commercial_agent_id = carbon_get_post_meta($contract_id, 'commercial_agent');
        $user_agent_id = carbon_get_post_meta($commercial_agent_id, 'user');
        $user_agent = get_user_by('ID', $user_agent_id);
        $sku = carbon_get_post_meta($contract_id, 'sku');
    
        $email_sender = new EmailSender();
        $to = $user_company->user_email;
        $subject = 'New Contract Created';
        $message = "<p>Hello, {$user_company->first_name},</p>
            <p>A new contract has been created with the following details:</p>
            <p><strong>Agent:</strong> {$user_agent->first_name} {$user_agent->last_name}</p>
            <p><strong>Contract SKU:</strong> {$sku}</p>
            <p>Thank you for your continued partnership.</p>";
    
        $sent = $email_sender->send_email($to, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to company: ' . $error_message);
            }
        }
    }
    
    public function update_contract_status()
    {
        check_ajax_referer('update-status-contract-nonce', 'security');
    
        $contract_id = intval($_POST['contract_id']);
        $new_status = sanitize_text_field($_POST['status']);
    
        $current_user = wp_get_current_user();
    
        if (!$contract_id || !$new_status) {
            wp_send_json_error('Invalid request.');
        }
    
        // Initialize finalization date
        $finalization_date = '';
    
        if ($new_status === "finished") {
            if (in_array('company', (array) $current_user->roles)) {
                $finalization_date = date('Y-m-d H:i:s', strtotime('+30 days'));
                $new_status = "finishing";
                carbon_set_post_meta($contract_id, 'finalization_date', $finalization_date);
            }
        }
    
        $status_history = Helper::add_item_to_status_history($contract_id, $new_status);
        carbon_set_post_meta($contract_id, 'status', $new_status);
        carbon_set_post_meta($contract_id, 'status_history', $status_history);
    
        $company_email = carbon_get_post_meta($contract_id, 'company_email'); // Correo de la compañía
        $commercial_agent_id = carbon_get_post_meta($contract_id, 'commercial_agent');
        $commercial_agent_user = get_user_by('ID', $commercial_agent_id);
        $agent_email = $commercial_agent_user->user_email;
        $sku = carbon_get_post_meta($contract_id, 'sku');
    
        switch ($new_status) {
            case 'accepted':
                $this->send_email_to_agent($agent_email, $sku, 'accepted');
                $this->send_email_to_company($company_email, $sku, 'accepted');
                break;
    
            case 'rejected':
                $this->send_email_to_agent($agent_email, $sku, 'rejected');
                $this->send_email_to_company($company_email, $sku, 'rejected');
                break;
    
            case 'finished':
                $this->send_email_to_agent($agent_email, $sku, 'finished');
                $this->send_email_to_company($company_email, $sku, 'finished');
                break;
    
            case 'finishing':
                $this->send_email_to_agent($agent_email, $sku, 'finishing');
                $this->send_email_to_company($company_email, $sku, $finalization_date, 'finishing');
                break;
    
            default:
                break;
        }
    
        wp_send_json_success('Contract status updated successfully.');
    }
    
    public function schedule_daily_contract_check() {
        if (!wp_next_scheduled('daily_contract_check_event')) {
            wp_schedule_event(time(), 'daily', 'daily_contract_check_event');
        }
    }
   
    
    public function check_and_update_contracts() {
        $args = array(
            'post_type' => 'contract',
            'meta_query' => array(
                array(
                    'key' => 'finalization_date',
                    'value' => current_time('Y-m-d'),
                    'compare' => '<=',
                    'type' => 'DATE'
                ),
                array(
                    'key' => 'status',
                    'value' => 'finishing',
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        );
    
        $contracts = get_posts($args);
    
        foreach ($contracts as $contract) {
            carbon_set_post_meta($contract->ID, 'status', 'finished');
            $company_id = carbon_get_post_meta($contract->ID,"company");
            $user_company_id = get_post_meta($company_id,"_user_id")[0];
            $user_company = get_user_by("ID",$user_company_id);


            $commercial_agent_id = carbon_get_post_meta($contract->ID,"commercial_agent");
            $user_commercial_agent_id = get_post_meta($commercial_agent_id,"_user_id")[0];
            $user_commercial_agent = get_user_by("ID",$user_commercial_agent_id);

            $sku = carbon_get_post_meta($contract->ID,"sku");                
                               
                   
          

            $this->send_email_to_agent($user_commercial_agent->user_email, $sku, 'finished');
            $this->send_email_to_company($user_company->user_email, $sku, 'finished');
        }
    }
   
    

    private function send_email_to_agent($agent_email, $sku, $status)
    {
        $email_sender = new EmailSender();
        $subject = "Contract Status Update: {$status}";
        $message = "<p>Hello,</p>
            <p>Your contract (SKU: $sku) has been $status.</p>
            <p>Thank you for your attention.</p>";
    
        $sent = $email_sender->send_email($agent_email, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to agent: ' . $error_message);
            }
        }
        return $sent;
    }
    

    private function send_email_to_company($company_email, $sku, $status, $finalization_date = "")
    {
        $email_sender = new EmailSender();
        $finalization_text_date = ($status === "finishing" && $finalization_date) ? "at " . Helper::get_human_time_diff($finalization_date) : "";
        $subject = "Contract Status Update: $status";
        $message = "<p>Hello,</p>
            <p>The contract (SKU: $sku) has been $status $finalization_text_date.</p>
            <p>Thank you for your attention.</p>";
    
        $sent = $email_sender->send_email($company_email, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to company: ' . $error_message);
            }
        }
    
        return $sent;
    }
    
    
}


