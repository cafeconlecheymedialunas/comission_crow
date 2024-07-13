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
$files_to_require = array(
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
);
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


add_action('wp_ajax_create_opportunity', 'create_opportunity_callback');
add_action('wp_ajax_nopriv_create_opportunity', 'create_opportunity_callback');

function create_opportunity_callback() {
   
    $post_title = sanitize_text_field($_POST['title']);
    $content = sanitize_textarea_field($_POST['content']);
    $post_data = array(
        'post_title'    => $post_title,
        'post_status'   => 'publish',
        'post_type'     => 'opportunity',
        "post_content" => $content
    );

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {

        wp_send_json_error("Opportunity could not be created");
        // Respuesta de éxito

    }
    carbon_set_post_meta( $post_id, 'target_audience', $_POST['target_audience']  );
    carbon_set_post_meta( $post_id, 'languages', $_POST['languages']  );
    carbon_set_post_meta( $post_id, 'location', $_POST['location'] );
    carbon_set_post_meta( $post_id, 'age', $_POST['age'] );
    carbon_set_post_meta( $post_id, 'gender', $_POST['gender'] );
    carbon_set_post_meta( $post_id, 'currency', $_POST['currency'] );

    carbon_set_post_meta( $post_id, 'price', $_POST['price'] ); 
    carbon_set_post_meta( $post_id, 'age', $_POST['age'] );
    carbon_set_post_meta( $post_id, 'commission', $_POST['commission'] ); 
    carbon_set_post_meta( $post_id, 'deliver_leads', $_POST['deliver_leads'] );
    carbon_set_post_meta( $post_id, 'sales_cycle_estimation', $_POST['sales_cycle_estimation'] );
    carbon_set_post_meta( $post_id, 'images', $_POST['images'] ); 
    carbon_set_post_meta( $post_id, 'supporting_materials', $_POST['supporting_materials'] );
    carbon_set_post_meta( $post_id, 'videos', $_POST['videos'] );

    carbon_set_post_meta( $post_id, 'tips', $_POST['tips'] );
    carbon_set_post_meta( $post_id, 'question_1', $_POST['question_1'] );
    carbon_set_post_meta( $post_id, 'question_2', $_POST['question_2'] );
    carbon_set_post_meta( $post_id, 'question_3', $_POST['question_3'] );
    carbon_set_post_meta( $post_id, 'question_4', $_POST['question_4'] );
    carbon_set_post_meta( $post_id, 'question_5', $_POST['question_5'] );
    carbon_set_post_meta( $post_id, 'question_6', $_POST['question_6'] );
    

    wp_send_json_success("Se creo con exito");
    wp_die();
}

// En functions.php
function custom_rewrite_rules() {
    add_rewrite_rule('^dashboard/([^/]*)/?', 'index.php?pagename=dashboard&subpage=$matches[1]', 'top');
}
add_action('init', 'custom_rewrite_rules');

function add_query_vars($vars) {
    $vars[] = 'subpage';
    return $vars;
}
add_filter('query_vars', 'add_query_vars');

function load_custom_template($template) {
    if (get_query_var('pagename') == 'dashboard' && get_query_var('subpage')) {
        $new_template = locate_template(array('template-dashboard.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'load_custom_template');



