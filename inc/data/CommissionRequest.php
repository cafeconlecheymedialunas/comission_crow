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
        
        $errors = new WP_Error();
        $general_errors = []; // Array for general errors
        $item_errors = []; // Array for item-specific errors
        
        // Sanitize and validate form data
        $contract_id = sanitize_text_field($_POST['contract_id']);
        $general_invoice = sanitize_text_field($_POST["general_invoice"]);
        $comments = wp_kses_post($_POST["comments"]);
        
        // Validate contract_id
        if (empty($contract_id)) {
            $general_errors[] = 'Contract ID is required.';
        }
    
    
        // Validate current user
        $current_user = wp_get_current_user();
        if (!in_array("commercial_agent", $current_user->roles)) {
            $general_errors[] = 'You must be a commercial agent to create a commission request.';
        }
        
        // Validate contract existence
        $contract = get_post($contract_id);
        if (!$contract) {
            $general_errors[] = 'The specified contract does not exist.';
        }
        
        // Verify the status of the contract
        $status = carbon_get_post_meta($contract_id, "status");
        if ($status === "pending" || $status === "refused") {
            $general_errors[] = 'The contract is either pending or refused.';
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
            $general_errors[] = 'A commission request already exists for this contract.';
        }
    
        // Check if there are any general errors and send them in response
       
    
        // Prepare and update Carbon Fields
        $items = [];
        $total = 0;
        $commission = carbon_get_post_meta($contract_id, "commission");
    
        // Validate item data
        if (isset($_POST['price']) && is_array($_POST['price'])) {
            foreach ($_POST['price'] as $index => $price) {
                $price_paid = floatval(sanitize_text_field($price));
                $quantity = intval(sanitize_text_field($_POST['quantity'][$index]));
                $detail = sanitize_text_field($_POST['detail'][$index]);
                $invoice = sanitize_text_field($_POST['invoice'][$index]);
                $item_errors[$index] = []; // Initialize item-specific errors array
    
                // Validate item data
                if ($price_paid <= 0) {
                    $item_errors[$index][] = 'Price must be a positive number.';
                }
                if ($quantity <= 0) {
                    $item_errors[$index][] = 'Quantity must be a positive integer.';
                }
                if (empty($detail)) {
                    $item_errors[$index][] = 'Detail is required.';
                }
                if (empty($invoice)) {
                    $item_errors[$index][] = 'Invoice number is required.';
                }
    
                if (empty($item_errors[$index])) {
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
        }
    
        // Check if there are any item-specific errors and send them in response
        if (!empty($item_errors)) {
            wp_send_json_error(['items' => $item_errors]);
            wp_die();
        }
    
        $total_agent = ($commission * $total) / 100;
        $status_history = add_item_to_status_history($contract_id);
        $post_id = wp_insert_post([
            'post_type' => 'commission_request',
            'post_status' => 'publish',
            'post_title' => 'Commission Request by contract' . $contract_id, // Customize the title as needed
        ]);

        
      

    
        if (is_wp_error($post_id)) {
            $general_errors[] = 'Could not create the commission request.';
        }

        if (!empty($general_errors)) {
            wp_send_json_error(['general' => $general_errors]);
            wp_die();
        }
    
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
        wp_die();
    }
    
    
    
    /* Cron para chequear la finalizacion de un contrato
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
*/



}
