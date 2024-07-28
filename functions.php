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

    __DIR__ . '/inc/core/Crud.php',

  
     __DIR__ . '/inc/data/Company.php',
     __DIR__ . '/inc/data/CommercialAgent.php',
     __DIR__ . '/inc/data/ProfileUser.php',
     __DIR__ . '/inc/data/Contract.php',
     __DIR__ . '/inc/data/Commissionrequest.php',
     __DIR__ . '/inc/data/Dispute.php',
     __DIR__ . '/inc/data/Opportunity.php',
     __DIR__ . '/inc/core/CustomPostType.php',
     __DIR__ . '/inc/core/CustomTaxonomy.php',
     __DIR__ . '/inc/core/Admin.php',
     __DIR__ . '/inc/core/Public.php',
     __DIR__ . '/inc/core/Auth.php',
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









function add_item_to_status_history($contract_id, $status = "pending")
{
    $status_history = get_post_meta($contract_id, 'status_history', true);
    
    if (!is_array($status_history)) {
        $status_history = [];
    }

    // Agregar el nuevo estado al historial
    $status_history[] = [
        'history_status' => $status,
        'date_status' => current_time('mysql'),
        'changed_by' => get_current_user_id(),
    ];
    return $status_history;
}









function get_user_associated_post_type()
{
    $current_user = wp_get_current_user();

    $query = new WP_Query([
        'post_type'  => $current_user->roles[0],
        'meta_query' => [
            [
                'key'   => 'user_id',
                'value' => $current_user->ID,
                'compare' => '=',
            ]
        ]
    ]);
        


    return  $query->posts[0] ?? false;

}




