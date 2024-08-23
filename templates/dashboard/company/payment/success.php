<?php
\Stripe\Stripe::setApiKey(carbon_get_theme_option("stripe_secret_key"));

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

if ($session_id):
    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);

        if (!$session) {
            throw new Exception('Session not found.');
        }

        $payment = Payment::get_instance();
        $payment_id = $payment->get_post_id_by_stripe_session($session_id);

        if (!$payment_id) {
            throw new Exception('Payment ID not found.');
        }

        $invoice = carbon_get_post_meta($payment_id, 'invoice');

        if (!$invoice) {
            $invoice = $payment->generate_invoice($session_id);
            if (!$invoice) {
                throw new Exception('Failed to generate invoice.');
            }
        }

        $payment_post = get_post($payment_id);

        if (!$payment_post) {
            throw new Exception('Payment post not found.');
        }

        carbon_set_post_meta($payment_id, 'status', "payment_completed");

        $commission_request_id = carbon_get_post_meta($payment_id, 'commission_request_id');
        if (!$commission_request_id) {
            throw new Exception('Commission request ID not found.');
        }

        $status_commision_request_history = Helper::add_item_to_status_history($commission_request_id, "");
        carbon_set_post_meta($commission_request_id, 'status_history', $status_commision_request_history);
        carbon_set_post_meta($commission_request_id, 'status', "payment_completed");

        $total_cart = carbon_get_post_meta($payment_id, 'total_cart');
        $total_agent = carbon_get_post_meta($payment_id, 'total_agent');
        $date = carbon_get_post_meta($payment_id, 'date');
        $total_paid = carbon_get_post_meta($payment_id, 'total_paid');
        $total_platform = carbon_get_post_meta($payment_id, 'total_platform');
        $total_tax_service = carbon_get_post_meta($payment_id, 'total_tax_service');
        $source = carbon_get_post_meta($payment_id, 'source');

        $items = carbon_get_post_meta($commission_request_id, 'items');
        if (empty($items)) {
            throw new Exception('No items found in commission request.');
        }

        $commission_request = get_post($commission_request_id);
        if (!$commission_request) {
            throw new Exception('Commission request not found.');
        }

        $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
        if (!$contract_id) {
            throw new Exception('Contract ID not found.');
        }

        $opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');
        if (!$opportunity_id) {
            throw new Exception('Opportunity ID not found.');
        }

        $sku = carbon_get_post_meta($contract_id, 'sku');
        if (!$sku) {
            throw new Exception('SKU not found.');
        }

        $payment->send_create_admin_payment_email($payment_id);
        $payment->send_create_agent_payment_email($payment_id);
        $payment->send_create_company_payment_email($payment_id);

    } catch (Exception $e) {
        $status_message = 'Error retrieving session: ' . $e->getMessage();
    }
    ?>
	    <div class="container mt-5">
	        <div class="row">
	            <div class="col-md-12">
	                <div class="alert alert-success" role="alert">
	                    <div class="d-flex justify-content-between align-items-center">
	                        <h4 class="alert-heading d-inline mb-0">Thank you for your purchase!</h4>
	                        <?php if (!empty($invoice)): ?>
	                            <a href="<?php echo wp_get_attachment_url($invoice[0]); ?>" download class="btn btn-sm btn-primary">
	                                <i class="fa-solid fa-file-invoice"></i>
	                            </a>
	                        <?php endif;?>
                    </div>
                    <hr>
                    <div class="mb-0 d-flex justify-content-between">
                        <span class="fw-bold">#SKU: <?php echo esc_html($sku); ?></span>
                        <span><?php echo esc_html($date); ?></span>
                    </div>
                    <hr>
                    <h5><?php echo get_the_title($opportunity_id); ?></h5>
                    <p><strong>Detail:</strong></p>

                    <ul class="list-group mb-3">
                        <?php foreach ($items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <span class="text-muted">
                                        <?php echo esc_html(Helper::convert_price_to_selected_currency($item["price_paid"]));
                                        $template = 'templates/info-price.php';
                                        if (locate_template($template)) {
                                            include locate_template($template);
                                        } ?>
                                    </span>
                                    <small class="text-muted">X <?php echo esc_html($item["quantity"]); ?></small>
                                </div>
                                <h6 class="my-0 text-muted">
                                    <?php echo esc_html(Helper::convert_price_to_selected_currency($item["subtotal"]));
                                $template = 'templates/info-price.php';
                                if (locate_template($template)) {
                                    include locate_template($template);
                                } ?>
                                </h6>
                            </li>
                        <?php endforeach;?>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <span class="text-muted">Total Sales</span>
                            </div>
                            <h6 class="my-0 text-muted">
                                <?php echo esc_html(Helper::convert_price_to_selected_currency($total_cart));
                                $template = 'templates/info-price.php';
                                if (locate_template($template)) {
                                    include locate_template($template);
                                } ?>
                            </h6>
                        </li>
                    </ul>
                    <hr>
                    <p>
                        <strong>Agent Commission:</strong><?php echo esc_html(Helper::convert_price_to_selected_currency($total_agent));
                    $template = 'templates/info-price.php';
                    if (locate_template($template)) {
                        include locate_template($template);
                    } ?>
                    </p>
                    <p>
                        <strong>Platform Fee:</strong> 
                        <?php echo esc_html(Helper::convert_price_to_selected_currency($total_platform));
                        $template = 'templates/info-price.php';
                        if (locate_template($template)) {
                            include locate_template($template);
                        } 
                        ?>
                    </p>
                    <p>
                        <strong>Tax Service Fee:</strong> 
                        <?php echo esc_html(Helper::convert_price_to_selected_currency($total_tax_service));
                            $template = 'templates/info-price.php';
                            if (locate_template($template)) {
                                include locate_template($template);
                            } 
                        ?>
                    </p>
                    <p><strong>Source:</strong> <?php echo esc_html($source); ?></p>
                    <hr>
                    <h5>Total Paid: <?php echo esc_html("$" . number_format($total_paid, 2, '.', ',') . " (USD)"); ?></h5>
                    <h5>Total in your currency: <?php echo esc_html(Helper::convert_price_to_selected_currency($total_paid));
                        $template = 'templates/info-price.php';
                        if (locate_template($template)) {
                            include locate_template($template);
                        } ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>
<?php
else: ?>
    <div class="alert alert-danger">Access denied. You do not have permission to access this page.</div>
<?php endif;?>
