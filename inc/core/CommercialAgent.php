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
   
    
    public function save_agent_profile() {
        $errors = [];
        $current_user = wp_get_current_user();
    
        check_ajax_referer('update-profile-nonce', 'security');
        $commercial_agent_id = sanitize_text_field($_POST["commercial_agent_id"]);
    
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $user_email = sanitize_text_field($_POST['user_email']);
        $profile_image = sanitize_text_field($_POST["profile_image"]);
        $description = wp_kses_post($_POST['description']);
        $languages = isset($_POST["language"]) ? array_map('sanitize_text_field', $_POST["language"]) : [];
        $country = isset($_POST["country"]) ? array_map('sanitize_text_field', $_POST["country"]) : [];
        $skills = isset($_POST["skill"]) ? array_map('sanitize_text_field', $_POST["skill"]) : [];
        $industry = isset($_POST["industry"]) ? array_map('sanitize_text_field', $_POST["industry"]) : [];
        $seller_type = isset($_POST["seller_type"]) ? array_map('sanitize_text_field', $_POST["seller_type"]) : [];
        $selling_method = isset($_POST["selling_method"]) ? array_map('sanitize_text_field', $_POST["selling_method"]) : [];
        $years_of_experience = sanitize_text_field($_POST["years_of_experience"]);
    
        if (empty($commercial_agent_id)) {
            wp_send_json_error("Error when updating the profile");
        }
    
        if (empty($first_name)) {
            $errors["first_name"][] = "First name field is required.";
        }
    
        if (empty($last_name)) {
            $errors["last_name"][] = "Last name field is required.";
        }
    
        if (empty($user_email)) {
            $errors["user_email"][] = "Email field is required.";
        }
    
        if (!empty($errors)) {
            wp_send_json_error($errors);
            wp_die();
        }
    
        $updated_user = wp_update_user([
            "ID" => $current_user->ID,
            'user_email' => $user_email,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);
    
        if (is_wp_error($updated_user)) {
            wp_send_json_error($updated_user->get_error_messages());
        }
    
        $agent_post_id = wp_update_post([
            "ID" => $commercial_agent_id,
            'post_title' => $first_name . ' ' . $last_name,
            'post_status' => 'publish',
            'post_author' => $current_user->ID,
            "post_content" => $description
        ]);
    
        if (is_wp_error($agent_post_id)) {
            wp_send_json_error("Error when updating the profile");
            wp_die();
        }
    
        carbon_set_post_meta($commercial_agent_id, 'years_of_experience', $years_of_experience);
    
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
    
        if (!empty($profile_image)) {
            set_post_thumbnail($commercial_agent_id, $profile_image);
        }
    
        wp_send_json_success("Profile updated successfully");
        wp_die();
    }
    
    public function get_deals()
    {
    
        $args = [
            'post_type' => 'deal',
            'meta_query' => [
                [
                    'key' => 'commercial_agent',
                    'value' => $this->commercial_agent->ID,
                    'compare' => '='
                ]
            ],
            'posts_per_page' => -1,
        ];
        $query = new WP_Query($args);
        return $query->posts; // Devolver los posts
    }
}
