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

        $company = sanitize_text_field($entity_type == "company" ? $_POST['company_id'] : $_POST['company'][0]);
        $commercial_agent = sanitize_text_field($entity_type == "commercial_agent" ? $_POST['commercial_agent_id'] : $_POST['commercial_agent'][0]);
        $opportunity = sanitize_text_field($_POST['opportunity'][0]);
        $minimal_price = sanitize_text_field($_POST['minimal_price']);
        $commission = sanitize_text_field($_POST['commission']);
        $content = wp_kses_post($_POST['content']);

        // Validations
        if (empty($company)) {
            $errors['company'][] = 'Company is required.';
        }
        if (empty($commercial_agent)) {
            $errors['commercial_agent'][] = 'Commercial Agent is required.';
        }
        if (empty($opportunity)) {
            $errors['opportunity'][] = 'Opportunity is required.';
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

        $opportunity_post = get_post($opportunity);
        $company_post = get_post($company);
        $commercial_agent_post = get_post($commercial_agent);

        if (!$opportunity_post) {
            $general_errors[] = 'Opportunity not found.';
        }

        if (!$company_post) {
            $general_errors[] = 'Company not found.';
        }

        if (!$commercial_agent_post) {
            $general_errors[] = 'Commercial Agent not found.';
        }

        // Check if there are any general errors
        if (!empty($general_errors)) {
            wp_send_json_error([
                'general' => $general_errors,
            ]);
        }

        $args = [
            'post_type' => 'contract',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'opportunity',
                    'value' => $opportunity_post->ID,
                    'compare' => '=',
                ],
                [
                    'key' => 'company',
                    'value' => $company_post->ID,
                    'compare' => '=',
                ],
                [
                    'key' => 'commercial_agent',
                    'value' => $commercial_agent_post->ID,
                    'compare' => '=',
                ],
            ],
        ];

        $query = new WP_Query($args);

        if (is_wp_error($query)) {
            $general_errors[] = 'Failed to retrieve contracts.';
        }

        // Check if there are any general errors
        if (!empty($general_errors)) {
            wp_send_json_error([
                'general' => $general_errors,
            ]);
        }

        foreach ($query->posts as $item) {
            $status = carbon_get_post_meta($item->ID, 'status');

            if ($status == "pending") {
                $general_errors[] = "There is already a requested contract proposal. Accept or reject that proposal before starting another one.";
            } elseif ($status == "accepted") {
                $general_errors[] = "There is already an accepted contract proposal. Finish that proposal before starting another one.";
            }
        }

        // Check if there are any general errors
        if (!empty($general_errors)) {
            wp_send_json_error([
                'general' => $general_errors,
            ]);
        }
        $timestamp = current_time('timestamp'); // Obtiene el timestamp actual en segundos
        $sku = $opportunity_post->ID . "-" . $company_post->ID . "-" . $commercial_agent_post->ID . "-" . $timestamp;
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

        carbon_set_post_meta($contract_id, 'company', $company_post->ID);
        carbon_set_post_meta($contract_id, 'commercial_agent', $commercial_agent_post->ID);
        carbon_set_post_meta($contract_id, 'opportunity', $opportunity_post->ID);
        carbon_set_post_meta($contract_id, 'minimal_price', $minimal_price);
        carbon_set_post_meta($contract_id, 'commission', $commission);
        carbon_set_post_meta($contract_id, 'date', current_time("mysql"));
        carbon_set_post_meta($contract_id, "status_history", $status_history);
        carbon_set_post_meta($contract_id, "sku", $sku);
        carbon_set_post_meta($contract_id, "initiating_user", get_current_user_id());

        $this->send_contract_creation_email_to_agent($contract_id);
        $this->send_contract_creation_email_to_company($contract_id);

        wp_send_json_success(wp_get_current_user());
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
