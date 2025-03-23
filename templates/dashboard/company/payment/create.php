<?php
$user = ProfileUser::get_instance();
$commission_request_id = isset($_GET['commission_request_id']) ? sanitize_text_field($_GET['commission_request_id']) : null;
$total_agent = floatval(carbon_get_post_meta($commission_request_id, 'total_agent'));
$total_to_pay = floatval(carbon_get_post_meta($commission_request_id, 'total_to_pay'));
$total_platform = floatval(carbon_get_post_meta($commission_request_id, 'total_platform'));
$total_tax_service = floatval(carbon_get_post_meta($commission_request_id, 'total_tax_service'));
$total_cart = floatval(carbon_get_post_meta($commission_request_id, 'total_cart'));
$contract_id = intval(carbon_get_post_meta($commission_request_id, 'contract_id'));
$commission = floatval(carbon_get_post_meta($contract_id, 'commission'));
$sku = carbon_get_post_meta($contract_id, 'sku');
$minimal_price = floatval(carbon_get_post_meta($contract_id, 'minimal_price'));
$commercial_agent_id = intval(carbon_get_post_meta($contract_id, 'commercial_agent'));
//$user_id = intval(carbon_get_post_meta($commercial_agent_id, 'user_id'));
$opportunity_id = intval(carbon_get_post_meta($contract_id, 'opportunity'));
$full_name_agent = carbon_get_post_meta($commercial_agent_id, 'bank_account_name_holder');
$email_agent = carbon_get_post_meta($commercial_agent_id, 'stripe_email');
$company_id = intval(carbon_get_post_meta($contract_id, 'company'));
$company_name = carbon_get_post_meta($company_id, 'company_name');
$user_id = intval(carbon_get_post_meta($company_id, 'user_id'));
?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Pay"); ?></h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="container mt-5" id="commission-request" data-id="<?php echo esc_attr($commission_request_id); ?>">
                <div class="row">
                    <div class="col-md-8 order-md-1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
																<?php
// Ensure $commission_request_id is valid
/*if (isset($user_id) && is_numeric($user_id)) {
    // Retrieve all meta fields for the given post ID
    $all_meta = get_post_meta($user_id);

    // Check if there are any fields to display
    if (!empty($all_meta)) {
        echo '<ul>'; // Start the HTML list
        foreach ($all_meta as $key => $values) {
            // Each $values is an array, loop through it to display all stored values
            foreach ($values as $value) {
                echo '<li>';
                echo '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value);
                echo '</li>';
            }
        }
        echo '</ul>'; // End the HTML list
    } else {
        echo '<p>No meta fields found for this commission request.</p>';
    }
} else {
    echo '<p>Invalid commission request ID.</p>';
}*/
								?>
                                <h4 class="mb-3">Contract</h4>


                                <p class="fw-bold">Sku #<?php echo esc_html($sku); ?></p>
                                <?php if ($company > 0): ?>
                                    <p>Name: <?php echo $company_name ?></p>
                                    <p>Email: <?php echo $email_agent ?></p>
                                <?php endif; ?>
                                <?php if ($location): ?>
                                    <p>Agent location: <?php echo esc_html($location); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-3">Opportunity</h4>
                                <?php if ($opportunity_id > 0): ?>
                                    <?php if (has_post_thumbnail($opportunity_id)) {
                                        echo get_the_post_thumbnail($opportunity_id);
                                    } ?>
                                    <h6><?php echo esc_html(get_the_title($opportunity_id)); ?></h6>
                                <?php endif; ?>
                                <?php if ($minimal_price > 0): ?>
                                    <p>Minimum price: <?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($minimal_price)); ?></p>
                                <?php endif; ?>
                                <?php if ($commission > 0): ?>
                                    <p>Commission: <?php echo esc_html($commission); ?>%</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="mb-4">

                        <!-- Sección de información del cliente -->
                        <h4 class="mb-3">Customer Information</h4>
						<?php
// Ensure $commission_request_id is valid
/*if (isset($contract_id) && is_numeric($contract_id)) {
    // Retrieve all meta fields for the given post ID
    $all_meta = get_post_meta($contract_id);

    // Check if there are any fields to display
    if (!empty($all_meta)) {
        echo '<ul>'; // Start the HTML list
        foreach ($all_meta as $key => $values) {
            // Each $values is an array, loop through it to display all stored values
            foreach ($values as $value) {
                echo '<li>';
                echo '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value);
                echo '</li>';
            }
        }
        echo '</ul>'; // End the HTML list
    } else {
        echo '<p>No meta fields found for this commission request.</p>';
    }
} else {
    echo '<p>Invalid commission request ID.</p>';
}*/
?>
                        <p><strong>Name:</strong> <?php echo esc_html(carbon_get_post_meta($commission_request_id, 'customer_name')); ?></p>
                        <p><strong>Company:</strong> <?php echo esc_html(carbon_get_post_meta($commission_request_id, 'customer_company')); ?></p>
                        <p><strong>Email:</strong> <?php echo esc_html(carbon_get_post_meta($commission_request_id, 'customer_email')); ?></p>
                        <p><strong>Address:</strong> <?php echo esc_html(carbon_get_post_meta($commission_request_id, 'customer_address')); ?></p>
                        <p><strong>Phone:</strong> <?php echo esc_html(carbon_get_post_meta($commission_request_id, 'customer_phone')); ?></p>
						<?php
// Ensure $commission_request_id is valid
/*if (isset($opportunity_id) && is_numeric($opportunity_id)) {
    // Retrieve all meta fields for the given post ID
    $all_meta = get_post_meta($opportunity_id);

    // Check if there are any fields to display
    if (!empty($all_meta)) {
        echo '<ul>'; // Start the HTML list
        foreach ($all_meta as $key => $values) {
            // Each $values is an array, loop through it to display all stored values
            foreach ($values as $value) {
                echo '<li>';
                echo '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value);
                echo '</li>';
            }
        }
        echo '</ul>'; // End the HTML list
    } else {
        echo '<p>No meta fields found for this commission request.</p>';
    }
} else {
    echo '<p>Invalid commission request ID.</p>';
}*/
?>

                        <hr class="mb-4">
                        <h4 class="mb-3">Payment</h4>
                        <div class="d-block my-3">
                            <?php if($total_to_pay > 0.50):?>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                <input type="hidden" name="action" value="create_payment">
                                <input type="hidden" name="currency" value="usd">
                                <input type="hidden" name="payment_init" value="on">
                                <input type="hidden" name="commission_request_id" value="<?php echo esc_attr($commission_request_id); ?>">
                                <input type="hidden" name="success_url" value="<?php echo esc_url(home_url('/dashboard/company/payment/success')); ?>">
                                <input type="hidden" name="cancel_url" value="<?php echo esc_url(home_url('/dashboard/company/payment/cancel')); ?>">
                                <button class="btn btn-primary" type="submit">Pay with Stripe</button>
                            </form>
                            <?php else:?>
                                <p>Payments can only be generated for amounts greater than 0.50 USD.</p>
                            <?php endif; ?>
                        </div>
						<?php
// Ensure $commission_request_id is valid
/*if (isset($company_id) && is_numeric($company_id)) {
    // Retrieve all meta fields for the given post ID
    $all_meta = get_post_meta($company_id);

    // Check if there are any fields to display
    if (!empty($all_meta)) {
        echo '<ul>'; // Start the HTML list
        foreach ($all_meta as $key => $values) {
            // Each $values is an array, loop through it to display all stored values
            foreach ($values as $value) {
                echo '<li>';
                echo '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value);
                echo '</li>';
            }
        }
        echo '</ul>'; // End the HTML list
    } else {
        echo '<p>No meta fields found for this commission request.</p>';
    }
} else {
    echo '<p>Invalid commission request ID.</p>';
}*/
?>
                    </div>
                    <div class="col-md-4 order-md-2 mb-4">
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Your cart</span>
                            <span class="badge badge-secondary badge-pill"><?php echo count($items); ?></span>
                        </h4>
                        <ul class="list-group mb-3">
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                    <div>
                                        <span class="text-muted"><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($item["price_paid"])); ?></span>
                                        <small class="text-muted">X <?php echo esc_html($item["quantity"]); ?></small>
                                    </div>
                                    <h6 class="my-0 text-muted"><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($item["subtotal"])); ?></h6>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total Sales (USD)</span>
                                <strong><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($total_cart)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Agent’s fee</span>
                                <strong><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($total_agent)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Platform’s fee</span>
                                <strong><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($total_platform)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Tax Service Fee</span>
                                <strong><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($total_tax_service)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Total in your currency:</span>
                                <span class="fw-bold"><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($total_to_pay)); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Total to pay:</span>
                                <span class="fw-bold"><?php echo esc_html("$" . number_format($total_to_pay, 2, '.', ',') . " (USD)"); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
