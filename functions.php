<?php

/**
 * Include Theme Customizer.
 *
 * @since v1.0
 */
define('THEME_URL', get_template_directory_uri());


// Array de rutas de archivos
$files_to_require = array(
    __DIR__ . '/inc/core/Custom_Post_Type.php',
    __DIR__ . '/inc/core/Container_Custom_Fields.php',
   
    __DIR__ . '/inc/wp-bootstrap-navwalker.php',
    __DIR__ . '/inc/wp-bootstrap-navwalker-footer.php',
    __DIR__ . '/inc/customizer.php',
    __DIR__ . '/inc/setup-theme.php',
    __DIR__ . '/inc/admin.php',


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
