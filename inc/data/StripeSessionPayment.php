<?php
class StripeSessionPayment
{
    private $name, $description, $price, $email;
    public function __construct($name, $description, $price, $email)
    {

        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->email = $email;
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

    public function create_session($commission_request_id)
    {
        \Stripe\Stripe::setApiKey(carbon_get_theme_option("stripe_secret_key"));
        header('Content-Type: application/json');

        try {
            // Obtener o crear el cliente de Stripe
            $customer = $this->get_or_create_stripe_customer($this->email);

            $line_items = [];

            // Mensaje dinámico basado en la moneda


            $product = \Stripe\Product::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            $price = \Stripe\Price::create([
                'product' => $product->id,
                'unit_amount' => intval($this->price * 100), // Precio en centavos
                'currency' => "USD",
            ]);
            $line_items[] = [
                'price' => $price->id,
                'quantity' => 1,
            ];

            // Crear la sesión de checkout con Stripe Tax habilitado
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card', 'link'], // Especificar métodos de pago compatibles
                'line_items' => $line_items,
                'mode' => 'payment',
                'customer' => $customer->id, // Asociar la sesión al cliente
                'success_url' => home_url('/dashboard/company/payment/success/?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => home_url('/dashboard/company/payment/cancel/?session_id={CHECKOUT_SESSION_ID}'),
            ]);
           
            $payment_id = wp_insert_post([
                'post_title' => 'Payment for Commission Request ' . $commission_request_id,
                'post_type' => 'payment',
                'post_status' => 'draft', // Establecer como borrador
            ]);

            carbon_set_post_meta($payment_id, "commission_request_id", $commission_request_id);
            carbon_set_post_meta($payment_id, "payment_stripe_id", $checkout_session->id);
            carbon_set_post_meta($payment_id, "status", 'payment_pending'); // Inicializar el estado


        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
