<?php
\Stripe\Stripe::setApiKey(carbon_get_theme_option("stripe_secret_key"));

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

if ($session_id):
    try {
        $payment = Payment::get_instance();

        $result = $payment->handle_success($session_id);
      
        if ($result['success']) {
            $data = $result['data']; // Captura los datos devueltos
            $payment_id = $data['payment_id'];
            $sku = $data['sku'] ?? ''; // Agrega esto si también estás guardando SKU en los metadatos
            $date = $data['date'];
            $opportunity_id = $data['opportunity_id'] ?? ''; // Agrega esto si también estás guardando opportunity_id en los metadatos
            $items = $data['items'] ?? []; // Asegúrate de que `items` esté en el array
            $total_cart = $data['total_cart'];
            $total_agent = $data['total_agent'];
            $total_platform = $data['total_platform'];
            $total_tax_service = $data['total_tax_service'];
            $total_paid = $data['total_paid'];
            $source = $data['source'] ?? ''; // Agrega esto si también estás guardando source en los metadatos
            $invoice = $data['invoice'] ?? []; // Agrega esto si también estás guardando invoice en los metadatos
        } else {
            $status_message = $result['message'];
        }

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
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                    <div>
                                        <span class="text-muted">
                                            <?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($item["price_paid"])); ?>
                                        </span>
                                        <small class="text-muted">X <?php echo esc_html($item["quantity"]); ?></small>
                                    </div>
                                    <h6 class="my-0 text-muted">
                                        <?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($item["subtotal"])); ?>
                                    </h6>
                                </li>
                            <?php endforeach;?>
                        <?php endif;?>
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
                        <strong>Agent’s fee:</strong><?php echo esc_html(Helper::convert_price_to_selected_currency($total_agent));
                        $template = 'templates/info-price.php';
                        if (locate_template($template)) {
                            include locate_template($template);
                        } ?>
                    </p>
                    <p>
                        <strong>Platform’s fee:</strong> 
                        <?php echo esc_html(Helper::convert_price_to_selected_currency($total_platform));
                        $template = 'templates/info-price.php';
                        if (locate_template($template)) {
                            include locate_template($template);
                        } ?>
                    </p>
                    <p>
                        <strong>Tax Service Fee:</strong> 
                        <?php echo esc_html(Helper::convert_price_to_selected_currency($total_tax_service));
                        $template = 'templates/info-price.php';
                        if (locate_template($template)) {
                            include locate_template($template);
                        } ?>
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

<?php else: ?>
    <div class="alert alert-danger">Access denied. You do not have permission to access this page.</div>
<?php endif; ?>
