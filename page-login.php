<?php
/**
 * Template Name: Login
 * Description: Page template with no sidebar.
 *
 */
get_header();

?>

<form id="login_form">
    <input type="text" id="log" name="log" placeholder="Username or Email" required>
    <input type="password" id="pwd" name="pwd" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
<div id="login_message"></div>

<?php get_footer(); ?>
