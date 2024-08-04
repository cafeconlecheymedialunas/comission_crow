<?php
class Dispute
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

   

  

    public function handle_create_dispute()
    {
        // Verify nonce for security
        check_ajax_referer('create_dispute_nonce', 'security');
    
        $subject = sanitize_textarea_field($_POST['subject']);
        $description = sanitize_textarea_field($_POST['description']);
    
        $errors = [];
        $general_errors = [];
    
        // Validar commission_request_id
        $commission_request_id = intval($_POST['commission_request_id']);
        $commission_request = get_post($commission_request_id);


    
        if (!$commission_request) {
            $general_errors[] = 'Commission Request not found.';
            wp_send_json_error(['general' => $general_errors]);
            wp_die();
        }

        $dispute_query = new WP_Query([
            'post_type'  => 'dispute',
            'meta_query' => [
                [
                    'key'   => "commission_request_id",
                    'value' => $commission_request_id,
                    'compare' => '=',
                ]
            ]
        ]);

        if($dispute_query->have_posts()) {
            $general_errors[] = 'There is already a dispute with this contract';
            wp_send_json_error(['general' => $general_errors]);
            wp_die();
        }
    
        // Validar description
        if (empty($_POST['description'])) {
            $errors['description'][] = 'Description is required.';
        }
    
        // Validar subject
        if (empty($_POST['subject'])) {
            $errors['subject'][] = 'Subject is required.';
        }
    
        if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
            $validation_result = validate_files($_FILES['documents']);
            if (isset($validation_result['error'])) {
                $errors['documents'][] = $validation_result['error'];
            }
        }
    
        // Si hay errores de validación, devolverlos
        if (!empty($errors)) {
            wp_send_json_error(['fields' => $errors]);
            wp_die();
        }
    
        // Validar y manejar la carga de documentos si existen
        if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
        
            $uploads = handle_multiple_file_upload($_FILES['documents']);
            if (isset($uploads['error'])) {
                $general_errors[] = $uploads['error'];
                wp_send_json_error(['general' => $errors]);
                wp_die();
            }
        }
    
        // Crear el post de dispute
        $post_id = wp_insert_post([
            'post_type'   => 'dispute',
            'post_status' => 'publish',
            'post_title'  => 'Dispute related to request ' . $commission_request_id
        ]);
    
        // Check for errors
        if (is_wp_error($post_id)) {
            $general_errors[] = 'Could not create dispute.';
            wp_send_json_error(['general' => $general_errors]);
            wp_die();
        }
    
        // Set post meta
        $status_history = Helper::add_item_to_status_history($post_id);
        carbon_set_post_meta($post_id, 'commission_request_id', $commission_request_id);
        carbon_set_post_meta($post_id, 'description', $description);
        carbon_set_post_meta($post_id, 'subject', $subject);
        carbon_set_post_meta($post_id, 'status', "pending");
        carbon_set_post_meta($post_id, 'initiating_user', get_current_user_id());
        carbon_set_post_meta($post_id, 'status_history', $status_history);
        carbon_set_post_meta($post_id, 'date', current_time("mysql"));

        // Updatge Commission Request to in_DISPUTE
        $status_commision_request_history = Helper::add_item_to_status_history($commission_request_id, "in_dispute");
        carbon_set_post_meta($commission_request_id, 'status_history', $status_commision_request_history);
        carbon_set_post_meta($commission_request_id, 'status', "in_dispute");

        if (!empty($uploads)) {
            carbon_set_post_meta($post_id, 'documents', $uploads); // Save as array of attachment IDs
        }
    
        wp_send_json_success(['message' => 'Dispute successfully created.']);
        wp_die();
    }


    public function delete_dispute()
    {
        // Verificar el nonce para la seguridad
        check_ajax_referer('delete_dispute_nonce', 'security');

        $dispute_id = intval($_POST['dispute_id']);

        if (!$dispute_id) {
            wp_send_json_error(['message' => 'You need a valid ID.']);
        }

        // Imprimir el ID para depuración
        error_log('Dispute ID: ' . $dispute_id);

        // Verificar si la disputa existe usando get_post
        $dispute = get_post($dispute_id);

        if (!$dispute || $dispute->post_type !== 'dispute') {
            wp_send_json_error(['message' => 'Dispute not found']);
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');

        if (!$commission_request_id) {
            wp_send_json_error(['message' => 'Post not found.']);
        }

        $initiating_user_id = carbon_get_post_meta($dispute_id, 'initiating_user');

        if (get_current_user_id() != $initiating_user_id) {
            wp_send_json_error(['message' => 'This dispute can only be deleted by the user who created it.']);
        }

        // Actualizar el estado de la solicitud de comisión a 'pending'
        carbon_set_post_meta($commission_request_id, 'status', 'pending');

        if (wp_delete_post($dispute_id, true)) {
            wp_send_json_success(['message' => 'Dispute successfully deleted!']);
        } else {
            wp_send_json_error(['message' => 'Error deleting the post. Try again later.']);
        }

        wp_die();
    }

    


}
