<?php
/* Template Name: Password Recovery */

get_header();
?>

<div class="password-recovery-container">
    <h2>Recuperar Contraseña</h2>
    <form id="password_recovery_form">
        <label for="user_email">Correo Electrónico:</label>
        <input type="email" id="user_email" name="user_email" required>
        <button type="submit">Enviar Enlace de Recuperación</button>
    </form>
    <div id="password_recovery_message"></div>
</div>

<?php
get_footer();
?>
