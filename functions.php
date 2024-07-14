<?php

/**
 * Include Theme Customizer.
 *
 * @since v1.0
 */
define('THEME_URL', get_template_directory_uri());
require_once get_template_directory() . '/vendor/autoload.php';
\Carbon_Fields\Carbon_Fields::boot();

// Array de rutas de archivos
$files_to_require = [
    __DIR__ . '/inc/core/Custom_Post_Type.php',
    __DIR__ . '/inc/core/Custom_Taxonomy.php',
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker.php',
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker-footer.php',
    __DIR__ . '/inc/setup/customizer.php',
    __DIR__ . '/inc/setup/setup-theme.php',
    __DIR__ . '/inc/admin.php',
    __DIR__ . '/inc/public.php',
     __DIR__ . '/inc/auth.php',
    __DIR__ . '/inc/public.php',
];
function require_files(array $files)
{
    foreach ($files as $file) {
        if (is_readable($file)) {
            require_once $file;
        }
    }
}

require_files($files_to_require);

$admin = new Admin();
$public = new PublicFront();




// En functions.php
function custom_rewrite_rules()
{
    add_rewrite_rule('^dashboard/([^/]*)/?', 'index.php?pagename=dashboard&subpage=$matches[1]', 'top');
}
add_action('init', 'custom_rewrite_rules');

function add_query_vars($vars)
{
    $vars[] = 'subpage';
    return $vars;
}
add_filter('query_vars', 'add_query_vars');

function load_custom_template($template)
{
    if (get_query_var('pagename') == 'dashboard' && get_query_var('subpage')) {
        $new_template = locate_template(['template-dashboard.php']);
        if ('' != $new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'load_custom_template');



// Función para manejar el envío del formulario
add_action('wp_ajax_create_opportunity', 'handle_opportunity_form_submission');
add_action('wp_ajax_nopriv_create_opportunity', 'handle_opportunity_form_submission');

function handle_opportunity_form_submission()
{
    // Verifica la seguridad del nonce (si es necesario)
    $errors = [];

    // Verify nonce security (if necessary)
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create_opportunity_nonce')) {
        $errors["nonce"][] = "Nonce verification failed.";
    }
 
    // Recibe y sanitiza los datos del formulario
    $post_title = sanitize_text_field($_POST['title']);
    $post_content = sanitize_textarea_field($_POST['content']);
    $sector = sanitize_text_field($_POST['sector']);
    $target_audience = sanitize_text_field($_POST['target_audience']);
    $company_type = sanitize_text_field($_POST['company_type']);
    $languages = $_POST["languages"];
    $location = sanitize_text_field($_POST['location']);
    $age = sanitize_text_field($_POST['age']);
    $gender = sanitize_text_field($_POST['gender']);
    $currency = sanitize_text_field($_POST['currency']);
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

    $videos =  $_POST['videos'];
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

    // Numeric and specific range validations
    if (isset($age) && !empty($age) && !is_numeric($age)) {
        $errors["age"][] = "Age must be a number.";
    }

    if (!is_numeric($price)) {
        $errors["price"][] = "Price must be a number.";
    }

    if (!is_numeric($commission)) {
        $errors["commission"][] = "Commission must be a number.";
    }

    // Specific validation for 'commission' field
    if ($commission < 1 || $commission > 100) {
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

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error("Error al crear la oportunidad");
        wp_die();
    }

    // Guarda los campos personalizados usando Carbon Fields
    carbon_set_post_meta($post_id, 'sector', $sector);
    carbon_set_post_meta($post_id, 'target_audience', $target_audience);
    carbon_set_post_meta($post_id, 'company_type', $company_type);
    carbon_set_post_meta($post_id, 'languages', $languages);
    carbon_set_post_meta($post_id, 'location', $location);
    carbon_set_post_meta($post_id, 'age', $age);
    carbon_set_post_meta($post_id, 'gender', $gender);
    carbon_set_post_meta($post_id, 'currency', $currency);
    carbon_set_post_meta($post_id, 'price', $price);
    carbon_set_post_meta($post_id, 'commission', $commission);
    carbon_set_post_meta($post_id, 'deliver_leads', $deliver_leads);
    carbon_set_post_meta($post_id, 'sales_cycle_estimation', $sales_cycle_estimation);
    carbon_set_post_meta($post_id, 'tips', $tips);
    carbon_set_post_meta($post_id, 'question_1', $question_1);
    carbon_set_post_meta($post_id, 'question_2', $question_2);
    carbon_set_post_meta($post_id, 'question_3', $question_3);
    carbon_set_post_meta($post_id, 'question_4', $question_4);
    carbon_set_post_meta($post_id, 'question_5', $question_5);
    carbon_set_post_meta($post_id, 'question_6', $question_6);
    carbon_set_post_meta($post_id, 'images', $images);
    carbon_set_post_meta($post_id, 'supporting_materials', $supporting_materials);
    carbon_set_post_meta($post_id, 'videos', $videos);

    
    // Envía respuesta JSON de éxito
    wp_send_json_success("Opportunity created successfully");
    wp_die();
}
?>

