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
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker.php',
    __DIR__ . '/inc/setup/wp-bootstrap-navwalker-footer.php',
     __DIR__ . '/inc/setup/customizer.php',
    __DIR__ . '/inc/setup/setup-theme.php',

    __DIR__ . '/inc/core/EmailSender.php',
    __DIR__ . '/inc/core/CustomPostType.php',
    __DIR__ . '/inc/core/CustomTaxonomy.php',
    __DIR__ . '/inc/core/ContainerCustomFields.php',
    __DIR__ . '/inc/core/Crud.php',
    __DIR__ . '/inc/core/Helper.php',
    __DIR__ . '/inc/data/StatusManager.php',
    __DIR__ . '/inc/data/Auth.php',
    __DIR__ . '/inc/data/ProfileUser.php',
    __DIR__ . '/inc/data/Company.php',
    __DIR__ . '/inc/data/CommercialAgent.php',

    __DIR__ . '/inc/data/Contract.php',
    __DIR__ . '/inc/data/CommissionRequest.php',
    __DIR__ . '/inc/data/EmailMetaBox.php',

    __DIR__ . '/inc/data/Deposit.php',
    __DIR__ . '/inc/data/Dispute.php',
    __DIR__ . '/inc/data/Opportunity.php',
    __DIR__ . '/inc/data/Payment.php',
    __DIR__ . '/inc/data/Rating.php',
    __DIR__ . '/inc/core/Admin.php',
    __DIR__ . '/inc/core/Public.php',
    __DIR__ . '/inc/core/Dashboard.php',

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

$dasboard = new Dashboard();

$containerCustomFields = new ContainerCustomFields($admin);
add_action('carbon_fields_register_fields', [$containerCustomFields, 'register_fields']);

// Función para validar los archivos
function validate_files($files, $allowed_types = ['application/pdf', 'text/plain'], $max_size = 10485760) // 10MB
{
    foreach ($files['name'] as $key => $value) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            $file_type = $files['type'][$key];
            $file_size = $files['size'][$key];

            if (!in_array($file_type, $allowed_types)) {
                return ['error' => 'Invalid file type. Only PDF and text files are allowed.'];
            }

            if ($file_size > $max_size) {
                return ['error' => 'File size exceeds the maximum limit of 10MB.'];
            }
        }
    }
    return ['success' => true];
}

// Función para manejar la carga de múltiples archivos y devolver los IDs de los attachments
function handle_multiple_file_upload($files)
{
    $uploads = [];
    foreach ($files['name'] as $key => $value) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            $file = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key],
            ];

            $upload = wp_handle_upload($file, ['test_form' => false]);
            if ($upload && !isset($upload['error'])) {
                $attachment_id = wp_insert_attachment([
                    'guid' => $upload['url'],
                    'post_mime_type' => $upload['type'],
                    'post_title' => sanitize_file_name($upload['file']),
                    'post_content' => '',
                    'post_status' => 'inherit',
                ], $upload['file']);

                require_once ABSPATH . 'wp-admin/includes/image.php';
                $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attach_data);

                $uploads[] = $attachment_id;
            } else {
                return ['error' => 'File upload error: ' . $upload['error']];
            }
        }
    }
    return $uploads;
}



// Register the shortcode
// Register the shortcode
// functions.php
function social_media_shortcode() {
    // Retrieve social media URLs from theme options
    $website_url = carbon_get_theme_option('platform_website_url');
    $facebook_url = carbon_get_theme_option('platform_facebook_url');
    $instagram_url = carbon_get_theme_option('platform_instagram_url');
    $twitter_url = carbon_get_theme_option('platform_twitter_url');
    $linkedin_url = carbon_get_theme_option('platform_linkedin_url');
    $tiktok_url = carbon_get_theme_option('platform_tiktok_url');
    $youtube_url = carbon_get_theme_option('platform_youtube_url');

    // Define an array of social media platforms and their icons
    $social_media = array(
        'website_url' => array('icon' => 'fa-solid fa-globe', 'url' => $website_url),
        'facebook_url' => array('icon' => 'fab fa-facebook-f', 'url' => $facebook_url),
        'instagram_url' => array('icon' => 'fab fa-instagram', 'url' => $instagram_url),
        'twitter_url' => array('icon' => 'fab fa-twitter', 'url' => $twitter_url),
        'linkedin_url' => array('icon' => 'fab fa-linkedin-in', 'url' => $linkedin_url),
        'tiktok_url' => array('icon' => 'fab fa-tiktok', 'url' => $tiktok_url),
        'youtube_url' => array('icon' => 'fab fa-youtube', 'url' => $youtube_url)
    );

    $output = '<div class="social-media-links d-flex flex-wrap">';

    foreach ($social_media as $key => $data) {
        if ($data['url']) {
        
            $output .= '<a href="' . esc_url($data['url']) . '" target="_blank" class="social-media-link d-flex align-items-center wrap justify-content-center rounded-circle ms-2 mb-2" style="width:40px;height:40px;background-color:#6787FE; color:white;">';
            $output .= '<i class="' . esc_attr($data['icon']) . '"></i>';
            $output .= '</a>';
       
        }
    }

    $output .= '</div>';

    return $output;
}

add_shortcode('social_media', 'social_media_shortcode');




