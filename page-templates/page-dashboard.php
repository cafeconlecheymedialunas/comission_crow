<?php
/**
 * Template Name: Dashboard
 * Description: Page template for Authenticated Users with roles.
 */

// Start output buffering to prevent header modification errors
ob_start();

// Redirige a la página de inicio de sesión si el usuario no está autenticado
if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth'));
    exit;
}

get_header("dashboard");

$current_user = wp_get_current_user();
$role = get_query_var('role', ''); // Obtiene la variable 'role' de la query

if ($role === "commercial-agent") {
    $key_role = "commercial_agent";
} else {
    $key_role = $role;
}

$subpages = get_query_var('subpages', ''); // Obtiene la variable 'subpages' de la query

// Función para verificar si el usuario tiene uno de los roles especificados
function user_has_role($roles)
{
    $user = wp_get_current_user();
    if (empty($user->roles)) {
        return false;
    }
   
    foreach ($roles as $role) {
        if (in_array($role, $user->roles)) {
            return true;
        }
    }
    return false;
}

// Función para obtener la plantilla apropiada basada en el rol y las subpáginas
function get_template_for_role($role, $subpages)
{
    $subpages = trim($subpages, '/');
    $template_path = "templates/dashboard/{$role}/{$subpages}.php";
    if (locate_template($template_path)) {
        return locate_template($template_path);
    }
    return false;
}

$associated_post = ProfileUser::get_instance()->get_user_associated_post_type();
?>

<div class="dashboard pt-30">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                <div class="user-profile card mb-4 d-flex justify-content-center align-items-center">
                    <?php
                    if ($associated_post) {
                        $post_thumbnail = get_the_post_thumbnail($associated_post->ID, "full", ['class' => 'img-fluid']);
                        $default = get_template_directory_uri() . "/assets/img/placeholder.png";
                        if ($post_thumbnail) {
                            echo $post_thumbnail;
                        } else {
                            echo '<img class="img-fluid" src="' . $default . '"/>';
                        }
                    }
?>
                    <h5><?php echo esc_html($current_user->first_name . " " . $current_user->last_name); ?></h5>
                    <p><?php echo esc_html($current_user->user_email); ?></p>
                    <a class="view-profile">View Profile</a>
                </div>
                <?php include get_template_directory() . '/templates/dashboard/menu.php'; ?>
            </div>
            <div class="col-xl-9 col-lg-8 position-relative">
                <?php
                // Verifica si el usuario tiene el rol apropiado y si la URL role coincide con el rol del usuario
                if (user_has_role([$key_role])) {
                    // Si el usuario es un administrador, permite el acceso a todas las plantillas
                    
                    $template_path = get_template_for_role($role, $subpages);
                    

                    if ($template_path) {
                        include $template_path;
                    } else {
                        echo '<div class="alert alert-danger">Page not found</div>';
                    }
                } else {
                    // Muestra mensaje de acceso denegado
                    echo '<div class="alert alert-danger">Access denied. You do not have permission to access this page.</div>';
                }
?>
            </div>
        </div>
    </div>
</div>

<?php
// Flush the output buffer and send everything to the browser
ob_end_flush();
get_footer();
?>
