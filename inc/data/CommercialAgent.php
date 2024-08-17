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
        $location = isset($_POST["location"]) ? array_map('sanitize_text_field', $_POST["location"]) : [];
        $skills = isset($_POST["skill"]) ? array_map('sanitize_text_field', $_POST["skill"]) : [];
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $seller_type = isset($_POST["seller_type"]) ? array_map('sanitize_text_field', $_POST["seller_type"]) : [];
        $selling_method = isset($_POST["selling_method"]) ? array_map('sanitize_text_field', $_POST["selling_method"]) : [];
        $currency = isset($_POST["currency"]) ? array_map('sanitize_text_field', $_POST["currency"]) : [];
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
            "post_content" => $description,
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

        if (!empty($location)) {
            wp_set_post_terms($commercial_agent_id, $location, 'location', false);
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

        if (!empty($currency)) {
            wp_set_post_terms($commercial_agent_id, $currency, 'currency', false);
        }

        // Actualizar imagen del perfil
        // Subir y establecer la imagen del perfil
        if (!empty($_FILES['profile_image']['name'])) {
            $uploaded_file = $_FILES['profile_image'];
            $upload = wp_handle_upload($uploaded_file, ['test_form' => false]);

            if (isset($upload['error']) && $upload['error'] != 0) {
                wp_send_json_error(['general' => ['There was an error uploading the image.']]);
                wp_die();
            } else {
                $filetype = wp_check_filetype(basename($upload['file']), null);
                $attachment = [
                    'post_mime_type' => $filetype['type'],
                    'post_title' => sanitize_file_name($uploaded_file['name']),
                    'post_content' => '',
                    'post_status' => 'inherit',
                ];
                $attach_id = wp_insert_attachment($attachment, $upload['file'], $commercial_agent_id);
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                set_post_thumbnail($commercial_agent_id, $attach_id);
            }
        }

        wp_send_json_success($current_user);
        wp_die();
    }

    public function load_commercial_agents()
    {

        $industry_filter = isset($_GET['industry']) ? $_GET['industry'] : [];
        $language_filter = isset($_GET['language']) ? $_GET['language'] : [];
        $location_filter = isset($_GET['location']) ? $_GET['location'] : [];
        $seller_type_filter = isset($_GET['seller_type']) ? $_GET['seller_type'] : [];
        $selling_method_filter = isset($_GET['selling_method']) ? $_GET['selling_method'] : [];
        $years_of_experience = isset($_GET['years_of_experience']) ? intval($_GET['years_of_experience']) : null;

        // Configuración básica de la consulta
        $query_args = array(
            'post_type' => 'commercial_agent',
            'posts_per_page' => -1,
        );

        if ($industry_filter || $language_filter || $location_filter || $seller_type_filter || $selling_method_filter) {
            $tax_query = array('relation' => 'AND');

            if ($language_filter) {
                $tax_query['tax_query'][] = [
                    'taxonomy' => 'language',
                    'field' => 'term_id',
                    'terms' => $language_filter,
                    'operator' => 'IN', // Todos los términos deben estar presentes
                ];
            }

            if ($selling_method_filter) {
                $tax_query['tax_query'][] = [
                    'taxonomy' => 'selling_method',
                    'field' => 'term_id',
                    'terms' => $selling_method_filter,
                    'operator' => 'IN', // Todos los términos deben estar presentes
                ];
            }

            if ($industry_filter) {
                $tax_query['tax_query'][] = [
                    'taxonomy' => 'industry',
                    'field' => 'term_id',
                    'terms' => $industry_filter,
                    'operator' => 'IN',
                ];
            }

            if ($location_filter) {
                $tax_query['tax_query'][] = [
                    'taxonomy' => 'location',
                    'field' => 'term_id',
                    'terms' => $location_filter,
                    'operator' => 'IN',
                ];
            }

            if ($seller_type_filter) {
                $tax_query['tax_query'][] = [
                    'taxonomy' => 'seller_type',
                    'field' => 'term_id',
                    'terms' => $seller_type_filter,
                    'operator' => 'IN',
                ];
            }
            $query_args['tax_query'] = $tax_query;
        }

        if ($years_of_experience) {
            $meta_query = array('relation' => 'AND');
            if ($years_of_experience) {
                $query_args['meta_query'][] = [
                    'key' => 'years_of_experience',
                    'value' => $years_of_experience,
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                ];
            }
            $query_args['meta_query'] = $meta_query;
        }

        $commercial_agents = new WP_Query($query_args);

        // Preparar la respuesta
        if ($commercial_agents->have_posts()) {
            foreach ($commercial_agents->posts as $commercial_agent):
                $user_id = get_post_meta($commercial_agent->ID, '_user_id',true);
                $user = get_user_by("ID",$user_id);
            
                $years_of_experience = get_post_meta($commercial_agent->ID, 'years_of_experience', true);
                $profile_image = get_the_post_thumbnail($commercial_agent->ID, "thumbnail", [
                    'class' => 'profile-image',
                ]);
                $industry_terms = wp_get_post_terms($commercial_agent->ID, 'industry');
                $selling_method_terms = wp_get_post_terms($commercial_agent->ID, 'selling_method');
                $seller_type_terms = wp_get_post_terms($commercial_agent->ID, 'seller_type');
                $location_terms = wp_get_post_terms($commercial_agent->ID, 'location');
                $language_terms = wp_get_post_terms($commercial_agent->ID, 'language');

                $industry_names = array_map(fn($term) => esc_html($term->name), $industry_terms);
                $selling_method_names = array_map(fn($term) => esc_html($term->name), $selling_method_terms);
                $seller_type_names = array_map(fn($term) => esc_html($term->name), $seller_type_terms);
                $location_names = array_map(fn($term) => esc_html($term->name), $location_terms);
                $language_names = array_map(fn($term) => esc_html($term->name), $language_terms);
                ?>
				            <div class="result-item row">
		                        <div class="col-md-3">
		                        <?php if ($profile_image): ?>
				                    <?php echo $profile_image; ?>
				                <?php endif;
                                $agent_id = $commercial_agent->ID;
                                $avg_rating_template = 'templates/avg-rating.php';
                                if (locate_template($avg_rating_template)) {
                                    include locate_template($avg_rating_template);
                                }
                                ?>
                            <a href="<?php echo home_url('/commercial-agent-item/?commercial_agent_id=' . $commercial_agent->ID); ?>" class="btn btn-primary">Detail</a>
                        </div>

                <div class="info col-md-9">
                    <div class="meta">
                        <h3 class="title"><?php echo esc_html($commercial_agent->post_title); ?></h3>
                        <?php if($user && $user->user_registered):?>
                        <p>Member since <?php echo $user->user_registered; ?></p>
                        <?php endif;?>
                        <ul class="list-inline">


                        <?php if ($industry_names): ?>
                            <li class="industry list-inline-item"><i class="fa-solid fa-list"></i><?php echo implode(', ', $industry_names); ?></li>
                        <?php endif;?>
                        <?php if ($location_names): ?>
                            <li class="location list-inline-item"><i class="fa-solid fa-location-crosshairs"></i><?php echo implode(', ', $location_names); ?></li>
                        <?php endif;?>
                        <?php if ($language_names): ?>
                            <li class="language list-inline-item"><i class="fa-solid fa-language"></i><?php echo implode(', ', $language_names); ?></li>
                        <?php endif;?>
                        </ul>
                    </div>
                    <div class="type">
                        <?php if ($selling_method_names): ?>
                            <p class="selling-methods">Selling methods: <?php echo implode(', ', $selling_method_names); ?></p>
                        <?php endif;?>
                        <?php if ($seller_type_names): ?>
                            <p class="seller-type">Seller Type: <?php echo implode(', ', $seller_type_names); ?></p>
                        <?php endif;?>
                    </div>
                </div>


            </div>
            <?php
endforeach;
            wp_reset_postdata();
        } else {
            echo '<p>No agents found.</p>';
        }

        wp_die(); // Termina la ejecución para solicitudes AJAX
    }

    public function get_contracts($statuses = [], $type = "")
    {
        $args = [
            'post_type' => 'contract',
            'meta_query' => [
                [
                    'key' => 'commercial_agent',
                    'value' => $this->commercial_agent->ID,
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
