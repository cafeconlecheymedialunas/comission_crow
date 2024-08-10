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
