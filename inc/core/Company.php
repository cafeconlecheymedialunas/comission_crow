<?php
class Company
{
    private $user_id;
    private $company;
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

    private function set_current_company()
    {
        $this->user_id = get_current_user_id();
        $args = [
            'post_type' => 'company',
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
        $this->company = $query->posts[0] ?? null; // Devolver null si no hay resultados
    }

    public function get_company()
    {
        $this->set_current_company();
        return $this->company;
    }

    public function save_opportunity()
    {
      

        
        $errors = [];

        check_ajax_referer('create-opportunity-nonce', 'security');
    
        $post_title = sanitize_text_field($_POST['title']);
        $post_content =  wp_kses_post($_POST['content']);
        
        $target_audience = sanitize_text_field($_POST['target_audience']);
      

        $age = sanitize_text_field($_POST['age']);
        $gender = sanitize_text_field($_POST['gender']);
      
        $price = sanitize_text_field($_POST['price']);
        $commission = sanitize_text_field($_POST['commission']);
        $deliver_leads = isset($_POST['deliver_leads']) ? sanitize_text_field($_POST['deliver_leads']) : 'no';
        $sales_cycle_estimation = sanitize_text_field($_POST['sales_cycle_estimation']);
        $tips = sanitize_textarea_field($_POST['tips']);
        $question_1 = sanitize_textarea_field($_POST['question_1']);
        $question_2 = sanitize_textarea_field($_POST['question_2']);
        $question_3 = sanitize_textarea_field($_POST['question_3']);
        $question_4 = sanitize_textarea_field($_POST['question_4']);
        $question_5 = sanitize_textarea_field($_POST['question_5']);
        $question_6 = sanitize_textarea_field($_POST['question_6']);
        $images = isset($_POST['images']) ?  explode(',', sanitize_text_field($_POST['images'])) : '';
        $supporting_materials = isset($_POST['supporting_materials']) ?  explode(',', sanitize_text_field($_POST['supporting_materials'])) : '';
        $company = $_POST["company_id"];
        $videos =  $_POST['videos'];

        $language = isset($_POST["language"]) ? array_map('sanitize_text_field', $_POST["language"]) : [];
        $country = isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [];
        $currency = isset($_POST["currency"]) ? array_map('sanitize_text_field', $_POST["currency"]) : [];
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $type_of_company = isset($_POST["type_of_company"]) ? array_map('sanitize_text_field', $_POST["type_of_company"]) : [];

        
        $videos = array_map(function ($video_url) {
            return ['video' => sanitize_text_field($video_url)];
        }, $_POST['videos']);

        if (empty($post_title)) {
            $errors["title"][] = "Title field is required.";
        }

        if (empty($price)) {
            $errors["price"][] = "Price field is required.";
        }

        if (empty($commission)) {
            $errors["commission"][] = "Commission field is required.";
        }

        if (!is_numeric($price)) {
            $errors["price"][] = "Price must be a number.";
        }

        if (!is_numeric($commission)) {
            $errors["commission"][] = "Commission must be a number.";
        }

        // Specific validation for 'commission' field
        if ($commission <= 0 || $commission > 100) {
            $errors["commission"][] = "Commission must be between 1 and 100.";
        }

        // If there are errors, send JSON response with errors array
        if (!empty($errors)) {
            wp_send_json_error($errors);
            wp_die();
        }

        $post_data = [
            'post_title'    => $post_title,
            'post_content'  => $post_content,
            'post_status'   => 'publish',
            'post_type'     => 'opportunity',
        ];

        if(isset($_POST["opportunity_id"]) && !empty($_POST["opportunity_id"])) {

            $post_data["ID"] = $_POST["opportunity_id"];
            $opportunity_id =  wp_update_post($post_data);
        } else {
            $opportunity_id = wp_insert_post($post_data);
        }
        

        if (is_wp_error($opportunity_id)) {
            $operation = isset($_POST["opportunity_id"]) && !empty($_POST["opportunity_id"])?"updating":"creating";
            wp_send_json_error("Error when $operation the opportunity");
            wp_die();
        }

      


    
        
        if (!empty($language)) {
            wp_set_post_terms($opportunity_id, $language, 'language', false);
        }
    
        if (!empty($country)) {
            wp_set_post_terms($opportunity_id, $country, 'country', false);
        }
    
        if (!empty($currency)) {
            wp_set_post_terms($opportunity_id, $currency, 'currency', false);
        }
    
        if (!empty($industry)) {
            wp_set_post_terms($opportunity_id, $industry, 'industry', false);
        }
    
        if (!empty($type_of_company)) {
            wp_set_post_terms($opportunity_id, $type_of_company, 'type_of_company', false);
        }

    
        if (!empty($profile_image)) {
            set_post_thumbnail($opportunity_id, $profile_image);
        }
      
        carbon_set_post_meta($opportunity_id, 'target_audience', $target_audience);
        carbon_set_post_meta($opportunity_id, 'age', $age);
        carbon_set_post_meta($opportunity_id, 'gender', $gender);
        carbon_set_post_meta($opportunity_id, 'price', $price);
        carbon_set_post_meta($opportunity_id, 'commission', $commission);
        carbon_set_post_meta($opportunity_id, 'deliver_leads', $deliver_leads);
        carbon_set_post_meta($opportunity_id, 'sales_cycle_estimation', $sales_cycle_estimation);
        carbon_set_post_meta($opportunity_id, 'tips', $tips);
        carbon_set_post_meta($opportunity_id, 'question_1', $question_1);
        carbon_set_post_meta($opportunity_id, 'question_2', $question_2);
        carbon_set_post_meta($opportunity_id, 'question_3', $question_3);
        carbon_set_post_meta($opportunity_id, 'question_4', $question_4);
        carbon_set_post_meta($opportunity_id, 'question_5', $question_5);
        carbon_set_post_meta($opportunity_id, 'question_6', $question_6);
        carbon_set_post_meta($opportunity_id, 'images', $images);
        carbon_set_post_meta($opportunity_id, 'supporting_materials', $supporting_materials);
        carbon_set_post_meta($opportunity_id, 'videos', $videos);
        carbon_set_post_meta($opportunity_id, 'company', $company);
        
        // Envía respuesta JSON de éxito
        wp_send_json_success("Opportunity created successfully");
        wp_die();

    }
   
    public function get_agreements($statuses = [], $type = "")
    {
        $args = [
            'post_type' => 'agreement',
            'meta_query' => [
                [
                    'key' => 'company',
                    'value' => $this->company->ID,
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

        $agreements = [];
        $current_user = wp_get_current_user();

        foreach ($query->posts as $agreement) {
            $history_status = carbon_get_post_meta($agreement->ID, 'status_history');

            if (empty($history_status)) {
                continue;
            }

            $history_status_start = $history_status[0];
            $sender = isset($history_status_start["changed_by"]) ? $history_status_start["changed_by"] : null;

            if ($type == "requested" && $current_user->ID === $sender) {
                $agreements[] = $agreement;
            } elseif ($type == "received" && $current_user->ID !== $sender) {
                $agreements[] = $agreement;
            }
        }

        return $agreements;
    }

    



    public function save_company_profile()
    {
        $errors = [];
        $current_user = wp_get_current_user();
    
        check_ajax_referer('update-profile-nonce', 'security');
        $company_id = sanitize_text_field($_POST["company_id"]);

        $company_logo = sanitize_text_field($_POST["company_logo"]);
        $company_name = sanitize_text_field($_POST["company_name"]);
        $description = wp_kses_post($_POST['description']);
        $website_url = sanitize_text_field($_POST["website_url"]);
        $facebook_url = sanitize_text_field($_POST["facebook_url"]);
        $instagram_url = sanitize_text_field($_POST["instagram_url"]);
        $twitter_url = sanitize_text_field($_POST["twitter_url"]);
        $linkedin_url = sanitize_text_field($_POST["linkedin_url"]);
        $tiktok_url = sanitize_text_field($_POST["tiktok_url"]);
        $youtube_url = sanitize_text_field($_POST["youtube_url"]);
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $country = isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [];
        $type_of_company = isset($_POST["type_of_company"]) ? array_map('sanitize_text_field', $_POST["type_of_company"]) : [];
        $activity = isset($_POST["activity"]) ? array_map('sanitize_text_field', $_POST["activity"]) : [];
       

        $employees_number = sanitize_text_field($_POST["employees_number"]);
    
        if (empty($company_id)) {
            wp_send_json_error("Error when updating the profile");
        }
    
    
        if (!empty($errors)) {
            wp_send_json_error($errors);
            wp_die();
        }
    
        
    
        if (!$current_user) {
            wp_send_json_error("No se pudo recuperar el usuario");
        }
    
        $company_post_id = wp_update_post([
            "ID" => $company_id,
            'post_title' => $company_name,
            'post_status' => 'publish',
            'post_author' => $current_user->ID,
            "post_content" => $description
        ]);
    
        if (is_wp_error($company_post_id)) {
            wp_send_json_error("Error when updating the profile");
            wp_die();
        }
    
      
    
        if (!empty($industry)) {
            wp_set_post_terms($company_post_id, $industry, 'industry', false);
        }
    
        if (!empty($activity)) {
            wp_set_post_terms($company_post_id, $activity, 'activity', false);
        }
    
        if (!empty($country)) {
            wp_set_post_terms($company_post_id, $country, 'country', false);
        }
    
        if (!empty($type_of_company)) {
            wp_set_post_terms($company_post_id, $type_of_company, 'type_of_company', false);
        }

        if (!empty($company_logo)) {
            set_post_thumbnail($company_post_id, $company_logo);
        }

        carbon_set_post_meta($company_post_id, 'company_name', $company_name);
        carbon_set_post_meta($company_post_id, 'employees_number', $employees_number);
        carbon_set_post_meta($company_post_id, 'website_url', $website_url);
        carbon_set_post_meta($company_post_id, 'facebook_url', $facebook_url);
        carbon_set_post_meta($company_post_id, 'instagram_url', $instagram_url);
        carbon_set_post_meta($company_post_id, 'twitter_url', $twitter_url);
        carbon_set_post_meta($company_post_id, 'linkedin_url', $linkedin_url);
        carbon_set_post_meta($company_post_id, 'tiktok_url', $tiktok_url);
        carbon_set_post_meta($company_post_id, 'youtube_url', $youtube_url);
        wp_send_json_success("Profile updated successfully");
        wp_die();
    }
    

    public function get_opportunities()
    {
        $company = $this->get_company();
        $args = [
            'post_type' => 'opportunity',
            'meta_query' => [
                [
                    'key' => 'company',
                    'value' => $company->ID,
                    'compare' => '='
                ]
            ],
            'posts_per_page' => -1,
        ];
        $query = new WP_Query($args);
        return $query->posts; // Devolver los posts
    }

 

    public function delete_opportunity()
    {
      
    
        // Verificar el nonce para la seguridad
        check_ajax_referer('delete-opportunity-nonce', 'security');

        // Obtener el ID de la oportunidad a eliminar
        $opportunity_id = isset($_POST['opportunity_id']) ? intval($_POST['opportunity_id']) : 0;

        // Verificar si el ID es válido
        if (!$opportunity_id) {
            wp_send_json_error(['message' => 'Necesitas un ID válido.']);
        }

        // Verificar si el usuario actual tiene permisos para eliminar la oportunidad
        if (!current_user_can('delete_post', $opportunity_id)) {
            wp_send_json_error(['message' => 'No tienes permisos para eliminar esta oportunidad.']);
        }

        // Intentar eliminar la oportunidad
        if (wp_delete_post($opportunity_id, true)) {
            wp_send_json_success(['message' => 'Oportunidad eliminada correctamente.']);
        } else {
            wp_send_json_error(['message' => 'Error al eliminar la oportunidad. Inténtalo de nuevo más tarde.']);
        }
        wp_die();
    }
}
