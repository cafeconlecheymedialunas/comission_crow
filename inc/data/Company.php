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
        $this->user_id = wp_get_current_user();
        $args = [
            'post_type' => 'company',
            'meta_query' => [
                [
                    'key' => 'user_id',
                    'value' => $this->user_id->ID,
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
        check_ajax_referer('create-opportunity-nonce', 'security');
        $current_user = wp_get_current_user();
      
        
        // Sanitización de datos
        $post_title = sanitize_text_field($_POST['title']);
        $post_content = wp_kses_post($_POST['content']);
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
        $company = isset($_POST["company_id"]) ? sanitize_text_field($_POST["company_id"]) : '';
        $videos = isset($_POST['videos']) ? array_map('sanitize_text_field', $_POST['videos']) : [];
        $language = isset($_POST["language"]) ? array_map('sanitize_text_field', $_POST["language"]) : [];
        $country = isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [];
        $currency = isset($_POST["currency"]) ? array_map('sanitize_text_field', $_POST["currency"]) : [];
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $type_of_company = isset($_POST["type_of_company"]) ? array_map('sanitize_text_field', $_POST["type_of_company"]) : [];
        
        $errors = [];
        
        // Validación
        if (empty($post_title)) {
            $errors["title"][] = __("Title field is required.");
        }
        
        if (empty($price)) {
            $errors["price"][] = __("Price field is required.");
        } elseif (!is_numeric($price)) {
            $errors["price"][] = __("Price must be a number.");
        }
        
        if (empty($commission)) {
            $errors["commission"][] = __("Commission field is required.");
        } elseif (!is_numeric($commission)) {
            $errors["commission"][] = __("Commission must be a number.");
        } elseif ($commission <= 0 || $commission > 100) {
            $errors["commission"][] = __("Commission must be between 1 and 100.");
        }
        
        $error_videos = [];
        foreach ($videos as $video_url) {
            if (empty($video_url)) {
                $error_videos[] = true;
                break;
            }
        }
        if (!empty($error_videos)) {
            $errors["videos"][] = __("One or more video URLs are not valid.");
        }
    
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_image_size = 5 * 1024 * 1024; // 5 MB

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $image_type = $_FILES['images']['type'][$key];
                $image_size = $_FILES['images']['size'][$key];

                if (!in_array($image_type, $allowed_image_types)) {
                    $errors['images'][] = __("Invalid file type for image: ") . $image_name;
                }

                if ($image_size > $max_image_size) {
                    $errors['images'][] = __("Image size exceeds the limit of 5MB: ") . $image_name;
                }
            }
        }
        
        $allowed_supporting_types = ['application/pdf', 'text/plain'];
        $max_supporting_size = 10 * 1024 * 1024; // 10 MB
        
        if (isset($_FILES['supporting_materials']) && !empty($_FILES['supporting_materials']['name'][0])) {
            foreach ($_FILES['supporting_materials']['name'] as $key => $file_name) {
                // Verifica el error del archivo
                if ($_FILES['supporting_materials']['error'][$key] !== 0) {
                    $errors['supporting_materials'][] = __("Error in file upload: ") . $file_name;
                    continue; // Salta al siguiente archivo
                }
        
                $file_type = $_FILES['supporting_materials']['type'][$key];
                $file_size = $_FILES['supporting_materials']['size'][$key];
        
                if (!in_array($file_type, $allowed_supporting_types)) {
                    $errors['supporting_materials'][] = __("Invalid file type for supporting material: ") . $file_name;
                }
        
                if ($file_size > $max_supporting_size) {
                    $errors['supporting_materials'][] = __("Supporting material size exceeds the limit of 10MB: ") . $file_name;
                }
            }
        }

        if (!empty($errors)) {
            wp_send_json_error(['fields' => $errors]);
            wp_die();
        }
    
        $uploaded_image_ids = [];
        $uploaded_supporting_material_ids = [];
    
    
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $file = [
                        'name'     => $_FILES['images']['name'][$key],
                        'type'     => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error'    => $_FILES['images']['error'][$key],
                        'size'     => $_FILES['images']['size'][$key]
                    ];
        
                    $attachment_id = media_handle_sideload($file, 0);
                    
                    if (is_wp_error($attachment_id)) {
                        $errors['images'][] = __("Error when saving Images") ;
                    } else {
                        $uploaded_image_ids[] = $attachment_id;
                    }
                }
            }
        }

        if (isset($_FILES['supporting_materials']) && !empty($_FILES['supporting_materials']['name'][0])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
    
            foreach ($_FILES['supporting_materials']['name'] as $key => $file_name) {
                if ($_FILES['supporting_materials']['error'][$key] === 0) {
                    $file = [
                        'name'     => $_FILES['supporting_materials']['name'][$key],
                        'type'     => $_FILES['supporting_materials']['type'][$key],
                        'tmp_name' => $_FILES['supporting_materials']['tmp_name'][$key],
                        'error'    => $_FILES['supporting_materials']['error'][$key],
                        'size'     => $_FILES['supporting_materials']['size'][$key]
                    ];
    
                    $attachment_id = media_handle_sideload($file, 0);
    
                    if (is_wp_error($attachment_id)) {
                        $errors['supporting_materials'][] = __("Error when saving Supporting Materials Files");
                    } else {
                        $uploaded_supporting_material_ids[] = $attachment_id;
                    }
                }
            }
        }
    
        if (!empty($errors)) {
            wp_send_json_error(['fields' => $errors]);
            wp_die();
        }
    
        $post_data = [
            'post_title'    => $post_title,
            'post_content'  => $post_content,
            'post_status'   => 'publish',
            'post_type'     => 'opportunity',
        ];
    
        if (isset($_POST["opportunity_id"]) && !empty($_POST["opportunity_id"])) {
            $post_data["ID"] = $_POST["opportunity_id"];
            $opportunity_id = wp_update_post($post_data);
        } else {
            $opportunity_id = wp_insert_post($post_data);
        }
    
        if (is_wp_error($opportunity_id)) {
            $operation = isset($_POST["opportunity_id"]) && !empty($_POST["opportunity_id"]) ? "updating" : "creating";
            wp_send_json_error(['general' => __("Error when $operation the opportunity: ") ]);
            wp_die();
        }
    
        // Actualizar términos
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
    
        // Guardar metadatos adicionales
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
        carbon_set_post_meta($opportunity_id, 'company', $company);
        carbon_set_post_meta($opportunity_id, 'videos', $videos);
    
     
        if (!empty($uploaded_image_ids)) {
            carbon_set_post_meta($opportunity_id, 'images', $uploaded_image_ids);
        }
    
        if (!empty($uploaded_supporting_material_ids)) {
            carbon_set_post_meta($opportunity_id, 'supporting_materials', $uploaded_supporting_material_ids);
        }
    
        // Enviar respuesta de éxito
        wp_send_json_success($current_user);
        wp_die();
    }
    
    
    
   
    public function get_contracts($statuses = [], $type = "")
    {
        $args = [
            'post_type' => 'contract',
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

    



    public function save_company_profile()
    {
        check_ajax_referer('update-profile-nonce', 'security');
        $current_user = wp_get_current_user();
        $company_id = sanitize_text_field($_POST["company_id"]);

        // Sanitización de datos
        $company_logo = sanitize_text_field($_POST["company_logo"]);
        $company_name = sanitize_text_field($_POST["company_name"]);
        $description = wp_kses_post($_POST['description']);
        $website_url = $_POST["website_url"];
        $facebook_url = $_POST["facebook_url"];
        $instagram_url = $_POST["instagram_url"];
        $twitter_url = $_POST["twitter_url"];
        $linkedin_url = $_POST["linkedin_url"];
        $tiktok_url = $_POST["tiktok_url"];
        $youtube_url = $_POST["youtube_url"];
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $country = isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [];
        $type_of_company = isset($_POST["type_of_company"]) ? array_map('sanitize_text_field', $_POST["type_of_company"]) : [];
        $activity = isset($_POST["activity"]) ? array_map('sanitize_text_field', $_POST["activity"]) : [];
        $employees_number = sanitize_text_field($_POST["employees_number"]);

        $errors = new WP_Error();
        $field_errors = [];

        // Validaciones
        if (empty($company_id)) {
            $field_errors['company_id'][] = __('Company ID is required.');
        }

        if (empty($company_name)) {
            $field_errors['company_name'][] = __('Company name is required.');
        }

        if (!is_numeric($employees_number) || $employees_number < 0) {
            $field_errors['employees_number'][] = __('Number of employees must be a positive number.');
        }
      


        if (!empty($field_errors)) {
            wp_send_json_error(['fields' => $field_errors]);
            wp_die();
        }

        // Actualizar el perfil de la empresa
        $company_post_id = wp_update_post([
            "ID" => $company_id,
            'post_title' => $company_name,
            'post_status' => 'publish',
            'post_author' => $current_user->ID,
            "post_content" => $description
        ]);

        if (is_wp_error($company_post_id)) {
            wp_send_json_error(['general' => [$company_post_id->get_error_message()]]);
            wp_die();
        }

        // Actualizar términos
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

        // Actualizar imagen de la empresa
        if (!empty($company_logo)) {
            set_post_thumbnail($company_post_id, $company_logo);
        }

        // Subir la imagen de la empresa
        if (!empty($_FILES['company_logo']['name'])) {
            $uploaded_file = $_FILES['company_logo'];
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
                    'post_status' => 'inherit'
                ];
                $attach_id = wp_insert_attachment($attachment, $upload['file'], $company_id);
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                set_post_thumbnail($company_post_id, $attach_id);
            }
        }

        // Guardar metadatos adicionales
        carbon_set_post_meta($company_post_id, 'company_name', $company_name);
        carbon_set_post_meta($company_post_id, 'employees_number', $employees_number);
        carbon_set_post_meta($company_post_id, 'website_url', $website_url);
        carbon_set_post_meta($company_post_id, 'facebook_url', $facebook_url);
        carbon_set_post_meta($company_post_id, 'instagram_url', $instagram_url);
        carbon_set_post_meta($company_post_id, 'twitter_url', $twitter_url);
        carbon_set_post_meta($company_post_id, 'linkedin_url', $linkedin_url);
        carbon_set_post_meta($company_post_id, 'tiktok_url', $tiktok_url);
        carbon_set_post_meta($company_post_id, 'youtube_url', $youtube_url);

        wp_send_json_success($current_user);
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


        // Intentar eliminar la oportunidad
        if (wp_delete_post($opportunity_id, true)) {
            wp_send_json_success(['message' => 'Oportunidad eliminada correctamente.']);
        } else {
            wp_send_json_error(['message' => 'Error al eliminar la oportunidad. Inténtalo de nuevo más tarde.']);
        }
        wp_die();
    }
}
