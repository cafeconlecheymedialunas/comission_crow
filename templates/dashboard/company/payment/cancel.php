<?php
// Incluye el archivo de Stripe PHP SDK
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';
if ($session_id) {
    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);
        
        // El estado del pago será 'canceled' en caso de cancelación
        if ($session->payment_status === 'unpaid' || $session->payment_status === 'canceled') {
            // El pago fue cancelado o no se ha completado
            echo 'Payment was canceled or not completed.';
        } else {
            // El pago se completó exitosamente
            echo 'Payment completed successfully!';
        }

        // Opcional: Actualizar el estado del pago en tu base de datos
        $payment_id = Payment::get_instance()->get_post_id_by_stripe_session($session_id);
        if ($payment_id) {
            carbon_set_post_meta($payment_id, 'status', 'canceled');
        }
    } catch (Exception $e) {
        echo 'Error retrieving session: ' . $e->getMessage();
    }
} else {
    echo 'No session ID provided.';
}
