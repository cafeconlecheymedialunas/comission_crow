<?php
use Konekt\PdfInvoice\InvoicePrinter;

class Payment
{
    private function __construct()
    {
    }
    private static $instance = null;
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function create_payment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_init'])) {
            $commission_request_id = intval($_POST['commission_request_id']);
            $currency = isset($_POST["currency"])?sanitize_text_field($_POST["currency"]):"USD";
    
            $total_cart = carbon_get_post_meta($commission_request_id, 'total_cart');
            $total_agent = carbon_get_post_meta($commission_request_id, 'total_agent');
            $total_platform = carbon_get_post_meta($commission_request_id, 'total_platform');
            $total_tax_service = carbon_get_post_meta($commission_request_id, 'total_tax_service');
            $total_paid = carbon_get_post_meta($commission_request_id, 'total_to_pay');
    
            $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
            $opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');
            $commercial_agent_id = carbon_get_post_meta($contract_id, 'commercial_agent');
            $company_id = carbon_get_post_meta($contract_id, 'company');

          
    
            $currency = wp_get_post_terms($company_id,"currency");
          
            $currency_code = !empty($currency) ? carbon_get_term_meta($currency[0]->term_id, 'currency_code') : "USD";
            $currency_symbol = !empty($currency) ? carbon_get_term_meta($currency[0]->term_id, 'currency_symbol') : "$";
            $currency_exchange_rate = !empty($currency) ? carbon_get_term_meta($currency[0]->term_id, 'currency_exhange_rate') : 1;
          
            $commercial_agent_title = get_the_title($commercial_agent_id);
            $opportunity_title = get_the_title($opportunity_id);
    
            $sku = carbon_get_post_meta($contract_id, 'sku');
    
            $user = wp_get_current_user(); // Obtener el usuario actual
            $customer_email = $user->user_email; // Obtener el correo electrónico del usuario
           
            \Stripe\Stripe::setApiKey(carbon_get_theme_option("stripe_secret_key"));
            header('Content-Type: application/json');
    
            try {
                // Obtener o crear el cliente de Stripe
                $customer = $this->get_or_create_stripe_customer($customer_email);
    
                $line_items = [];
    
                // Mensaje dinámico basado en la moneda
                $conversion_message = '';
                if ($currency_code !== 'USD' || empty($currency_code)) {
                    $conversion_message = " The price was automatically converted to US dollars. The exchange rate for your currency is equal to $currency_symbol$currency_exchange_rate ($currency_code) = 1 dollar.";
                }
                
                $product = \Stripe\Product::create([
                    'name' => "Sku: $sku, Opportunity: $opportunity_title",
                    'description' => "To: $commercial_agent_title.$conversion_message",
                ]);
                $price = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => intval($total_paid * 100), // Precio en centavos
                    'currency' => "USD",
                ]);
                $line_items[] = [
                    'price' => $price->id,
                    'quantity' => 1,
                ];
    
                // Crear la sesión de checkout
                $checkout_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card',"link"], // Especificar métodos de pago compatibles
                    'line_items' => $line_items,
                    'mode' => 'payment',
                    'customer' => $customer->id, // Asociar la sesión al cliente
                    'success_url' => home_url('/dashboard/company/payment/success/?session_id={CHECKOUT_SESSION_ID}'),
                    'cancel_url' => home_url('/dashboard/company/payment/cancel/?session_id={CHECKOUT_SESSION_ID}'),
                ]);
    
                // Crear el post type payment y actualizar los campos personalizados
                $payment_id = wp_insert_post([
                    'post_title' => 'Payment for Commission Request ' . $commission_request_id,
                    'post_type' => 'payment',
                    'post_status' => 'publish',
                ]);
    
                carbon_set_post_meta($payment_id, "commission_request_id", $commission_request_id);
                carbon_set_post_meta($payment_id, "total_cart", $total_cart);
                carbon_set_post_meta($payment_id, "total_agent", $total_agent);
                carbon_set_post_meta($payment_id, "total_platform", $total_platform);
                carbon_set_post_meta($payment_id, "total_tax_service", $total_tax_service);
                carbon_set_post_meta($payment_id, "total_paid", $total_paid);
                carbon_set_post_meta($payment_id, "source", "stripe");
                carbon_set_post_meta($payment_id, "payment_stripe_id", $checkout_session->id);
                carbon_set_post_meta($payment_id, "date", current_time('mysql'));
                carbon_set_post_meta($payment_id, "status", 'payment_pending'); // Inicializar el estado
                carbon_set_post_meta($payment_id, "user", get_current_user_id());
    
                $status_commision_request_history = Helper::add_item_to_status_history($commission_request_id, "payment_pending");
                carbon_set_post_meta($commission_request_id, 'status_history', $status_commision_request_history);
                carbon_set_post_meta($commission_request_id, 'status', "payment_pending");
    
                // Redirigir a la página de checkout de Stripe
                header("HTTP/1.1 303 See Other");
                header("Location: " . $checkout_session->url);
                exit; // Asegúrate de que el script se detiene aquí para realizar la redirección
    
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        } else {
            echo "Solicitud no válida.";
        }
    }
    


    public function send_create_agent_payment_email($payment_id)
    {
        // Obtener detalles del pago
        $commission_request_id = carbon_get_post_meta($payment_id, 'commission_request_id');
        $total_agent = Helper::convert_price_to_selected_currency(carbon_get_post_meta($commission_request_id, 'total_agent'));
        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        $commercial_agent = carbon_get_post_meta($contract_id, 'commercial_agent');
        $user = carbon_get_post_meta($commercial_agent, 'user');
        $user = get_user_by('ID', $user);
        $sku = carbon_get_post_meta($contract_id, 'sku');
    
        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();
    
        // Definir los parámetros del correo electrónico
        $to = $user->user_email;
        $subject = 'Payment Received for Your Commission';
        $message = "<p>Hello, {$user->first_name},</p>
            <p>Your commission payment has been successfully processed.</p>
            <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
            <p><strong>Agent Commission:</strong> {$total_agent}</p>
            <p><strong>Contract SKU:</strong> {$sku}</p>
            <p>Thank you for your business.</p>";
    
        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
        }
    
        return $sent;
    }
    
    public function send_create_company_payment_email($payment_id)
    {
        // Obtener detalles del pago
        $commission_request_id = carbon_get_post_meta($payment_id, 'commission_request_id');
        $total_agent = Helper::convert_price_to_selected_currency(carbon_get_post_meta($commission_request_id, 'total_agent'));
        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        $commercial_agent = carbon_get_post_meta($contract_id, 'commercial_agent');
        $user = carbon_get_post_meta($commercial_agent, 'user');
        $user = get_user_by('ID', $user);
        $sku = carbon_get_post_meta($contract_id, 'sku');
    
        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();
    
        // Definir los parámetros del correo electrónico
        $to = $user->user_email;
        $subject = 'Payment Confirmation for Your Company';
        $message = "<p>Hello, {$user->first_name},</p>
            <p>Your company payment has been successfully processed.</p>
            <p><strong>Commission Request ID:</strong> {$commission_request_id}</p>
            <p><strong>Agent Commission:</strong> {$total_agent}</p>
            <p><strong>Contract SKU:</strong> {$sku}</p>
            <p>Thank you for your business.</p>";
    
        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
        }
    
        return $sent;
    }
   
    
    public function send_create_admin_payment_email($payment_id)
    {
        // Obtener detalles del pago
        $commission_request_id = carbon_get_post_meta($payment_id, 'commission_request_id');
      
        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
       
      
        $sku = carbon_get_post_meta($contract_id, 'sku');
    
        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();
    
        // Definir los parámetros del correo electrónico
        $to = get_option('admin_email');
        $subject = "New Payment Created";
        $message = "<p>Hello Admin,</p>
            <p>A new payment has been created with the following details:</p>
            <p><strong>Commission Request ID:</strong> <?php echo esc_html($commission_request_id); ?></p>
            <p><strong>Contract SKU:</strong> <?php echo esc_html($sku); ?></p>
            <p>Thank you for your attention.</p>";
    
        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
        }
    
        return $sent;
    }
    
    
    public function generate_invoice($session_id)
    {
        // Buscar el session_id en el payment post type
        $payment_id = $this->get_post_id_by_stripe_session($session_id);
        if (!$payment_id) {
            echo 'Payment not found.';
            return;
        }
    
        // Obtener el commission_request_id y contract_id
        $commission_request_id = carbon_get_post_meta($payment_id, 'commission_request_id');
        if (!$commission_request_id) {
            echo 'Commission request not found.';
            return;
        }
    
        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        if (!$contract_id) {
            echo 'Contract not found.';
            return;
        }
    
        // Obtener detalles del commission_request y contract
        $items = carbon_get_post_meta($commission_request_id, 'items');
        $total_cart = carbon_get_post_meta($payment_id, 'total_cart');
        $total_agent = carbon_get_post_meta($payment_id, 'total_agent');
        $total_platform = carbon_get_post_meta($payment_id, 'total_platform');
        $total_tax_service = carbon_get_post_meta($payment_id, 'total_tax_service');
        $total_paid = carbon_get_post_meta($payment_id, 'total_paid');
    
        // Obtener información del contrato
        $contract = get_post($contract_id);
        $contract_commission = carbon_get_post_meta($contract_id, 'commission');
        $contract_minimal_price = carbon_get_post_meta($contract_id, 'minimal_price');
        $contract_date = carbon_get_post_meta($contract_id, 'date');
    
        // Obtener el opportunity asociado al contrato
        $opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');
        if (!$opportunity_id) {
            echo 'Opportunity not found.';
            return;
        }
    
        $opportunity_name = get_the_title($opportunity_id);
    
        // Obtener el usuario actual y su información
        $current_user = wp_get_current_user();
        $current_user_name = $current_user->display_name;

        $company_id = carbon_get_post_meta($contract_id, 'company');
        $sku = carbon_get_post_meta($contract_id, 'sku');
    
        // Obtener el nombre de la empresa desde las opciones de WordPress
        $company_name = carbon_get_post_meta($company_id, 'company_name');
        $company_street = carbon_get_post_meta($company_id, 'company_street');
        $company_number = carbon_get_post_meta($company_id, 'company_number');
        $company_city = carbon_get_post_meta($company_id, 'company_city');
        $company_state = carbon_get_post_meta($company_id, 'company_state');
        $company_postalcode = carbon_get_post_meta($company_id, 'company_postalcode');


        $platform_billing_address_street = carbon_get_theme_option("billing_address_street");
        $platform_billing_address_number = carbon_get_theme_option("billing_address_number");
        $platform_billing_address_city = carbon_get_theme_option("billing_address_city");
        $platform_billing_address_state = carbon_get_theme_option("billing_address_state");
        $platform_billing_address_location = carbon_get_theme_option("billing_address_location");
        $platform_billing_address_postalcode = carbon_get_theme_option("billing_address_postalcode");
        $platform_billing_company_holder = carbon_get_theme_option("billing_company_holder");
        $platform_billing_company_name = carbon_get_theme_option("billing_company_name");
        // Crear la factura
        $invoice = new InvoicePrinter();
        $invoice->setColor("#6787fe");
        $invoice->setType("Commission Request Invoice");
        $invoice->setReference("SKU-" . $sku); // Generar un ID único para la factura
        $invoice->setDate(date('M dS, Y', time()));
        $invoice->setTime(date('h:i:s A', time()));
        $invoice->setFrom([
            $platform_billing_company_holder,
            $platform_billing_company_name,
            "$platform_billing_address_number $platform_billing_address_street",
            "$platform_billing_address_city $platform_billing_address_state, PC $platform_billing_address_postalcode"
        ]);
        $invoice->setTo([
            $current_user_name,
            $company_name,
            "$company_number $company_street",
            "$company_city $company_state, PC $company_postalcode"
        ]);
    
        // Agregar items a la factura
        foreach ($items as $index => $item) {
            $invoice->addItem(
                $opportunity_name, // Usar el nombre del opportunity
                "", // Usar la descripción del opportunity
                $item['quantity'],
                0,
                $item['price_paid'],
                0,
                $item['subtotal']
            );
        }
    
        $invoice->addTotal("Total Sales", $total_cart);
        $invoice->addTotal("Stripe Tax", $total_tax_service);
        $invoice->addTotal("Commission Agent", $total_agent);
        $invoice->addTotal("Commission Platform", $total_platform);
        $invoice->addTotal("Total due", $total_paid, true);
    
        $invoice->addBadge("Payment Completed");
        $invoice->addTitle("Important Notice");
        $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");
        $invoice->setFooternote($platform_billing_company_name);
    
        // Guardar el PDF en un archivo temporal
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['path'] . '/payment-'.$payment_id.'.pdf';
        $invoice->render($file_path, 'F');
    
        // Cargar el archivo PDF a la biblioteca de medios de WordPress
        $attachment_id = wp_insert_attachment([
            'guid' => $upload_dir['url'] . '/invoice-' . $payment_id. '.pdf',
            'post_mime_type' => 'application/pdf',
            'post_title' => 'Invoice for Payment ' . $payment_id,
            'post_content' => '',
            'post_status' => 'inherit'
        ], $file_path);
    
        // Generar los metadatos del archivo
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        wp_generate_attachment_metadata($attachment_id, $file_path);
    
        // Actualizar el post meta con el ID del archivo adjunto
        carbon_set_post_meta($payment_id, 'invoice', $attachment_id);
    
        return $attachment_id;
    }
    
    
    private function get_or_create_stripe_customer($email)
    {
        $existing_customers = \Stripe\Customer::all(['email' => $email, 'limit' => 1]);
        if (count($existing_customers->data) > 0) {
            return $existing_customers->data[0];
        } else {
            return \Stripe\Customer::create(['email' => $email]);
        }
    }

    public function get_post_id_by_stripe_session($session_id)
    {
        $args = [
            'post_type' => 'payment',
            'meta_query' => [
                [
                    'key' => 'payment_stripe_id',
                    'value' => $session_id,
                    'compare' => '=',
                ],
            ],
            'posts_per_page' => 1,
            'fields' => 'ids',
        ];

        $query = new WP_Query($args);
        $posts = $query->get_posts();
        return (!empty($posts)) ? $posts[0] : null;
    }
}
