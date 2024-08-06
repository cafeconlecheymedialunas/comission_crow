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
        // Verificar nonce para seguridad
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
            'post_type' => 'dispute',
            'meta_query' => [
                [
                    'key' => "commission_request_id",
                    'value' => $commission_request_id,
                    'compare' => '=',
                ],
            ],
        ]);

        if ($dispute_query->have_posts()) {
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
        $dispute_id = wp_insert_post([
            'post_type' => 'dispute',
            'post_status' => 'publish',
            'post_title' => 'Dispute related to request ' . $commission_request_id,
        ]);

        // Check for errors
        if (is_wp_error($dispute_id)) {
            $general_errors[] = 'Could not create dispute.';
            wp_send_json_error(['general' => $general_errors]);
            wp_die();
        }

        // Set post meta
        $status_history = Helper::add_item_to_status_history($dispute_id);
        carbon_set_post_meta($dispute_id, 'commission_request_id', $commission_request_id);
        carbon_set_post_meta($dispute_id, 'description', $description);
        carbon_set_post_meta($dispute_id, 'subject', $subject);
        carbon_set_post_meta($dispute_id, 'status', "dispute_pending");
        carbon_set_post_meta($dispute_id, 'initiating_user', get_current_user_id());
        carbon_set_post_meta($dispute_id, 'status_history', $status_history);
        carbon_set_post_meta($dispute_id, 'date', current_time("mysql"));

        // Updatge Commission Request to in_DISPUTE
        $status_commision_request_history = Helper::add_item_to_status_history($commission_request_id, "dispute_pending");
        carbon_set_post_meta($commission_request_id, 'status_history', $status_commision_request_history);
        carbon_set_post_meta($commission_request_id, 'status', "dispute_pending");

        if (!empty($uploads)) {
            carbon_set_post_meta($dispute_id, 'documents', $uploads); // Save as array of attachment IDs
        }

        $this->send_dispute_created_email_to_agent($dispute_id);
        $this->send_dispute_created_email_to_company($dispute_id);

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
        $status_commision_request_history = Helper::add_item_to_status_history($commission_request_id, "payment_pending");
        carbon_set_post_meta($commission_request_id, 'status_history', $status_commision_request_history);
        carbon_set_post_meta($commission_request_id, 'status', "payment_pending");

        $dispute_id = wp_delete_post($dispute_id, true);

        if (is_wp_error($dispute_id)) {
            wp_send_json_error(['message' => 'Error deleting the post. Try again later.']);
        }

        $this->send_dispute_deleted_email_to_agent($dispute_id);
        $this->send_dispute_deleted_email_to_company($dispute_id);

        wp_send_json_success(['message' => 'Dispute successfully deleted!']);
        wp_die();
    }
    public function send_dispute_created_email_to_agent($dispute_id)
    {
        // Obtener detalles de la disputa
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return false; // Retorna false si el ID de la disputa no es válido
        }
    
        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $agent_user_id = carbon_get_post_meta($dispute_id, 'initiating_user');
        $agent_user = get_user_by('ID', $agent_user_id);
    
        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return false; // Retorna false si el ID del usuario no es válido
        }
    
        $company_id = carbon_get_post_meta($commission_request_id, 'company');
        $company_name = get_post_meta($company_id, 'company_name', true);
    
        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();
    
        // Definir los parámetros del correo electrónico
        $to = $agent_user->user_email;
        $subject = 'New Dispute Created';
        $message = "<p>Hello {$agent_user->first_name},</p>
            <p>A new dispute has been created with the following details:</p>
            <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
            <p><strong>Dispute ID:</strong> {$dispute_id}</p>
            <p><strong>Subject:</strong> {$dispute->post_title}</p>
            <p><strong>Description:</strong> {$dispute->post_content}</p>
            <p><strong>Company:</strong> {$company_name}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";
    
        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to agent regarding dispute creation: ' . $error_message);
            }
            return false; // Retorna false si el envío del correo falló
        }
    
        return true; // Retorna true si el correo fue enviado correctamente
    }
    
    

    public function send_dispute_created_email_to_company($dispute_id)
    {
        // Obtener detalles de la disputa
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return;
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $company_id = carbon_get_post_meta($commission_request_id, 'company');
        $company_user_id = get_post_meta($company_id, 'user', true);
        $company_user = get_user_by('ID', $company_user_id);

        if (!$company_user) {
            error_log('Invalid company user ID.');
            return;
        }

        $company_name = get_post_meta($company_id, 'company_name', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $company_user->user_email;
        $subject = 'New Dispute Created';
        $message = "<p>Hello,</p>
            <p>A new dispute has been created related to your company with the following details:</p>
            <p><strong>Company:</strong> {$company_name}</p>
            <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
            <p><strong>Dispute ID:</strong> {$dispute_id}</p>
            <p><strong>Subject:</strong> {$dispute->post_title}</p>
            <p><strong>Description:</strong> {$dispute->post_content}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";

            $sent = $email_sender->send_email($to, $subject, $message);

            if (!$sent) {
                $errors = $email_sender->get_error();
                foreach ($errors->get_error_messages() as $error_message) {
                    error_log('Error sending email: ' . $error_message);
                }
                return false;
            }
    
            return true;
    }

    public function send_dispute_approval_email_to_agent($dispute_id)
    {
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return;
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $agent_user_id = carbon_get_post_meta($dispute_id, 'initiating_user');
        $agent_user = get_user_by('ID', $agent_user_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return;
        }

        $company_id = carbon_get_post_meta($commission_request_id, 'company');
        $company_name = get_post_meta($company_id, 'company_name', true);

        $email_sender = new EmailSender();
        $to = $agent_user->user_email;
        $subject = 'Dispute Approved';
        $message = "<p>Hello {$agent_user->first_name},</p>
        <p>Your dispute with ID {$dispute_id} has been approved.</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Subject:</strong> {$dispute->post_title}</p>
        <p><strong>Description:</strong> {$dispute->post_content}</p>
        <p>If you have any questions, please contact us.</p>";

        $sent = $email_sender->send_email($to, $subject, $message);

        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
            return false;
        }

        return true;
    }

    public function send_dispute_approval_email_to_company($dispute_id)
    {
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return;
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $company_id = carbon_get_post_meta($commission_request_id, 'company');
        $company_user_id = get_post_meta($company_id, 'user', true);
        $company_user = get_user_by('ID', $company_user_id);

        if (!$company_user) {
            error_log('Invalid company user ID.');
            return;
        }

        $company_name = get_post_meta($company_id, 'company_name', true);

        $email_sender = new EmailSender();
        $to = $company_user->user_email;
        $subject = 'Dispute Approved';
        $message = "<p>Hello,</p>
        <p>The dispute with ID {$dispute_id} related to your company has been approved.</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Subject:</strong> {$dispute->post_title}</p>
        <p><strong>Description:</strong> {$dispute->post_content}</p>
        <p>If you have any questions, please contact us.</p>";

        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email  ' . $error_message);
            }
            return false;
        }

        return true;
    }
    public function send_dispute_rejection_email_to_agent($dispute_id)
    {
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return;
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $agent_user_id = carbon_get_post_meta($dispute_id, 'initiating_user');
        $agent_user = get_user_by('ID', $agent_user_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return;
        }

        $company_id = carbon_get_post_meta($commission_request_id, 'company');
        $company_name = get_post_meta($company_id, 'company_name', true);

        $email_sender = new EmailSender();
        $to = $agent_user->user_email;
        $subject = 'Dispute Rejected';
        $message = "<p>Hello {$agent_user->first_name},</p>
        <p>Your dispute with ID {$dispute_id} has been rejected.</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Subject:</strong> {$dispute->post_title}</p>
        <p><strong>Description:</strong> {$dispute->post_content}</p>
        <p>If you have any questions, please contact us.</p>";
        
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
            return false;
        }

        return true;
    }

    public function send_dispute_rejection_email_to_company($dispute_id)
    {
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return;
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $company_id = carbon_get_post_meta($commission_request_id, 'company');
        $company_user_id = get_post_meta($company_id, 'user', true);
        $company_user = get_user_by('ID', $company_user_id);

        if (!$company_user) {
            error_log('Invalid company user ID.');
            return;
        }

        $company_name = get_post_meta($company_id, 'company_name', true);

        $email_sender = new EmailSender();
        $to = $company_user->user_email;
        $subject = 'Dispute Rejected';
        $message = "<p>Hello,</p>
        <p>The dispute with ID {$dispute_id} related to your company has been rejected.</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Subject:</strong> {$dispute->post_title}</p>
        <p><strong>Description:</strong> {$dispute->post_content}</p>
        <p>If you have any questions, please contact us.</p>";

        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
            return false;
        }

        return true;
    }

    public function send_dispute_deleted_email_to_agent($dispute_id)
    {
        // Obtener detalles de la disputa
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return;
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $agent_user_id = carbon_get_post_meta($dispute_id, 'initiating_user');
        $agent_user = get_user_by('ID', $agent_user_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return;
        }

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $agent_user->user_email;
        $subject = 'Dispute Deleted';
        $message = "<p>Hello {$agent_user->first_name},</p>
            <p>The dispute with the following details has been deleted:</p>
            <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
            <p><strong>Dispute ID:</strong> {$dispute_id}</p>
            <p><strong>Subject:</strong> {$dispute->post_title}</p>
            <p><strong>Description:</strong> {$dispute->post_content}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
            return false;
        }

        return true;
    }

    public function send_dispute_deleted_email_to_company($dispute_id)
    {
        // Obtener detalles de la disputa
        $dispute = get_post($dispute_id);
        if (!$dispute) {
            error_log('Invalid dispute ID.');
            return;
        }

        $commission_request_id = carbon_get_post_meta($dispute_id, 'commission_request_id');
        $company_id = carbon_get_post_meta($commission_request_id, 'company');
        $company_user_id = get_post_meta($company_id, 'user', true);
        $company_user = get_user_by('ID', $company_user_id);

        if (!$company_user) {
            error_log('Invalid company user ID.');
            return;
        }

        $company_name = get_post_meta($company_id, 'company_name', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $company_user->user_email;
        $subject = 'Dispute Deleted';
        $message = "<p>Hello,</p>
            <p>The dispute related to your company with the following details has been deleted:</p>
            <p><strong>Company:</strong> {$company_name}</p>
            <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
            <p><strong>Dispute ID:</strong> {$dispute_id}</p>
            <p><strong>Subject:</strong> {$dispute->post_title}</p>
            <p><strong>Description:</strong> {$dispute->post_content}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";

   
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
            return false;
        }

        return true;
    }
}
