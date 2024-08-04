<?php
// Incluye el archivo de Stripe PHP SDK
\Stripe\Stripe::setApiKey(carbon_get_theme_option("stripe_secret_key"));

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

if ($session_id) :
    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);
       
      
        
        // Recuperar los detalles del pago
        $payment = Payment::get_instance();
        $payment_id = Payment::get_instance()->get_post_id_by_stripe_session($session_id);
        $invoice = carbon_get_post_meta($payment_id, 'invoice');

        if(!$invoice) {
            $invoice = Payment::get_instance()->generate_invoice($session_id);
        }
        $payment_post = get_post($payment_id);
        carbon_set_post_meta($payment_id, 'status', "succeeded");
        
        
        // Obtener los campos personalizados del post type 'payment'
        $total_cart = carbon_get_post_meta($payment_id, 'total_cart');
        $total_agent = carbon_get_post_meta($payment_id, 'total_agent');
        $date = carbon_get_post_meta($payment_id, 'date');
        $total_paid = carbon_get_post_meta($payment_id, 'total_paid');
        $total_platform = carbon_get_post_meta($payment_id, 'total_platform');
        $total_tax_service = carbon_get_post_meta($payment_id, 'total_tax_service');
       
       
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
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Thank you for your purchase!</h4>
                    <hr>
                    <div class="mb-0 d-flex justify-content-between">
                        <span class="fw-bold">#SKU: <?php echo esc_html($sku); ?></span>
                        <span><?php echo esc_html($date); ?></span>
                    </div>
                    <hr>
                    <h5><?php echo get_the_title($opportunity_id); ?></h5>
                    <p><strong>Detail:</strong></p>

                    <ul class="list-group mb-3">
                        <?php foreach ($items as $item) : ?>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <span class="text-muted"><?php echo esc_html(Helper::format_price($item["price_paid"])); ?></span>
                                    <small class="text-muted">X <?php echo esc_html($item["quantity"]); ?></small>
                                </div>
                                <h6 class="my-0 text-muted"><?php echo esc_html(Helper::format_price($item["subtotal"])); ?></h6>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <span class="text-muted">Total Sales</span>
                            </div>
                            <h6 class="my-0 text-muted"><?php echo esc_html(Helper::format_price($total_cart)); ?></h6>
                        </li>
                    </ul>
                    <hr>
                    <p><strong>Agent Commission:</strong><?php echo esc_html(Helper::format_price($total_agent)); ?></p>
                    <p><strong>Platform Fee:</strong> <?php echo esc_html(Helper::format_price($total_platform)); ?></p>
                    <p><strong>Tax Service Fee:</strong> <?php echo esc_html(Helper::format_price($total_tax_service)); ?></p>
                    <p><strong>Source:</strong> <?php echo esc_html($source); ?></p>
                    <hr>
                    <h5>Total Paid: <?php echo esc_html(Helper::format_price($total_paid)); ?></h5>
                    <hr>
                    <?php if(!empty($invoice)):?>
                    <a href="<?php echo wp_get_attachment_url($invoice[0]); ?>" download class="btn btn-primary">
                        <i class="fas fa-file-invoice"></i> Download Invoice
                    </a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
<?php
else : ?>
    <div class="alert alert-danger">Access denied. You do not have permission to access this page.</div>
<?php endif; ?>
