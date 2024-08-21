<?php
class Deposit
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
    public function withdraw_funds()
    {
        // Verifica el nonce
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'withdraw-funds')) {
            wp_send_json_error(['general' => 'Nonce verification failed.']);
            return;
        }
    
        $commercial_agent_id = isset($_POST['commercial_agent_id']) ? intval($_POST['commercial_agent_id']) : 0;

      
        $current_user_id = get_current_user_id();
    
        if ($current_user_id <= 0 || $commercial_agent_id <= 0) {
            wp_send_json_error(['general' => 'Invalid user or agent ID.']);
            return;
        }
    
          // Verificar si el agente comercial es válido
          if (!get_post( $commercial_agent_id)) {
            wp_send_json_error(['general' => 'Commercial Agent Not Found.']);
            return;
        }
        // Verifica si hay algún pedido de retiro abierto para el usuario
        $open_withdrawals = get_posts([
            'post_type' => 'deposit',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'user',
                    'value' => $current_user_id,
                    'compare' => '=',
                ],
                [
                    'key' => 'status',
                    'value' => 'deposit_requested',
                    'compare' => '=',
                ]
            ],
        ]);
    
        if (!empty($open_withdrawals)) {
            wp_send_json_error(['general' => 'You already have an open withdrawal request.']);
            return;
        }
    
        $wallet_balance = $this->calculate_wallet_balance();
        $amount_to_withdraw = $wallet_balance;
    
        if ($amount_to_withdraw <= 0) {
            wp_send_json_error(['general' => 'Invalid withdrawal amount.']);
            return;
        }
    
        if ($amount_to_withdraw > $wallet_balance) {
            wp_send_json_error(['general' => 'Insufficient funds.']);
            return;
        }
    
      
    
        $post_data = [
            'post_title'    => 'Withdraw Fund Request for User ' . $current_user_id,
            'post_content'  => 'Amount: ' . $amount_to_withdraw,
            'post_status'   => 'publish',
            'post_author'   => $current_user_id,
            'post_type'     => 'deposit',
        ];
    
        $post_id = wp_insert_post($post_data);
    
        if (!$post_id) {
            error_log('Failed to create deposit record for user ' . $current_user_id);
            wp_send_json_error(['general' => 'Failed to create deposit record.']);
        }
    
        carbon_set_post_meta($post_id, 'total_withdraw_funds', $amount_to_withdraw);
        carbon_set_post_meta($post_id, 'date', current_time("mysql"));
        carbon_set_post_meta($post_id, 'user', $current_user_id);
        carbon_set_post_meta($post_id, 'status', "deposit_requested");
    
        $this->send_deposit_request_email_to_admin($post_id);
        $this->send_deposit_request_email_to_agent($commercial_agent_id, $post_id);
    
        wp_send_json_success(['message' => 'Funds withdrawal request successfully recorded.']);
    }
    

    public function calculate_total_income_by_month()
    {
        $commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();

        // Extraer los IDs de los commission requests
        $commission_request_ids = array_map(function ($post) {
            return $post->ID;
        }, $commission_requests);

        $payments = get_posts([
            'post_type' => 'payment',
            'posts_per_page' => -1,
        ]);

        // Array para almacenar el ingreso total por mes
        $total_income_by_month = array_fill(1, 12, 0);

        foreach ($payments as $payment) {
            $commission_request_id = carbon_get_post_meta($payment->ID, 'commission_request_id');

            // Verifica si el commission_request_id está en la lista de IDs de commission_requests del usuario
            if (in_array($commission_request_id, $commission_request_ids)) {
                $total_paid = carbon_get_post_meta($payment->ID, 'total_paid');
                $payment_date = get_post_meta($payment->ID, 'date', true);

                if ($payment_date) {
                    $timestamp = strtotime($payment_date);
                    $month = (int) date('n', $timestamp);
                    $total_income_by_month[$month] += floatval($total_paid);
                }
            }
        }

        return $total_income_by_month;
    }
    public function calculate_total_incomes()
    {
        // Obtiene los requests de comisión para el usuario
        $commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();

        // Extrae los IDs de los commission requests
        $commission_request_ids = array_map(function ($post) {
            return $post->ID;
        }, $commission_requests);

        

        // Obtiene todos los pagos completados
        $payments = get_posts([
            'post_type' => 'payment',
            'posts_per_page' => -1,
            'post_status' => 'publish', // Asegúrate de que solo se obtengan pagos publicados
        ]);
        

        $total_income = 0;
    

        foreach ($payments as $payment) {
            // Obtiene el ID de la solicitud de comisión asociada
            $commission_request_id = carbon_get_post_meta($payment->ID, 'commission_request_id');

           

            

            // Verifica si el commission_request_id está en la lista de IDs de commission_requests del usuario
            if (in_array($commission_request_id, $commission_request_ids)) {
                // Obtiene el estado del pago
            
                $payment_status = get_post_meta($payment->ID, '_status');
             

                // Solo agrega al total si el pago está completado
                if ($payment_status[0] === 'payment_completed') {
                    $total_paid = carbon_get_post_meta($payment->ID, 'total_agent');
                    $total_income += floatval($total_paid);
                }
            }
        }

        return $total_income;
    }

    public function calculate_total_withdrawals()
    {
        $deposits = get_posts([
            'post_type' => 'deposit',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'user',
                    'value' => get_current_user_id(),
                    'compare' => '=',
                ],
            ],
        ]);

        $total_withdrawals = 0;

        foreach ($deposits as $deposit) {
            $total_paid = carbon_get_post_meta($deposit->ID, 'total_paid');
            $deposit_status = carbon_get_post_meta($deposit->ID, 'status');
        

            // Incluye solo los depósitos con estado "deposit_completed" (excluye "pending_deposit")
            if ($deposit_status === 'deposit_completed') {
                $total_withdrawals += floatval($total_paid);
            }
        }

        return $total_withdrawals;
    }

    public function calculate_pending_withdrawals()
    {
        $deposits = get_posts([
            'post_type' => 'deposit',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'user',
                    'value' => get_current_user_id(),
                    'compare' => '=',
                ],
            ],
        ]);

        $pending_withdrawals = 0;

        foreach ($deposits as $deposit) {
            $total_paid = carbon_get_post_meta($deposit->ID, 'total_paid');
            $deposit_status = carbon_get_post_meta($deposit->ID, 'status');
        
            if ($deposit_status === 'deposit_requested') {
                $pending_withdrawals += floatval($total_paid);
            }
        }

        return $pending_withdrawals;
    }

    public function calculate_wallet_balance()
    {
        $total_income = $this->calculate_total_incomes();
        $total_withdrawals = $this->calculate_total_withdrawals();
        $wallet_balance = $total_income - $total_withdrawals;
        return $wallet_balance;
    }

    public function send_deposit_request_email_to_admin($post_id)
    {
        $admin_email = get_option('admin_email');
        $post = get_post($post_id);

        if (!$post) {
            error_log('Invalid deposit post ID.');
            return false;
        }

        $amount = get_post_meta($post_id, 'total_withdraw_funds', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $admin_email;
        $subject = __('New Deposit Request');
        $message = "<p>Hello Admin,</p>
            <p>A new deposit request has been created with the following details:</p>
            <p><strong>Deposit ID:</strong> {$post_id}</p>
            <p><strong>Requested Amount:</strong> {$amount}</p>
            <p><strong>Description:</strong> {$post->post_content}</p>
            <p><strong>User:</strong> " . get_user_by('ID', $post->post_author)->user_login . "</p>
            <p>Please review and process this request as soon as possible.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending deposit request email to admin: ' . $error_message);
            }
        }

        return $sent;
    }

    public function send_deposit_request_email_to_agent($agent_id, $post_id)
    {
        $agent_user = get_user_by('ID', $agent_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return false;
        }

        $post = get_post($post_id);

        if (!$post) {
            error_log('Invalid deposit post ID.');
            return false;
        }

        $amount = get_post_meta($post_id, 'total_withdraw_funds', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $agent_user->user_email;
        $subject = __('Deposit Request Received');
        $message = "<p>Hello {$agent_user->first_name},</p>
            <p>Your deposit request has been received with the following details:</p>
            <p><strong>Deposit ID:</strong> {$post_id}</p>
            <p><strong>Requested Amount:</strong> {$amount}</p>
            <p><strong>Description:</strong> {$post->post_content}</p>
            <p><strong>User:</strong> " . get_user_by('ID', $post->post_author)->user_login . "</p>
            <p>Your request will be processed within the next 48 hours. If you have any questions, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending deposit request email to agent: ' . $error_message);
            }
        }

        return $sent;
    }

    public function send_deposit_approval_email_to_agent($post_id)
    {
        $post = get_post($post_id);

        if (!$post) {
            error_log('Invalid deposit post ID.');
            return false;
        }

        $agent_id = get_post_meta($post_id, 'agent', true);
        $agent_user = get_user_by('ID', $agent_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return false;
        }

        $amount = get_post_meta($post_id, 'total_deposit_amount', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $agent_user->user_email;
        $subject = __('Deposit Approved');
        $message = "<p>Hello {$agent_user->first_name},</p>
            <p>Your deposit request has been approved with the following details:</p>
            <p><strong>Deposit ID:</strong> {$post_id}</p>
            <p><strong>Approved Amount:</strong> {$amount}</p>
            <p><strong>Description:</strong> {$post->post_content}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending deposit approval email to agent: ' . $error_message);
            }
        }

        return $sent;
    }

    public function send_deposit_approval_email_to_company($post_id)
    {
        $post = get_post($post_id);

        if (!$post) {
            error_log('Invalid deposit post ID.');
            return false;
        }

        $company_email = get_option('company_email');
        $amount = get_post_meta($post_id, 'total_deposit_amount', true);

        if (!$company_email) {
            error_log('Company email is not set.');
            return false;
        }

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $company_email;
        $subject = __('Deposit Approved');
        $message = "<p>Hello,</p>
            <p>A deposit request has been approved with the following details:</p>
            <p><strong>Deposit ID:</strong> {$post_id}</p>
            <p><strong>Approved Amount:</strong> {$amount}</p>
            <p><strong>Description:</strong> {$post->post_content}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending deposit approval email to company: ' . $error_message);
            }
        }

        return $sent;
    }

    public function send_deposit_rejection_email_to_agent($post_id)
    {
        $post = get_post($post_id);

        if (!$post) {
            error_log('Invalid deposit post ID.');
            return false;
        }

        $agent_id = get_post_meta($post_id, 'agent', true);
        $agent_user = get_user_by('ID', $agent_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return false;
        }

        $amount = get_post_meta($post_id, 'total_deposit_amount', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $agent_user->user_email;
        $subject = __('Deposit Rejected');
        $message = "<p>Hello {$agent_user->first_name},</p>
            <p>Your deposit request has been rejected with the following details:</p>
            <p><strong>Deposit ID:</strong> {$post_id}</p>
            <p><strong>Requested Amount:</strong> {$amount}</p>
            <p><strong>Description:</strong> {$post->post_content}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending deposit rejection email to agent: ' . $error_message);
            }
            
        }

        return $sent;
    }

    public function send_deposit_rejection_email_to_company($post_id)
    {
        $post = get_post($post_id);

        if (!$post) {
            error_log('Invalid deposit post ID.');
            return false;
        }

        $company_email = get_option('company_email');

        if (!$company_email) {
            error_log('Company email is not set.');
            return false;
        }

        $amount = get_post_meta($post_id, 'total_deposit_amount', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $company_email;
        $subject = __('Deposit Rejected');
        $message = "<p>Hello,</p>
            <p>A deposit request has been rejected with the following details:</p>
            <p><strong>Deposit ID:</strong> {$post_id}</p>
            <p><strong>Requested Amount:</strong> {$amount}</p>
            <p><strong>Description:</strong> {$post->post_content}</p>
            <p>If you have any questions or need further assistance, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending deposit rejection email to company: ' . $error_message);
            }
            
        }

        return $sent;
    }
}
