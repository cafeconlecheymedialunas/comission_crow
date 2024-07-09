<?php
/**
 * Template Name: Dashboard
 * Description: Page template with no sidebar.
 *
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

get_header("dashboard");

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        echo '<h1>Welcome, ' . $current_user->display_name . '</h1>';
    } else {
        echo'<p>You need to <a href="/login">login</a> to access the dashboard.</p>';
    }
?>

<?php get_footer(); ?>
