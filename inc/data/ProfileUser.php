<?php
class ProfileUser
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


    public function update_user_data()
    {

        check_ajax_referer('update-userdata', 'security');

        $user_id = get_current_user_id();

        // Sanitiza y valida los datos
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $user_email = sanitize_email($_POST['user_email']);
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';

        $errors = [];

        // Validaciones
        if (empty($first_name)) {
            $errors['first_name'] = __('First name is required.');
        }
        if (empty($last_name)) {
            $errors['last_name'] = __('Last name is required.');
        }
        if (empty($user_email)) {
            $errors['user_email'] = __('Email is required.');
        } elseif (!is_email($user_email)) {
            $errors['user_email'] = __('This email has an incorrect format.');
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                $errors['password'][] = __('Password must be at least 8 characters long.');
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors['password'][] = __('Password must contain at least one uppercase letter.');
            }
            if (!preg_match('/[a-z]/', $password)) {
                $errors['password'][] = __('Password must contain at least one lowercase letter.');
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors['password'][] = __('Password must contain at least one number.');
            }
            if (!preg_match('/[\W_]/', $password)) {
                $errors['password'][] = __('Password must contain at least one special character.');
            }
        }

        // Si hay errores, envía respuesta de error
        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            die();
        }

        // Actualiza los datos del usuario
        $user_data = [
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $user_email,
            "display_name" => $first_name . " " . $last_name,
        ];

        if (!empty($password)) {
            $user_data['user_pass'] = $password;
        }

        $updated_user_id = wp_update_user($user_data);

        if (is_wp_error($updated_user_id)) {
            wp_send_json_error(['message' => __('Error updating user: ') . $updated_user_id->get_error_message()]);
        }

        $user = get_user_by("ID", $updated_user_id);

        if (in_array("commercial_agent", $user->roles)) {

            $post = ProfileUser::get_instance()->get_user_associated_post_type();

            $post_data = [
                'ID' => $post->ID,
                'post_title' => $first_name . " " . $last_name,
            ];

            // Realizar la actualización
            wp_update_post($post_data);
        }

        $role = $user->roles[0];
        $role = $role === "commercial_agent" ? "commercial-agent" : "company";

        // Build the redirect URL
        $redirect_url = site_url("dashboard/$role/profile");

        wp_send_json_success(["redirect_url" => $redirect_url]);
      

    }

    public function update_stripe_email()
    {
        // Verificar el nonce para seguridad
        check_ajax_referer('update_stripe_email_nonce', 'security');

        // Obtener los datos del formulario
        $stripe_email = sanitize_text_field($_POST['stripe_email']);
        $commercial_agent_id = intval($_POST['commercial_agent_id']);

        $post = get_post($commercial_agent_id);
        if (empty($post)) {
            wp_send_json_error(['general' => 'Agent not found']);
        }

        // Validar los datos
        if (empty($stripe_email) || !is_email($stripe_email)) {
            wp_send_json_error(['general' => 'Invalid email address.']);
        }

        // Actualizar el email en los metadatos del agente comercial
        carbon_set_post_meta($post->ID, 'stripe_email', $stripe_email);

        // Enviar respuesta de éxito
        wp_send_json_success(['general' => 'Email updated successfully.']);
    }

    public function get_commission_request_for_current_user($commission_request_id)
    {

        // Obtener el tipo de post asociado al usuario actual
        $post_current_user = $this->get_user_associated_post_type();

        if (!$post_current_user) {
            return false;
        }

        $commission_request = get_post($commission_request_id);

        if (!$commission_request) {
            return false;
        }

        $contract_id = carbon_get_post_meta($commission_request_id, "contract_id");

        if (!$contract_id) {
            return false;
        }

        $post_user_id = carbon_get_post_meta($contract_id, $post_current_user->post_type);

        if (!$post_user_id) {
            return false;
        }

        return ($post_user_id != $post_current_user->ID) ? false : $commission_request;
    }

    public function get_contracts($statuses = [], $type = "")
    {
        $post_current_user = $this->get_user_associated_post_type();
        $args = [
            'post_type' => 'contract',
            'meta_query' => [
                [
                    'key' => $post_current_user->post_type,
                    'value' => $post_current_user->ID,
                    'compare' => '=',
                ],
            ],
            'posts_per_page' => -1,
        ];

        // Verificar si $statuses es un array y no está vacío
        if (is_array($statuses) && !empty($statuses)) {
            $args['meta_query'][] = [
                'key' => 'status',
                'value' => $statuses,
                'compare' => 'IN', // Esto funcionará como un OR en meta_query
            ];
        }

        $query = new WP_Query($args);

        // Si no se especifica el tipo, devolver todos los posts encontrados
        if (!$type) {
            return $query->posts;
        }

        $contracts = [];
        $current_user = wp_get_current_user();

        foreach ($query->posts as $contract) {
            $initiating_user = carbon_get_post_meta($contract->ID, 'initiating_user');

            if (empty($initiating_user)) {
                continue;
            }

            if ($type == "requested" && $current_user->ID === $initiating_user) {
                $contracts[] = $contract;
            } elseif ($type == "received" && $current_user->ID !== $initiating_user) {
                $contracts[] = $contract;
            }
        }

        return $contracts;
    }

    public function get_commission_requests_for_user()
    {

        // Obtener el tipo de post asociado al usuario actual
        $post = $this->get_user_associated_post_type();

        if (!$post) {
            return []; // No hay post asociado al usuario
        }

        // Consulta para obtener los contratos asociados al usuario actual
        $contract_query = new WP_Query([
            'post_type' => 'contract',
            'meta_query' => [
                [
                    'key' => $post->post_type,
                    'value' => $post->ID,
                    'compare' => '=',
                ],
            ],
        ]);

        // Array para almacenar los IDs de los contratos
        $contract_ids = [];
        foreach ($contract_query->posts as $contract) {
            $contract_ids[] = $contract->ID;
        }

        if (empty($contract_ids)) {
            return []; // No hay contratos asociados
        }

        // Consulta para obtener las solicitudes de comisión asociadas a los contratos
        $commission_request_query = new WP_Query([
            'post_type' => 'commission_request',
            'meta_query' => [
                [
                    'key' => 'contract_id', // Asegúrate de que el meta_key sea correcto
                    'value' => $contract_ids,
                    'compare' => 'IN',
                ],
            ],
        ]);

        // Devolver los resultados
        return $commission_request_query->posts;
    }

    public function get_payment_for_commission_request_user()
    {
        $commission_requests = $this->get_commission_requests_for_user();

        if (!$commission_requests) {
            return [];
        }

        $commission_request_ids = [];
        foreach ($commission_requests as $commission_request) {
            $commission_request_ids[] = $commission_request->ID;
        }

        if (empty($commission_request_ids)) {
            return [];
        }

        $commission_request_query = new WP_Query([
            'post_type' => 'payment',
            'meta_query' => [
                [
                    'key' => 'commission_request_id',
                    'value' => $commission_request_ids,
                    'compare' => 'IN',
                ],
            ],
        ]);
        return $commission_request_query->posts;
    }

    public function get_disputes_for_user()
    {
        $commission_requests = $this->get_commission_requests_for_user();
        // Consulta para obtener las disputas asociadas a los contratos

        $commission_request_ids = [];
        foreach ($commission_requests as $commission_request) {
            $commission_request_ids[] = $commission_request->ID;
        }
        $dispute_query = new WP_Query([
            'post_type' => 'dispute',
            'meta_query' => [
                [
                    'key' => 'commission_request_id', // Asegúrate de que el meta_key sea correcto
                    'value' => $commission_request_ids,
                    'compare' => 'IN',
                ],
            ],
        ]);

        return $dispute_query->posts;
    }

    public function get_open_disputes_for_user()
    {
        $commission_requests = $this->get_commission_requests_for_user();
        // Consulta para obtener las disputas asociadas a los contratos

        $commission_request_ids = [];
        foreach ($commission_requests as $commission_request) {
            $commission_request_ids[] = $commission_request->ID;
        }
        $dispute_query = new WP_Query([
            'post_type' => 'dispute',
            'meta_query' => [
                [
                    'key' => 'commission_request_id', // Asegúrate de que el meta_key sea correcto
                    'value' => $commission_request_ids,
                    'compare' => 'IN',
                ],
                [
                    'key' => 'status', // Asegúrate de que el meta_key sea correcto
                    'value' => "pending",
                    'compare' => '=',
                ],
            ],
        ]);

        return $dispute_query->posts;
    }

    public function has_open_dispute($commission_request_id)
    {

        $dispute_query = new WP_Query([
            'post_type' => 'dispute',
            'meta_query' => [
                [
                    'key' => 'commission_request_id', // Asegúrate de que el meta_key sea correcto
                    'value' => $commission_request_id,
                    'compare' => '=',
                ],
                [
                    'key' => 'status', // Asegúrate de que el meta_key sea correcto
                    'value' => "pending",
                    'compare' => '=',
                ],
            ],
        ]);

        return $dispute_query->posts;
    }

    public function get_payments_for_user()
    {
        $commission_requests = $this->get_commission_requests_for_user();
        // Consulta para obtener las disputas asociadas a los contratos

        $commission_request_ids = [];
        foreach ($commission_requests as $commission_request) {
            $commission_request_ids[] = $commission_request->ID;
        }
        $dispute_query = new WP_Query([
            'post_type' => 'payment',
            'meta_query' => [
                [
                    'key' => 'commission_request_id', // Asegúrate de que el meta_key sea correcto
                    'value' => $commission_request_ids,
                    'compare' => 'IN',
                ],
                [
                    'key' => 'user', // Asegúrate de que el meta_key sea correcto
                    'value' => get_current_user_id(),
                    'compare' => '=',
                ],
            ],
        ]);

        // Devolver los resultados
        return $dispute_query->posts;
    }

    public function get_user_associated_post_type($user_id = null )
    {
        $current_user = !$user_id?wp_get_current_user():get_user_by("ID",$user_id);

       if(empty($current_user->roles)){
            return false;
       }

        $query = new WP_Query([
            'post_type' => $current_user->roles[0],
            'meta_query' => [
                [
                    'key' => 'user_id',
                    'value' => $current_user->ID,
                    'compare' => '=',
                ],
            ],
        ]);

        return $query->posts[0] ?? false;

    }

    public function get_another_part_of_contract($post_id)
    {
        $current_user = wp_get_current_user();
        $counterparter_key = in_array("company", $current_user->roles) ? "commercial_agent" : "company";

        $counterparty_id = carbon_get_post_meta($post_id, $counterparter_key);

        $counterparty = get_post($counterparty_id);

        return $counterparty;

    }

    public function get_user_another_part_of_contract($post_id)
    {
        $current_user = wp_get_current_user();
        $counterparter_key = in_array("company", $current_user->roles) ? "commercial_agent" : "company";

        $counterparty_id = carbon_get_post_meta($post_id, $counterparter_key);

        $user_id = carbon_get_post_meta($counterparty_id, 'user_id');

        return get_user_by("ID", $user_id);

    }

}
