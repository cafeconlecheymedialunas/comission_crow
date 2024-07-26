<?php
class Commissionrequest
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

    

    public function create_commission_request()
    {
        // Verify nonce for security
        check_ajax_referer('create_commission_request_nonce', 'security');
    
        // Sanitize and validate form data
        $contract_id = $_POST['contract_id'];
        $general_invoice = sanitize_text_field($_POST["general_invoice"]);
        $comments = wp_kses_post($_POST["comments"]);
    
        $current_user = wp_get_current_user();
        if (!in_array("commercial_agent", $current_user->roles)) {
            wp_send_json_error("You must be a commercial agent to create a commission request.");
        }
    
        $contract = get_post($contract_id);
    
        if (!$contract) {
            wp_send_json_error("The specified contract does not exist.");
        }
    
        // Verify the status of the contract
        $status = carbon_get_post_meta($contract_id, "status");
        if ($status === "pending" || $status === "refused") {
            wp_send_json_error("The contract is either pending or refused.");
        }
    
        // Check if a commission request already exists for this contract
        $query = new WP_Query([
            'post_type' => 'commission_request',
            'meta_query' => [
                [
                    'key' => 'contract_id',
                    'value' => $contract_id,
                    'compare' => '=', // Compare exact value
                ]
            ]
        ]);
        $commission_requests = $query->posts;
    
        if (!empty($commission_requests)) {
            wp_send_json_error("A commission request already exists for this contract.");
        }
    
        // Create the commission request post
        $post_id = wp_insert_post([
            'post_type' => 'commission_request',
            'post_status' => 'publish',
            'post_title' => 'Commission Request ' . $contract_id, // Customize the title as needed
        ]);
    
        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => 'Could not create the commission request.']);
        }
    
        // Prepare and update Carbon Fields
        $items = [];
        $total = 0;
        $commission = carbon_get_post_meta($contract_id, "commission");
    
        if (isset($_POST['price']) && is_array($_POST['price'])) {
            foreach ($_POST['price'] as $index => $price) {
                $price_paid = floatval(sanitize_text_field($price));
                $quantity = intval(sanitize_text_field($_POST['quantity'][$index]));
                $detail = sanitize_text_field($_POST['detail'][$index]);
                $invoice = sanitize_text_field($_POST['invoice'][$index]);
                $subtotal = $price_paid * $quantity;
                $total += $subtotal;
    
                $items[] = [
                    'price_paid' => $price_paid,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'detail' => $detail,
                    'invoice' => $invoice
                ];
            }
        }
    
        $total_agent = ($commission * $total) / 100;
        $status_history = add_item_to_status_history($contract_id);
        carbon_set_post_meta($post_id, 'contract_id', $contract_id);
        carbon_set_post_meta($post_id, 'general_invoice', $general_invoice);
        carbon_set_post_meta($post_id, 'items', $items);
        carbon_set_post_meta($post_id, 'total_cart', $total);
        carbon_set_post_meta($post_id, 'total_agent', $total_agent);
        carbon_set_post_meta($post_id, "date", current_time('mysql'));
        carbon_set_post_meta($post_id, "comments", $comments);
        carbon_set_post_meta($post_id, "status", "pending");
        carbon_set_post_meta($post_id, "status_history", $status_history);
        wp_send_json_success(['message' => 'Commission request successfully created.']);
    }
    
    
    
    



}
