<?php
/**
 * Template Name: Custom Auth Template
 */

// Obtener la URL de referencia
$redirect_url = wp_get_referer();
if (!$redirect_url) {
    $redirect_url = home_url('/');
}

// Verificar si el usuario está logueado
if (is_user_logged_in()) {
    wp_redirect($redirect_url); // Redirigir al usuario a la página de referencia
    exit;
}

get_header("login");
?>

<div class="auth-page">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-8">
                <h2 class="auth-title">Welcome to <?php echo get_bloginfo("name"); ?></h2>
                <h3 class="auth-description"><?php echo get_bloginfo("description"); ?></h3>
            </div>
            <div class="col-md-4">
                <div class="form-wrap shadow-sm bg-white min-vh-100">
                    
                    <?php
                    the_custom_logo();
                    // Mostrar formularios según el parámetro 'action' en la URL
                    if (isset($_GET['action']) && $_GET['action'] == 'register') {
                        echo do_shortcode('[register_form]');
                    } elseif (isset($_GET['action']) && $_GET['action'] == 'password_reset') {
                        echo do_shortcode('[password_reset_form]');
                    } elseif (isset($_GET['action']) && $_GET['action'] == 'new_password') {
                        echo do_shortcode('[new_password_form]');
                    } else {
                        echo do_shortcode('[login_form]');
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none">
        <path class="elementor-shape-fill" opacity="0.33" d="M473,67.3c-203.9,88.3-263.1-34-320.3,0C66,119.1,0,59.7,0,59.7V0h1000v59.7 c0,0-62.1,26.1-94.9,29.3c-32.8,3.3-62.8-12.3-75.8-22.1C806,49.6,745.3,8.7,694.9,4.7S492.4,59,473,67.3z"></path>
        <path class="elementor-shape-fill" opacity="0.66" d="M734,67.3c-45.5,0-77.2-23.2-129.1-39.1c-28.6-8.7-150.3-10.1-254,39.1 s-91.7-34.4-149.2,0C115.7,118.3,0,39.8,0,39.8V0h1000v36.5c0,0-28.2-18.5-92.1-18.5C810.2,18.1,775.7,67.3,734,67.3z"></path>
        <path class="elementor-shape-fill" d="M766.1,28.9c-200-57.5-266,65.5-395.1,19.5C242,1.8,242,5.4,184.8,20.6C128,35.8,132.3,44.9,89.9,52.5C28.6,63.7,0,0,0,0 h1000c0,0-9.9,40.9-83.6,48.1S829.6,47,766.1,28.9z"></path>
    </svg>
</div>
<?php get_footer("login");?>