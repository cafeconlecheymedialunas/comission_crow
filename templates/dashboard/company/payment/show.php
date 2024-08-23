<?php
// Incluye el archivo de Stripe PHP SDK
\Stripe\Stripe::setApiKey(carbon_get_theme_option("stripe_secret_key"));

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';
if ($session_id) {
    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);
        
        // Recuperar los detalles del pago
        $payment_id = Payment::get_instance()->get_post_id_by_stripe_session($session_id);
        $payment = get_post($payment_id);

        // Verificar el estado del pago
        $payment_status = $session->payment_status;
        if ($payment_status === 'paid') {
            $status_message = 'Payment completed successfully!';
        } else {
            $status_message = 'Payment failed or is pending.';
        }

        // Obtener los campos personalizados del post type 'payment'
        $total_cart = carbon_get_post_meta($payment_id, 'total_cart');
        $total_agent = carbon_get_post_meta($payment_id, 'total_agent');
       
        $date = carbon_get_post_meta($payment_id, 'date');
        $total_paid = carbon_get_post_meta($payment_id, 'total_paid');
        $total_platform = carbon_get_post_meta($payment_id, 'total_platform');
        $total_tax_service = carbon_get_post_meta($payment_id, 'total_tax_service');
        $invoice = carbon_get_post_meta($payment_id, 'invoice');
        $source = carbon_get_post_meta($payment_id, 'source');
        
        $commission_request_id = carbon_get_post_meta($payment_id, 'commission_request_id');
        $items =  carbon_get_post_meta($commission_request_id, 'items');
        $commission_request = get_post($commission_request_id);
       
        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');

        $opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');

        
      
        $sku = carbon_get_post_meta($contract_id, 'sku');

    } catch (Exception $e) {
        $status_message = 'Error retrieving session: ' . $e->getMessage();
    }
} else {
    $status_message = 'No session ID provided.';
}


?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="mb-0 d-flex justify-content-between">
                    <span class="fw-bold">#SKU:<?php echo esc_html($sku); ?></span>
                    <span><?php echo esc_html($date); ?></span>
                </div>
                <hr>
                <div class="mb-0 w-100 d-flex justify-content-between align-items-center">
                   
                                                <h5><?php echo get_the_title($opportunity_id);?></h5>
                                                <?php if(!empty($invoice)):?>
                   
                                              
                   <a href="<?php echo wp_get_attachment_url($invoice[0]); ?>" download class="btn btn-sm btn-secondary">
                       <i class="fa-solid fa-file-invoice"></i> Donwload Invoice 
                   </a>
                  
                  
                   <?php endif;?>
                                               
                </div>

                <hr>
                <p><strong>Detail:</strong></p>

                <ul class="list-group mb-3">
                <?php foreach ($items as $item) :?>
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <span class="text-muted"><?php echo esc_html(Helper::convert_price_to_selected_currency($item["price_paid"]));$template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></span>
                            <small class="text-muted">X <?php echo esc_html($item["quantity"]);?></small>
                        </div>
                        <h6 class="my-0 text-muted"><?php echo esc_html(Helper::convert_price_to_selected_currency($item["subtotal"]));$template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></h6>
                        
                    </li>
                <?php endforeach;?>
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <span class="text-muted">Total Sales</span>
                        </div>
                        <h6 class="my-0 text-muted"><?php echo esc_html(Helper::convert_price_to_selected_currency($total_cart)); $template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></h6>
                        
                    </li>
                </ul>
               
                <p><strong>Agent Commission: </strong> <?php echo esc_html(Helper::convert_price_to_selected_currency($total_agent)); $template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></p>
                <p><strong>Platform Fee: </strong> <?php echo esc_html(Helper::convert_price_to_selected_currency($total_platform)); $template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></p>
                <p><strong>Tax Service Fee: </strong><?php echo esc_html(Helper::convert_price_to_selected_currency($total_tax_service)); $template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></p>
                <p><strong>Source: </strong> <?php echo esc_html($source); ?></p>
                <hr>
                <h5>Total Paid: <?php echo esc_html("$".number_format($total_paid, 2, '.', ',')." (USD)"); ?></h5>
                <h5>Total in your currency: <?php echo esc_html(Helper::convert_price_to_selected_currency($total_paid)); $template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></h5>
            </div>
        </div>
    </div>
</div>


