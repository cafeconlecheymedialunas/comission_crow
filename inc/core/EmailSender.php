<?php
class EmailSender
{

    private $template_path;
    private $error;

    public function __construct()
    {
        $this->template_path =  get_template_directory() . '/templates/dashboard/emails/email-template.php';
        ;
        $this->error = new WP_Error();
    }

    public function send_email($to, $subject, $message)
    {
        // Obtén la plantilla con el asunto y el mensaje
        $email_body = $this->get_email_template($subject, $message);

        // Configura el encabezado para usar HTML
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        // Envía el correo electrónico
        $sent = wp_mail($to, $subject, $email_body, $headers);

        if (!$sent) {
            // Agrega un error a WP_Error
            $this->error->add('email_send_failed', 'El correo electrónico no se pudo enviar.');
            return false;
        }

        return true;
    }

    private function get_email_template($subject, $message)
    {
        if (file_exists($this->template_path)) {
            ob_start();
            // Incluye el archivo de plantilla
            include $this->template_path;
            return ob_get_clean();
        } else {
            $this->error->add('template_not_found', 'La plantilla de correo electrónico no se encuentra.');
            return 'La plantilla de correo electrónico no se encuentra.';
        }
    }

    public function get_error()
    {
        return $this->error;
    }
}
