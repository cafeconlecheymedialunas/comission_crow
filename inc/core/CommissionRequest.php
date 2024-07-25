<?php
class Commissionrequest{

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

    

    public function create_commission_request() {
        // Verificar nonce para la seguridad
        check_ajax_referer('create_commission_request_nonce', 'security');
    
        // Verificar si el agreement_id está establecido
        $agreement_id = isset($_POST['agreement_id']) ? intval($_POST['agreement_id']) : 0;

        $current_user = wp_get_current_user();
        if(!in_array("commercial_agent",$current_user->roles)){
            wp_send_json_error("There is already a commission request");
        }

        $agreement = get_post($agreement_id);
    
        if(!$agreement){
            wp_send_json_error("Can not get this post");
        }

        $status = carbon_get_post_meta($agreement_id,"status");

        if($status === "accepted" || $status === "refused"){
            wp_send_json_error("An order already exists");
        }


        $query = new WP_Query([
            'post_type'  => 'commission_request',
            'meta_query' => [
                [
                    'key'   => 'agreement_id',
                    'value' => $agreement_id,
                    'compare' => '=', // Comparar el valor exacto
                ]
            ]
        ]);

       $commission_requests =  $query->posts;


       
       if(!empty($commission_requests)){
            wp_send_json_error("There is already a commission request");
       }

        $post_id = wp_insert_post([
            'post_type'   => 'commission_request',
            'post_status' => 'publish',
            'post_title'  => 'Commission Request ' . $agreement_id, // Personalizar el título según sea necesario
        ]);
    
        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => 'Failed to create post']);
        }
    
        // Preparar y actualizar los campos de Carbon Fields
        $items = [];
        $total = 0;
    
        if (isset($_POST['price']) && is_array($_POST['price'])) {
            foreach ($_POST['price'] as $index => $price) {
                $price_paid = floatval(sanitize_text_field($price));
                $quantity = intval(sanitize_text_field($_POST['quantity'][$index]));
                $subtotal = $price_paid * $quantity;
                $total += $subtotal;
    
                $items[] = [
                    'price_paid' => $price_paid,
                    'quantity'   => $quantity,
                    'subtotal'   => $subtotal,
                ];
            }
        }
    
    
        carbon_set_post_meta($post_id, 'agreement_id', $agreement_id);
        carbon_set_post_meta($post_id, 'items', $items);
        carbon_set_post_meta($post_id, 'total', $total);
        carbon_set_post_meta($post_id,"date",current_time('mysql'));
    
        wp_send_json_success(['message' => 'Commission request created successfully']);
    }
    



}