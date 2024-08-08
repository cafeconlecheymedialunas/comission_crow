<?php
class Company extends Crud
{
    private $user_id;
    private $company;
    private function __construct()
    {
      
        parent::__construct('company');
        
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
    
        $company_logo = $_FILES["company_logo"];
    
       

        $company_name = sanitize_text_field($_POST["company_name"]);
        
        $company_street = sanitize_text_field($_POST["company_street"]);
        $company_number = sanitize_text_field($_POST["company_number"]);
        $company_city = sanitize_text_field($_POST["company_city"]);
        $company_postalcode = sanitize_text_field($_POST["company_postalcode"]);
        $company_state = sanitize_text_field($_POST["company_state"]);

        $post_content = wp_kses_post($_POST['post_content']);
        $website_url = esc_url_raw($_POST["website_url"]);
        $facebook_url = esc_url_raw($_POST["facebook_url"]);
        $instagram_url = esc_url_raw($_POST["instagram_url"]);
        $twitter_url = esc_url_raw($_POST["twitter_url"]);
        $linkedin_url = esc_url_raw($_POST["linkedin_url"]);
        $tiktok_url = esc_url_raw($_POST["tiktok_url"]);
        $youtube_url = esc_url_raw($_POST["youtube_url"]);
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $country = isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [];
        $type_of_company = isset($_POST["type_of_company"]) ? array_map('sanitize_text_field', $_POST["type_of_company"]) : [];
        $activity = isset($_POST["activity"]) ? array_map('sanitize_text_field', $_POST["activity"]) : [];
        $employees_number = sanitize_text_field($_POST["employees_number"]);
    
        $errors = new WP_Error();
        $field_errors = [];
    
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
    
        $data = [
            'post_title' => $company_name,
            'post_content' => $post_content,
            'website_url' => $website_url,
            'facebook_url' => $facebook_url,
            'instagram_url' => $instagram_url,
            'twitter_url' => $twitter_url,
            'linkedin_url' => $linkedin_url,
            'tiktok_url' => $tiktok_url,
            'youtube_url' => $youtube_url,
            'company_name' => $company_name,
            'company_street' => $company_street,
            'company_number' => $company_number,
            'company_city' => $company_city,
            'company_state' => $company_state,
            'company_postalcode' => $company_postalcode,
            'employees_number' => $employees_number
        ];
    
        $field_mappings = [
            'website_url' => 'Website URL',
            'facebook_url' => 'Facebook URL',
            'instagram_url' => 'Instagram URL',
            'twitter_url' => 'Twitter URL',
            'linkedin_url' => 'LinkedIn URL',
            'tiktok_url' => 'TikTok URL',
            'youtube_url' => 'YouTube URL',
            'company_name' => 'Company Name',
          
            'company_street' => 'Street Address',
            'company_number' => 'Number Address',
            'company_city' => 'City',
            'company_state' => 'State',
            'company_postalcode' => 'Post code',
            'employees_number' => 'Number of Employees'
        ];
    
        
        $update_result = $this->update($company_id, $data, $field_mappings);
        if (is_wp_error($update_result)) {
            wp_send_json_error(['general' => [$update_result->get_error_message()]]);
            wp_die();
        }
    
        if (!empty($industry)) {
            wp_set_post_terms($company_id, $industry, 'industry', false);
        }
        if (!empty($activity)) {
            wp_set_post_terms($company_id, $activity, 'activity', false);
        }
        if (!empty($country)) {
            wp_set_post_terms($company_id, $country, 'country', false);
        }
        if (!empty($type_of_company)) {
            wp_set_post_terms($company_id, $type_of_company, 'type_of_company', false);
        }
    
        if (!empty($company_logo['name'])) {
            $upload_result = $this->handle_company_logo_upload($company_logo, $company_id);
            if (is_wp_error($upload_result)) {
                wp_send_json_error(['general' => [$upload_result->get_error_message()]]);
                wp_die();
            }
        }
    
        wp_send_json_success($current_user);
        wp_die();
    }

    private function handle_company_logo_upload($company_logo, $company_id)
    {
        $upload = wp_handle_upload($company_logo, ['test_form' => false]);
    
        if (isset($upload['error']) && $upload['error'] != 0) {
            return new WP_Error('upload_error', 'There was an error uploading the image.');
        }
    
        $filetype = wp_check_filetype(basename($upload['file']), null);
        $attachment = [
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name($company_logo['name']),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        $attach_id = wp_insert_attachment($attachment, $upload['file'], $company_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($company_id, $attach_id);
    
        return true;
    }



    public function get_opportunities()
    {
        $args = [
            'post_type' => 'opportunity',
            'meta_query' => [
                [
                    'key' => 'company',
                    'value' => $this->company->ID,
                    'compare' => '='
                ]
            ],
            'posts_per_page' => -1,
        ];
        $query = new WP_Query($args);
        return $query->posts; // Devolver los posts
    }

 


}
