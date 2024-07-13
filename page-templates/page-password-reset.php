<?php
/* Template Name: Password Reset */

get_header();

$key = $_GET['key'] ?? '';
$login = $_GET['login'] ?? '';

if (empty($key) || empty($login)) {
    echo '<p>El enlace de restablecimiento no es válido.</p>';
    get_footer();
    exit;
}
?>

<div class="password-reset-container">
    <h2>Restablecer Contraseña</h2>
    <form id="password_reset_form">
        <input type="hidden" id="reset_key" value="<?php echo esc_attr($key); ?>">
        <input type="hidden" id="reset_login" value="<?php echo esc_attr($login); ?>">
        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" id="new_password" name="new_password" required>
        <button type="submit">Restablecer Contraseña</button>
    </form>
    <div id="password_reset_message"></div>
</div>

<?php
get_footer();
?>
