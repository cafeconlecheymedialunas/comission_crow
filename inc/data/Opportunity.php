<?php
class Opportunity extends Crud
{
    private function __construct()
    {
      
        parent::__construct('opportunity');
        
    }
    private static $instance = null;
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function save_opportunity()
    {
        check_ajax_referer('create-opportunity-nonce', 'security');
        $current_user = wp_get_current_user();

        $general_errors = [];
        $errors = [];
             
        // Sanitización de datos
        $data = [
            'post_title' => sanitize_text_field($_POST['title']), // Asegurarse de usar 'post_title'
            'post_content' => wp_kses_post($_POST['content']), // Asegurarse de usar 'post_content'
            'target_audience' => sanitize_text_field($_POST['target_audience']),
            'age' => sanitize_text_field($_POST['age']),
            'gender' => sanitize_text_field($_POST['gender']),
            'price' => sanitize_text_field($_POST['price']),
            'commission' => sanitize_text_field($_POST['commission']),
            'deliver_leads' => isset($_POST['deliver_leads']) ? sanitize_text_field($_POST['deliver_leads']) : 'no',
            'sales_cycle_estimation' => sanitize_text_field($_POST['sales_cycle_estimation']),
            'tips' => sanitize_textarea_field($_POST['tips']),
            'question_1' => sanitize_textarea_field($_POST['question_1']),
            'question_2' => sanitize_textarea_field($_POST['question_2']),
            'question_3' => sanitize_textarea_field($_POST['question_3']),
            'question_4' => sanitize_textarea_field($_POST['question_4']),
            'question_5' => sanitize_textarea_field($_POST['question_5']),
            'question_6' => sanitize_textarea_field($_POST['question_6']),
            'company_id' => isset($_POST["company_id"]) ? sanitize_text_field($_POST["company_id"]) : '',
            'videos' => isset($_POST['videos']) ? array_map('sanitize_text_field', $_POST['videos']) : [],
            'language' => isset($_POST["language"]) ? array_map('sanitize_text_field', $_POST["language"]) : [],
            'country' => isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [],
            'currency' => isset($_POST["currency"]) ? array_map('sanitize_text_field', $_POST["currency"]) : [],
            'industry' => isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [],
            'type_of_company' => isset($_POST["type_of_company"]) ? array_map('sanitize_text_field', $_POST["type_of_company"]) : [],
            'post_author' => $current_user->ID,
            'opportunity_id' => isset($_POST['opportunity_id']) ? (int) $_POST['opportunity_id'] : null,
        ];

        // Validación
       

        if (empty($data['post_title'])) {
            $errors["title"][] = __("Title field is required.");
        }

        if (empty($data['price'])) {
            $errors["price"][] = __("Price field is required.");
        } elseif (!is_numeric($data['price'])) {
            $errors["price"][] = __("Price must be a number.");
        }

        if (empty($data['commission'])) {
            $errors["commission"][] = __("Commission field is required.");
        } elseif (!is_numeric($data['commission'])) {
            $errors["commission"][] = __("Commission must be a number.");
        } elseif ($data['commission'] <= 0 || $data['commission'] > 100) {
            $errors["commission"][] = __("Commission must be between 1 and 100.");
        }

        $error_videos = [];
        foreach ($data['videos'] as $video_url) {
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

        $company = get_user_associated_post_type();

        
        if(is_wp_error($company) || empty($company->ID)) {
            $general_errors = "You need be a company to create an opportunity";
        }

        $data["company"] = $company->ID;

        if(isset($_POST["opportunity_id"])) {
            $opportunity = get_post($_POST["opportunity_id"]);

            if(is_wp_error($opportunity)) {
                $general_errors[] = "Opportunity does not exist";
            }

            $opportunity_id = $opportunity->ID;
            
        }

        if (!empty($errors)) {
            wp_send_json_error(['fields' => $errors]);
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

  

        $field_mappings = [
            "company" => "Company",
            'target_audience' => 'Target Audience',
            'age' => 'Age',
            'gender' => 'Gender',
            'price' => 'Price',
            'commission' => 'Commission',
            'deliver_leads' => 'Deliver Leads',
            'sales_cycle_estimation' => 'Sales Cycle Estimation',
            'tips' => 'Tips',
            'question_1' => 'Question 1',
            'question_2' => 'Question 2',
            'question_3' => 'Question 3',
            'question_4' => 'Question 4',
            'question_5' => 'Question 5',
            'question_6' => 'Question 6',
            'company_id' => 'Company ID',
            'videos' => 'Videos',
            'images' => 'Images',
            'supporting_materials' => 'Supporting Materials',
        ];

        if (!empty($general_errors)) {
            wp_send_json_error(['general' => $general_errors]);
        }
    
        $opportunity_id = isset($opportunity_id) && !empty($opportunity_id) ? $this->update($opportunity_id, $data, $field_mappings) : $this->create($data, $field_mappings);
        if($this->has_errors()) {
            wp_send_json_error(['general' => $this->get_errors()]);
        }

        

        
     
        if (!empty($data['language'])) {
            wp_set_post_terms($opportunity_id, $data['language'], 'language');
        }
    
        if (!empty($data['country'])) {
            wp_set_post_terms($opportunity_id, $data['country'], 'country');
        }
    
        if (!empty($data['currency'])) {
            wp_set_post_terms($opportunity_id, $data['currency'], 'currency');
        }
    
        if (!empty($data['industry'])) {
            wp_set_post_terms($opportunity_id, $data['industry'], 'industry');
        }
    
        if (!empty($data['type_of_company'])) {
            wp_set_post_terms($opportunity_id, $data['type_of_company'], 'type_of_company');
        }
       
        
        wp_send_json_success(['message' => __('Opportunity saved successfully!'), 'opportunity_id' => $opportunity_id]);
        

        wp_die();
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
