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
   

 


    public function generate_invoice($session_id) {
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
    
        // Obtener el nombre de la empresa desde las opciones de WordPress
        $company_name = get_option('company_name', 'Default Company Name'); // Asegúrate de tener esta opción configurada o cambia el método de obtención
    
        // Crear la factura
        $invoice = new InvoicePrinter();
        $invoice->setColor("#007fff");
        $invoice->setType("Sale Invoice");
        $invoice->setReference("INV-" . strtoupper(uniqid())); // Generar un ID único para la factura
        $invoice->setDate(date('M dS, Y', time()));
        $invoice->setTime(date('h:i:s A', time()));
        $invoice->setDue(date('M dS, Y', strtotime('+3 months')));
        $invoice->setFrom([
            "Martin Neves",
            "Nexfy",
            "128 AA Juanita Ave",
            "Glendora, CA 91740"
        ]);
        $invoice->setTo([
            $current_user_name,
            $company_name,
            "128 AA Juanita Ave",
            "Glendora, CA 91740"
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
        $invoice->addTotal("Stripe Tax", $total_tax_service );
        $invoice->addTotal("Commission Agent", $total_agent );
        $invoice->addTotal("Commission Platform", $total_platform );
        $invoice->addTotal("Total due", $total_paid, true);
    
        $invoice->addBadge("Payment Paid");
        $invoice->addTitle("Important Notice");
        $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");
        $invoice->setFooternote("My Company Name Here");
    
        // Guardar el PDF en un archivo temporal
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['path'] . '/invoice_' . $payment_id . '.pdf';
        $invoice->render($file_path, 'F');
    
        // Subir el archivo a la Biblioteca de Medios
        $file_name = basename($file_path);
        $file_type = wp_check_filetype($file_path);
        $attachment = [
            'guid'           => $upload_dir['url'] . '/' . $file_name,
            'post_mime_type' => $file_type['type'],
            'post_title'     => sanitize_file_name($file_name),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];
    
        $attachment_id = wp_insert_attachment($attachment, $file_path);
    
        // Generar metadatos del archivo adjunto
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
        wp_update_attachment_metadata($attachment_id, $attach_data);
    
        // Actualizar el campo invoice en el payment
        carbon_set_post_meta($payment_id, 'invoice', $attachment_id);
    
        echo 'Invoice generated and saved.';
    }
    



    public function create_payment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_init'])) {
            $commission_request_id = $_POST['commission_request_id'];
            $items = carbon_get_post_meta($commission_request_id, 'items'); // Obtener los items del carrito
    
            $total_cart = carbon_get_post_meta($commission_request_id, 'total_cart');
            $total_agent = calculate_agent_price($commission_request_id);
            $total_platform = calculate_platform_price($commission_request_id);
            $total_tax_service = calculate_tax_stripe_price($commission_request_id);
            $total_paid = calculate_total($commission_request_id);
    
            $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id'); 
            $opportunity_id = carbon_get_post_meta($contract_id, 'opportunity'); 
            $commercial_agent_id = carbon_get_post_meta($contract_id, 'commercial_agent'); 
            $commercial_agent_title = get_the_title($commercial_agent_id);
            $opportunity_title = get_the_title($opportunity_id);
    
            $sku = carbon_get_post_meta($contract_id, 'sku');
    
            $user = wp_get_current_user(); // Obtener el usuario actual
            $customer_email = $user->user_email; // Obtener el correo electrónico del usuario
    
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            header('Content-Type: application/json');
    
            try {
                // Buscar o crear un cliente de Stripe
                $customer = $this->get_or_create_stripe_customer($customer_email);
    
                $line_items = [];
                $product = \Stripe\Product::create([
                    'name' => "Sku: $sku, Opportunity: $opportunity_title",
                    'description' => "To: $commercial_agent_title",
                ]);
                $price = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => intval($total_paid * 100), // Precio en centavos
                    'currency' => 'usd',
                ]);
                $line_items[] = [
                    'price' => $price->id,
                    'quantity' => 1,
                ];
    
                // Crear la sesión de checkout
                $checkout_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'], // Especificar métodos de pago compatibles
                    'line_items' => $line_items,
                    'mode' => 'payment',
                    'customer' => $customer->id, // Asociar la sesión al cliente
                    'success_url' => home_url() . '/dashboard/company/payment/success/?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => home_url() . '/dashboard/company/payment/cancel/?session_id={CHECKOUT_SESSION_ID}',
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
                carbon_set_post_meta($payment_id, "status", 'pending'); // Inicializar el estado
    
                // Redirigir a la página de checkout de Stripe
                header("HTTP/1.1 303 See Other");
                header("Location: " . $checkout_session->url);
                exit; // Asegúrate de que el script se detiene aquí para realizar la redirección
    
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
    
    public function get_post_id_by_stripe_session($session_id) {
        $args = array(
            'post_type' => 'payment',
            'meta_query' => array(
                array(
                    'key' => 'payment_stripe_id',
                    'value' => $session_id,
                    'compare' => '='
                )
            )
        );
        $payments = get_posts($args);
        return !empty($payments) ? $payments[0]->ID : false;
    }
    
    private function get_or_create_stripe_customer($email) {
        $customers = \Stripe\Customer::all(['email' => $email]);
        if (count($customers->data) > 0) {
            return $customers->data[0];
        }
    
        // Si no existe, creamos uno nuevo
        return \Stripe\Customer::create([
            'email' => $email,
        ]);
    }
    
}
