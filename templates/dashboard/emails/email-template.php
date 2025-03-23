<?php
/**
 * Plantilla de correo electrónico similar a WooCommerce.
 *
 * @var string $subject El asunto del correo electrónico.
 * @var string $message El cuerpo del mensaje HTML.
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 4px;
        }
        .email-header {
            background: #007cba;
            color: #ffffff;
            padding: 20px;
            border-radius: 4px 4px 0 0;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 20px;
        }
        .email-footer {
            background: #f7f7f7;
            color: #999999;
            padding: 10px 20px;
            border-top: 1px solid #e1e1e1;
            font-size: 12px;
            text-align: center;
        }
        .button {
            display: inline-block;
            font-size: 14px;
            color: #ffffff;
            background-color: #007cba;
            border-radius: 4px;
            padding: 10px 15px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1><?php echo esc_html($subject); ?></h1>
        </div>
        <div class="email-body">
            <?php echo wp_kses_post($message); ?>
        </div>
        <div class="email-footer">
            <p>Gracias por su compra.</p>
            <p>&copy; <?php echo date('Y'); ?> Mi Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
