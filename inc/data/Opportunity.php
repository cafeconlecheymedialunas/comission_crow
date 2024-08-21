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

        $data = [
            'post_title' => sanitize_text_field($_POST['title']),
            'post_content' => wp_kses_post($_POST['post_content']),
            'target_audience' => isset($_POST["target_audience"]) ? $_POST["target_audience"] : [],
            'age' => isset($_POST["age"]) ? $_POST["age"] : [],
            'gender' => isset($_POST["gender"]) ? $_POST["gender"] : [],
            'price' => sanitize_text_field($_POST['price']),
            'commission' => sanitize_text_field($_POST['commission']),
            "date" => current_time("mysql"),
            'deliver_leads' => isset($_POST['deliver_leads']) && $_POST['deliver_leads'] === 'yes' ? true : false,
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
            'location' => isset($_POST["location"]) ? array_map('sanitize_text_field', $_POST["location"]) : [],
            'currency' => isset($_POST["currency"]) ? array_map('sanitize_text_field', $_POST["currency"]) : [],
            'industry' => isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [],
            'post_author' => $current_user->ID,
            'opportunity_id' => isset($_POST['opportunity_id']) ? (int) $_POST['opportunity_id'] : null,
        ];

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

        $minimal_price  = Helper::convert_to_usd($data["price"]);
        if (is_wp_error($minimal_price)) {
          
            wp_send_json_error([
                'general' => $minimal_price->get_error_message(),
            ]);
        }
        $error_videos = [];
        $videos = [];
        foreach ($data['videos'] as $video_url) {
            if (empty($video_url)) {
                $error_videos[] = true;
                break;
            }
            $videos[]["video"] = $video_url;
        }

        $data["videos"] = $videos;
        if (!empty($error_videos)) {
            $errors["videos"][] = __("One or more video URLs are not valid.");
        }

        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_image_size = 5 * 1024 * 1024;

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $validation_result = Helper::validate_files($_FILES['images'], $allowed_image_types, $max_image_size);
            if (!$validation_result["success"]) {
                $errors['images'][] = $validation_result["errors"][0];
            }
        }

        $allowed_supporting_types = ['application/pdf', 'text/plain'];
        $max_supporting_size = 10 * 1024 * 1024;

        if (isset($_FILES['supporting_materials']) && !empty($_FILES['supporting_materials']['name'][0])) {
            $validation_result = Helper::validate_files($_FILES['supporting_materials'], $allowed_supporting_types, $max_supporting_size);
            if (!$validation_result["success"]) {
                $errors['supporting_materials'][] = $validation_result["errors"][0];
            }
        }

        $company = ProfileUser::get_instance()->get_user_associated_post_type();
        if (is_wp_error($company) || empty($company->ID)) {
            $general_errors[] = __("You need to be a company to create an opportunity.");
        }
        $data["company"] = $company->ID;

        if (isset($_POST["opportunity_id"])) {
            $opportunity = get_post($_POST["opportunity_id"]);
            if (is_wp_error($opportunity)) {
                $general_errors[] = __("Opportunity does not exist.");
            }
            $opportunity_id = $opportunity->ID;
        }

        if (!empty($errors)) {
            wp_send_json_error(['fields' => $errors]);
        }

        $uploaded_image_ids = Helper::handle_multiple_file_upload($_FILES['images']);
        $uploaded_supporting_material_ids = Helper::handle_multiple_file_upload($_FILES['supporting_materials']);
        $data["images"] = $uploaded_image_ids;
        $data["supporting_materials"] = $uploaded_supporting_material_ids;

        $field_mappings = [
            "company" => "Company",
            'price' => 'Price',
            "date" => "Date",
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
        if ($this->has_errors()) {
            wp_send_json_error(['general' => $this->get_errors()]);
        }

        if (!empty($data['language'])) {
            wp_set_post_terms($opportunity_id, $data['language'], 'language');
        }

        if (!empty($data['location'])) {
            wp_set_post_terms($opportunity_id, $data['location'], 'location');
        }

        if (!empty($data['currency'])) {
            wp_set_post_terms($opportunity_id, $data['currency'], 'currency');
        }

        if (!empty($data['industry'])) {
            wp_set_post_terms($opportunity_id, $data['industry'], 'industry');
        }

       
        if (!empty($data['target_audience'])) {
            wp_set_post_terms($opportunity_id, $data['target_audience'], 'target_audience');
        }
        if (!empty($data['age'])) {
            wp_set_post_terms($opportunity_id, $data['age'], 'age');
        }
        if (!empty($data['gender'])) {
            wp_set_post_terms($opportunity_id, $data['gender'], 'gender');
        }

        $this->send_opportunity_created_email_to_company($opportunity_id);

        wp_send_json_success(["general" => __('Opportunity saved successfully!'), 'opportunity_id' => $opportunity_id]);
        wp_die();
    }

    public function delete_opportunity()
    {
        check_ajax_referer('delete-opportunity-nonce', 'security');
        $opportunity_id = isset($_POST['opportunity_id']) ? intval($_POST['opportunity_id']) : 0;

        if (!$opportunity_id) {
            wp_send_json_error(['general' => 'You need a valid ID.']);
        }

        $opportunity = get_post($opportunity_id);

        if (!$opportunity || $opportunity->post_type !== 'opportunity') {
            wp_send_json_error(['general' => 'Opportunity not found.']);
        }

        $contracts_query = new WP_Query([
            'post_type' => 'contract', // Update this to the post type used for contracts
            'meta_query' => [
                [
                    'key' => 'opportunity', // Update this to the meta field that links contracts to opportunities
                    'value' => $opportunity_id,
                    'compare' => '=',
                ],
            ],
        ]);

        // Check if there are any active contracts associated
        if ($contracts_query->have_posts()) {
            wp_send_json_error(['general' => 'Cannot delete the opportunity because there are active contracts associated.']);
        }

        $deleted = wp_delete_post($opportunity_id, true);

        if ($deleted) {
            $this->send_opportunity_deleted_email_to_company($opportunity_id);
            wp_send_json_success(["general" => 'Opportunity deleted successfully.']);
        } else {
            wp_send_json_error(["general" => 'Error deleting the opportunity. Please try again later.']);
        }
        wp_die();
    }

    public function load_opportunities()
    {
        ob_start();
    
        $industry_filter = isset($_GET['industry']) ? $_GET['industry'] : [];
        $language_filter = isset($_GET['language']) ? $_GET['language'] : [];
        $location_filter = isset($_GET['location']) ? $_GET['location'] : [];
        $currency_filter = isset($_GET['currency']) ? $_GET['currency'] : [];
        $target_audience_filter = isset($_GET['target_audience']) ? $_GET['target_audience'] : [];
        $age_filter = isset($_GET['age']) ? $_GET['age'] : [];
        $gender_filter = isset($_GET['gender']) ? $_GET['gender'] : [];
        $deliver_leads_filter = isset($_GET['deliver_leads']) ? $_GET['deliver_leads'] : null;
        $min_price = isset($_GET['minimum_price']) ? floatval($_GET['minimum_price']) : null;
        $max_price = isset($_GET['maximum_price']) ? floatval($_GET['maximum_price']) : null;
        $commission = isset($_GET['commission']) ? intval($_GET['commission']) : null;
        $search_term = isset($_GET['s']) && $_GET["post_type"] === "opportunity" ? sanitize_text_field($_POST['search_term']) : '';
    
        $query_args = array(
            'post_type' => 'opportunity',
            'posts_per_page' => -1,
        );
    
        if (!empty($search_term)) {
            $query_args['s'] = $search_term;
        }
    
        if ($min_price || $max_price || $commission) {
            $meta_query = array('relation' => 'AND');
    
            if ($min_price !== null) {
                $meta_query[] = array(
                    'key' => 'price',
                    'value' => $min_price,
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                );
            }
    
            if ($max_price !== null) {
                $meta_query[] = array(
                    'key' => 'price',
                    'value' => $max_price,
                    'compare' => '<=',
                    'type' => 'NUMERIC',
                );
            }
    
            if (is_numeric($commission) && $commission > 0) {
                $meta_query[] = array(
                    'key' => 'commission',
                    'value' => $commission,
                    'compare' => '<=',
                    'type' => 'NUMERIC',
                );
            }
    
            $query_args['meta_query'] = $meta_query;
        }
    
        if ($industry_filter || $language_filter || $location_filter || $currency_filter || $target_audience_filter || $age_filter || $gender_filter) {
            $tax_query = array('relation' => 'AND');
    
            if ($industry_filter) {
                $tax_query[] = array(
                    'taxonomy' => 'industry',
                    'field' => 'term_id',
                    'terms' => $industry_filter,
                    'operator' => 'IN',
                );
            }
            if ($language_filter) {
                $tax_query[] = array(
                    'taxonomy' => 'language',
                    'field' => 'term_id',
                    'terms' => $language_filter,
                    'operator' => 'IN',
                );
            }
    
            if ($location_filter) {
                $tax_query[] = array(
                    'taxonomy' => 'location',
                    'field' => 'term_id',
                    'terms' => $location_filter,
                    'operator' => 'IN',
                );
            }
    
            if ($currency_filter) {
                $tax_query[] = array(
                    'taxonomy' => 'currency',
                    'field' => 'term_id',
                    'terms' => $currency_filter,
                    'operator' => 'IN',
                );
            }
    
    
            if ($target_audience_filter) {
                $tax_query[] = array(
                    'taxonomy' => 'target_audience',
                    'field' => 'term_id',
                    'terms' => $target_audience_filter,
                    'operator' => 'IN',
                );
            }
    
            if ($gender_filter) {
                $tax_query[] = array(
                    'taxonomy' => 'gender',
                    'field' => 'term_id',
                    'terms' => $gender_filter,
                    'operator' => 'IN',
                );
            }
    
            if ($age_filter) {
                $tax_query[] = array(
                    'taxonomy' => 'age',
                    'field' => 'term_id',
                    'terms' => $age_filter,
                    'operator' => 'IN',
                );
            }
    
            $query_args['tax_query'] = $tax_query;
        }
    
        if ($deliver_leads_filter === 'yes') {
            $meta_query = isset($query_args['meta_query']) ? $query_args['meta_query'] : array();
            $meta_query[] = array(
                'key' => 'deliver_leads',
                'value' => 'yes',
                'compare' => '=',
            );
            $query_args['meta_query'] = $meta_query;
        }
    
        $opportunities = new WP_Query($query_args);
    
    
        if ($opportunities->have_posts()) {
            foreach($opportunities->posts as $opportunity):

            $opportunity_id = $opportunity->ID;

            $template = 'templates/dashboard/company/opportunity/list-item.php';
            if (locate_template($template)) {
                include locate_template($template);
            }
        
        endforeach;
            wp_reset_postdata();
        } else {
            echo '<p>No opportunities found.</p>';
        }
    
        wp_die();
    }
    

    private function send_opportunity_created_email_to_company($opportunity_id)
    {
        $opportunity = get_post($opportunity_id);
        if (!$opportunity) {
            return;
        }

        $company_id = get_post_meta($opportunity_id, 'company', true);
        $company = get_post($company_id);
        if (!$company) {
            return;
        }

        $to = get_post_meta($company_id, 'company_email', true);
        $subject = 'New Opportunity Created';
        $message = "<p>Hello,</p>
            <p>A new opportunity titled \"{$opportunity->post_title}\" has been created.</p>
            <p>Check it out here: <a href=\"" . esc_url(get_permalink($opportunity_id)) . "\">View Opportunity</a></p>
            <p>If you have any questions, please contact us.</p>";

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
        }

        return $sent;
    }

    private function send_opportunity_deleted_email_to_company($opportunity_id)
    {
        $opportunity = get_post($opportunity_id);
        if (!$opportunity) {
            return;
        }

        $company_id = get_post_meta($opportunity_id, 'company', true);
        $company = get_post($company_id);
        if (!$company) {
            return;
        }

        $to = get_post_meta($company_id, 'company_email', true);
        $subject = 'Opportunity Deleted';
        $message = "<p>Hello,</p>
            <p>The opportunity titled \"{$opportunity->post_title}\" has been deleted.</p>
            <p>If you have any questions, please contact us.</p>";

        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();

        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);

        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
        }

        return $sent;
    }

}
