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
            'target_audience' => sanitize_text_field($_POST['target_audience']),
            'age' => sanitize_text_field($_POST['age']),
            'gender' => sanitize_text_field($_POST['gender']),
            'price' => sanitize_text_field($_POST['price']),
            'commission' => sanitize_text_field($_POST['commission']),
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
            'country' => isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [],
            'currency' => isset($_POST["currency"]) ? array_map('sanitize_text_field', $_POST["currency"]) : [],
            'industry' => isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [],
            'type_of_company' => isset($_POST["type_of_company"]) ? array_map('sanitize_text_field', $_POST["type_of_company"]) : [],
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
            $validation_result = validate_files($_FILES['images'], $allowed_image_types, $max_image_size);
            if (isset($validation_result['error'])) {
                $errors['images'][] = $validation_result['error'];
            }
        }

        $allowed_supporting_types = ['application/pdf', 'text/plain'];
        $max_supporting_size = 10 * 1024 * 1024;

        if (isset($_FILES['supporting_materials']) && !empty($_FILES['supporting_materials']['name'][0])) {
            $validation_result = validate_files($_FILES['supporting_materials'], $allowed_supporting_types, $max_supporting_size);
            if (isset($validation_result['error'])) {
                $errors['supporting_materials'][] = $validation_result['error'];
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

        $uploaded_image_ids = handle_multiple_file_upload($_FILES['images']);
        $uploaded_supporting_material_ids = handle_multiple_file_upload($_FILES['supporting_materials']);
        $data["images"] = $uploaded_image_ids;
        $data["supporting_materials"] = $uploaded_supporting_material_ids;

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
        if ($this->has_errors()) {
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
            'post_type'   => 'contract', // Update this to the post type used for contracts
            'meta_query'  => [
                [
                    'key'     => 'opportunity_id', // Update this to the meta field that links contracts to opportunities
                    'value'   => $opportunity_id,
                    'compare' => '='
                ]
            ]
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
        $subject = __('New Opportunity Created');
        $message = sprintf(
            __('A new opportunity "%s" has been created. Check it out here: %s'),
            $opportunity->post_title,
            get_permalink($opportunity_id)
        );

        wp_mail($to, $subject, $message);
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
        $subject = __('Opportunity Deleted');
        $message = sprintf(
            __('The opportunity "%s" has been deleted.'),
            $opportunity->post_title
        );

        wp_mail($to, $subject, $message);
    }
}
