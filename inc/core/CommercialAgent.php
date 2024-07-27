<?php
class CommercialAgent
{
    private $user_id;
    private $commercial_agent;
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
    private function set_current_agent()
    {
        $this->user_id = get_current_user_id();
        $args = [
            'post_type' => 'commercial_agent',
            'meta_query' => [
                [
                    'key' => 'user_id',
                    'value' => $this->user_id,
                    'compare' => '=',
                ],
            ],
            'posts_per_page' => -1,
        ];
        $query = new WP_Query($args);
        $this->commercial_agent = $query->posts[0] ?? null; // Devolver null si no hay resultados
    }

   

    public function get_commercial_agent()
    {
        $this->set_current_agent();
        return $this->commercial_agent;
    }

   
    
    public function save_agent_profile()
    {
        // Verifica el nonce de seguridad
      
        check_ajax_referer('update-profile-nonce', 'security');
    
        // Verifica que el usuario esté logueado
        if (!is_user_logged_in()) {
            wp_send_json_error(['general' => ['You must be logged in to update your profile.']]);
            wp_die();
        }
    
        $current_user = wp_get_current_user();
        $commercial_agent_id = sanitize_text_field($_POST["commercial_agent_id"]);
    
        $errors = new WP_Error();
        $field_errors = [];
    
        // Sanitización y validación de datos
        $profile_image = sanitize_text_field($_POST["profile_image"]);
        $description = wp_kses_post($_POST['description']);
        $languages = isset($_POST["language"]) ? array_map('sanitize_text_field', $_POST["language"]) : [];
        $country = isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [];
        $skills = isset($_POST["skill"]) ? array_map('sanitize_text_field', $_POST["skill"]) : [];
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $seller_type = isset($_POST["seller_type"]) ? array_map('sanitize_text_field', $_POST["seller_type"]) : [];
        $selling_method = isset($_POST["selling_method"]) ? array_map('sanitize_text_field', $_POST["selling_method"]) : [];
        $years_of_experience = sanitize_text_field($_POST["years_of_experience"]);
    
        // Validaciones
        if (empty($commercial_agent_id)) {
            $field_errors['commercial_agent_id'][] = __('Profile ID is required.');
        }
    
        if (empty($description)) {
            $field_errors['description'][] = __('Description is required.');
        }
    
        if (!is_numeric($years_of_experience) || $years_of_experience < 0) {
            $field_errors['years_of_experience'][] = __('Years of experience must be a positive number.');
        }
    
        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            wp_die();
        }
    
        // Actualizar el perfil del agente
        $agent_post_id = wp_update_post([
            "ID" => $commercial_agent_id,
            'post_title' => "$current_user->first_name $current_user->last_name",
            'post_status' => 'publish',
            'post_author' => $current_user->ID,
            "post_content" => $description
        ]);
    
        if (is_wp_error($agent_post_id)) {
            wp_send_json_error(['general' => [$agent_post_id->get_error_message()]]);
            wp_die();
        }
    
        // Guardar los datos adicionales
        carbon_set_post_meta($commercial_agent_id, 'years_of_experience', $years_of_experience);
    
        // Actualizar términos
        if (!empty($skills)) {
            wp_set_post_terms($commercial_agent_id, $skills, 'skill', false);
        }
    
        if (!empty($languages)) {
            wp_set_post_terms($commercial_agent_id, $languages, 'language', false);
        }
    
        if (!empty($country)) {
            wp_set_post_terms($commercial_agent_id, $country, 'country', false);
        }
    
        if (!empty($selling_method)) {
            wp_set_post_terms($commercial_agent_id, $selling_method, 'selling_method', false);
        }
    
        if (!empty($industry)) {
            wp_set_post_terms($commercial_agent_id, $industry, 'industry', false);
        }
    
        if (!empty($seller_type)) {
            wp_set_post_terms($commercial_agent_id, $seller_type, 'seller_type', false);
        }
    
        // Actualizar imagen del perfil
        if (!empty($profile_image)) {
            set_post_thumbnail($commercial_agent_id, $profile_image);
        }
    
        wp_send_json_success($current_user);
        wp_die();
    }
    
    
    
    public function get_contracts($statuses = [], $type = "")
    {
        $args = [
            'post_type' => 'contract',
            'meta_query' => [
                [
                    'key' => 'commercial_agent',
                    'value' => $this->commercial_agent->ID,
                    'compare' => '='
                ]
            ],
            'posts_per_page' => -1,
        ];

        // Verificar si $statuses es un array y no está vacío
        if (is_array($statuses) && !empty($statuses)) {
            $args['meta_query'][] = [
                'key' => 'status',
                'value' => $statuses,
                'compare' => 'IN' // Esto funcionará como un OR en meta_query
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
            $history_status = carbon_get_post_meta($contract->ID, 'status_history');

            if (empty($history_status)) {
                continue;
            }

            $history_status_start = $history_status[0];
            $sender = isset($history_status_start["changed_by"]) ? $history_status_start["changed_by"] : null;

            if ($type == "pending" && $current_user->ID === $sender) {
                $contracts[] = $contract;
            } elseif ($type == "received" && $current_user->ID !== $sender) {
                $contracts[] = $contract;
            }
        }

        return $contracts;
    }
}
