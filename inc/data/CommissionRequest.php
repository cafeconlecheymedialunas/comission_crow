<?php
class CommissionRequest
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

        $errors = []; // Array for item-specific errors

        // Sanitize and validate form data
        $contract_id = sanitize_text_field($_POST['contract_id']);

        $comments = wp_kses_post($_POST["comments"]);

        // Validate contract_id
        if (empty($contract_id)) {

            wp_send_json_error(["general" => 'Contract ID is required.']);
        }

        $contract = get_post($contract_id);

        if (empty($contract)) {
            wp_send_json_error(["general" => 'Contract Not Found.']);

        }

        // Validate current user
        $current_user = wp_get_current_user();
        if (!in_array("commercial_agent", $current_user->roles)) {
            wp_send_json_error(["general" => 'You must be a commercial agent to create a commission request.']);

        }

        // Verify the status of the contract
        $status = carbon_get_post_meta($contract_id, "status");
        if ($status === "pending" || $status === "refused") {
            wp_send_json_error(["general" => 'The contract is either pending or refused.']);

        }

        // Check if a commission request already exists for this contract
        $query = new WP_Query([
            'post_type' => 'commission_request',
            'meta_query' => [
                [
                    'key' => 'contract_id',
                    'value' => $contract_id,
                    'compare' => '=', // Compare exact value
                ],
            ],
        ]);
        $commission_requests = $query->posts;

        if (!empty($commission_requests)) {

            wp_send_json_error(["general" => 'A commission request already exists for this contract.']);
        }

        // Check if there are any general errors and send them in response

        $allowed_supporting_types = ['application/pdf', 'text/plain'];
        $max_supporting_size = 10 * 1024 * 1024; // 10 MB

        if (isset($_FILES['general_invoice']) && !empty($_FILES['general_invoice']['name'][0])) {
            $validation_result = validate_files($_FILES['general_invoice'], $allowed_supporting_types, $max_supporting_size);
            if (isset($validation_result['error'])) {
                $errors['general_invoice'][] = $validation_result['error'];
            }
        }

        $general_invoice_ids = handle_multiple_file_upload($_FILES['general_invoice']);

        // Prepare and update Carbon Fields
        $items = [];
        $total_cart = 0;
        $commission = intval(carbon_get_post_meta($contract_id, "commission"));
        $minimal_price = carbon_get_post_meta($contract_id, "minimal_price");

        if (isset($_POST['price']) && is_array($_POST['price'])) {
            foreach ($_POST['price'] as $index => $price) {
                $price_paid = floatval(sanitize_text_field($price));
                $quantity = intval(sanitize_text_field($_POST['quantity'][$index]));
                $detail = sanitize_text_field($_POST['detail'][$index]);

                // Adding the row number to the error messages
                $row_number = $index + 1;

                if ($price_paid <= 0) {
                    wp_send_json_error(["general" => "In row $row_number the price is no specified."]);
                    wp_die();
                }
                if ($price_paid < $minimal_price) {
                    $minimal_price_formatted = Helper::get_human_time_diff($minimal_price);
                    wp_send_json_error(["general" => "In row $row_number  the price is less than the agreed contract price ($minimal_price_formatted)."]);
                    wp_die();
                }
                if ($quantity <= 0) {
                    wp_send_json_error(["general" => "In row $row_number the quantity is no specified."]);
                    wp_die();
                }

                if (isset($_FILES['invoice']) && !empty($_FILES['invoice']['name'][0])) {
                    $validation_result = validate_files($_FILES['invoice']);
                    if (isset($validation_result['error'])) {
                        $error = $validation_result['error'];
                        wp_send_json_error(["general" => "In row $row_number the file has this error: $error"]);
                        wp_die();
                    }
                }

                $invoices_ids = handle_multiple_file_upload($_FILES['invoice']);

                if ($quantity >= 0 && $price_paid >= 0) {
                    $subtotal = $price_paid * $quantity;
                    $total_cart += $subtotal;

                    $items[] = [
                        'price_paid' => $price_paid,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'detail' => $detail,
                        'invoice' => $invoices_ids,
                    ];
                }
            }
        }

        // Check if there are any item-specific errors and send them in response
        if (!empty($errors)) {
            wp_send_json_error(['fields' => $errors]);
            wp_die();
        }

        $sku = carbon_get_post_meta($contract_id, "sku");
        $contract_title_key = $sku ?? $contract_id;

        $post_id = wp_insert_post([
            'post_type' => 'commission_request',
            'post_status' => 'publish',
            'post_title' => 'Commission Request by contract ' . $contract_title_key, // Customize the title as needed
        ]);

        if (is_wp_error($post_id)) {
            wp_send_json_error(["general" => 'Could not create the commission request.']);
            wp_die();
        }

        $total_agent = $this->calculate_agent_price($total_cart, $commission);
        $total_tax_service = $this->calculate_tax_stripe_price($total_cart);
        $total_platform = $this->calculate_platform_price($total_cart);
        $total_to_pay = $this->calculate_total($total_agent, $total_tax_service, $total_platform);
        $status_history = Helper::add_item_to_status_history($contract_id, "pending");
        carbon_set_post_meta($post_id, 'contract_id', $contract_id);
        carbon_set_post_meta($post_id, 'general_invoice', $general_invoice_ids);
        carbon_set_post_meta($post_id, 'items', $items);
        carbon_set_post_meta($post_id, 'total_cart', $total_cart);
        carbon_set_post_meta($post_id, 'total_agent', $total_agent);
        carbon_set_post_meta($post_id, 'total_tax_service', $total_tax_service);
        carbon_set_post_meta($post_id, 'total_platform', $total_platform);
        carbon_set_post_meta($post_id, 'total_to_pay', $total_to_pay);
        carbon_set_post_meta($post_id, "date", current_time('mysql'));
        carbon_set_post_meta($post_id, "comments", $comments);
        carbon_set_post_meta($post_id, "status", "pending");
        carbon_set_post_meta($post_id, "status_history", $status_history);
        carbon_set_post_meta($post_id, "initiating_user", get_current_user_id());

        $this->send_commission_request_email_to_agent($post_id);
        $this->send_commission_request_email_to_company($post_id);

        wp_send_json_success(['message' => 'Commission request successfully created.']);
        wp_die();
    }

    public function calculate_agent_price($total_cart, $commission)
    {
        // Verifica que $total_cart y $commission sean valores numéricos y no vacíos
        if (empty($total_cart) || !is_numeric($total_cart)) {
            return 0; // Retorna 0 si el valor es inválido
        }
        if (empty($commission) || !is_numeric($commission)) {
            return 0; // Retorna 0 si el valor es inválido
        }

        // Calcula el descuento
        $discount = $total_cart * $commission / 100;

        return $discount;
    }

    public function calculate_tax_stripe_price($total_cart)
    {

        if (empty($total_cart) || !is_numeric($total_cart)) {
            return;
        }

        $tax = $total_cart * 3 / 100;

        return $tax;
    }

    public function calculate_platform_price($total_cart)
    {

        if (empty($total_cart) || !is_numeric($total_cart)) {
            return;
        }

        $tax = $total_cart * 5 / 100;

        return $tax;
    }

    public function calculate_total($agent_price, $tax_price, $platform_price)
    {

        $total_final = $agent_price + $tax_price + $platform_price;

        return $total_final;
    }

    public function delete_commission_request()
    {

        // Verificar el nonce para la seguridad
        check_ajax_referer('delete_commission_request_nonce', 'security');

        $commission_request_id = intval($_POST['commission_request_id']);

        if (!$commission_request_id) {
            wp_send_json_error(['message' => 'You need a valid ID.']);
        }

        $commission_request = get_post($commission_request_id);

        if (!$commission_request) {
            wp_send_json_error(['message' => 'Commission Request Not found']);
        }
        $initiating_user_id = carbon_get_post_meta($commission_request->ID, "initiating_user");

        if (get_current_user_id() !== $initiating_user_id) {
            wp_send_json_error(['message' => 'This commission request can only be deleted by the user who created it.']);
        }

        // Intentar eliminar la oportunidad
        if (wp_delete_post($commission_request_id, true)) {
            $this->send_commission_request_deletion_email_to_agent($commission_request_id);
            $this->send_commission_request_deletion_email_to_company($commission_request_id);
            wp_send_json_success(['message' => 'Commission Request Succesfully Deleted!']);
        } else {
            wp_send_json_error(['message' => 'Error deleting the post. Try again later.']);
        }
        wp_die();
    }

    private function send_commission_request_deletion_email_to_agent($commission_request_id)
    {
        // Obtener detalles de la solicitud de comisión
        $commission_request = get_post($commission_request_id);
        if (!$commission_request) {
            error_log('Invalid commission request ID.');
            return;
        }

        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        $agent_user_id = carbon_get_post_meta($commission_request_id, 'initiating_user');
        $agent_user = get_user_by('ID', $agent_user_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return;
        }

        $company_id = carbon_get_post_meta($contract_id, 'company');
        $company_name = get_post_meta($company_id, 'company_name', true);

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $agent_user->user_email;
        $subject = 'Commission Request Deleted';
        $message = "<p>Hello {$agent_user->first_name},</p>
        <p>We regret to inform you that the commission request you created has been deleted. Here are the details:</p>
        <p><strong>Contract ID:</strong> {$contract_id}</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Details:</strong> {$commission_request->post_content}</p>
        <p>If you have any questions or need further assistance, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to agent regarding deletion: ' . $error_message);
            }
        }
        return $sent;
    }
    private function send_commission_request_deletion_email_to_company($commission_request_id)
    {
        // Obtener detalles de la solicitud de comisión
        $commission_request = get_post($commission_request_id);
        if (!$commission_request) {
            error_log('Invalid commission request ID.');
            return;
        }

        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        $company_id = carbon_get_post_meta($contract_id, 'company');
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
        $subject = 'Commission Request Deleted';
        $message = "<p>Hello,</p>
        <p>We regret to inform you that a commission request associated with your company has been deleted. Here are the details:</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Contract ID:</strong> {$contract_id}</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Details:</strong> {$commission_request->post_content}</p>
        <p>If you have any questions or need further assistance, please contact us.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to company regarding deletion: ' . $error_message);
            }
        }
        return $sent;
    }

    public function is_paid($commission_request_id)
    {
        $query = new WP_Query([
            'post_type' => 'payment',
            'meta_query' => [
                [
                    'key' => 'commission_request_id',
                    'value' => $commission_request_id,
                    'compare' => '=', // Compare exact value
                ],
            ],
        ]);
        if (empty($query->posts)) {
            return;
        }

        return $query->posts;

    }

    public function has_open_dispute($commission_request_id)
    {
        $query = new WP_Query([
            'post_type' => 'dispute',
            'meta_query' => [
                [
                    'key' => 'commission_request_id',
                    'value' => $commission_request_id,
                    'compare' => '=', // Compare exact value
                ],
                [
                    'key' => 'status',
                    'value' => "pending",
                    'compare' => '=', // Compare exact value
                ],
            ],
        ]);
        if (empty($query->posts)) {
            return;
        }

        return $query->posts;

    }

    private function send_commission_request_email_to_agent($commission_request_id)
    {
        // Obtener detalles del pedido de comisión
        $commission_request = get_post($commission_request_id);
        if (!$commission_request) {
            error_log('Invalid commission request ID.');
            return;
        }

        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        $contract = get_post($contract_id);
        $agent_user_id = carbon_get_post_meta($commission_request_id, 'initiating_user');
        $agent_user = get_user_by('ID', $agent_user_id);

        if (!$agent_user) {
            error_log('Invalid agent user ID.');
            return;
        }

        $company_id = carbon_get_post_meta($contract_id, 'company');
        $company_name = get_post_meta($company_id, 'company_name', true);
        $commission_request_id = carbon_get_post_meta($commission_request_id, 'commission_request_id');

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $agent_user->user_email;
        $subject = 'New Commission Request Created';
        $message = "<p>Hello {$agent_user->first_name},</p>
        <p>A new commission request has been created with the following details:</p>
        <p><strong>Contract ID:</strong> {$contract_id}</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Details:</strong> {$commission_request->post_content}</p>
        <p>Thank you for your continued partnership.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to agent: ' . $error_message);
            }
        }
        return $sent;
    }
    private function send_commission_request_email_to_company($commission_request_id)
    {
        // Obtener detalles del pedido de comisión
        $commission_request = get_post($commission_request_id);
        if (!$commission_request) {
            error_log('Invalid commission request ID.');
            return;
        }

        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        $company_id = carbon_get_post_meta($contract_id, 'company');
        $company_user_id = get_post_meta($company_id, 'user', true);
        $company_user = get_user_by('ID', $company_user_id);

        if (!$company_user) {
            error_log('Invalid company user ID.');
            return;
        }

        $company_name = get_post_meta($company_id, 'company_name', true);
        $commission_request_id = carbon_get_post_meta($commission_request_id, 'commission_request_id');

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Definir los parámetros del correo electrónico
        $to = $company_user->user_email;
        $subject = 'New Commission Request Created';
        $message = "<p>Hello,</p>
        <p>A new commission request has been created with the following details:</p>
        <p><strong>Company:</strong> {$company_name}</p>
        <p><strong>Contract ID:</strong> {$contract_id}</p>
        <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
        <p><strong>Details:</strong> {$commission_request->post_content}</p>
        <p>Thank you for your continued partnership.</p>";

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email to company: ' . $error_message);
            }
        }
        return $sent;
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
