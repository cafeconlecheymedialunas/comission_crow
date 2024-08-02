<?php

$commission_request_id = isset($_GET["commission_request_id"]) ? intval($_GET["commission_request_id"]) : 0;

if (!$commission_request_id) {
    echo '<div class="alert alert-danger" role="alert">Invalid commission request ID.</div>';
    return;
}

$commission_request = get_post($commission_request_id);

$args = [
    'post_type' => 'payment',
    'meta_query' => [
        [
            'key' => "commission_request_id",
            'value' => $commission_request_id,
            'compare' => '='
        ]
    ],
    'posts_per_page' => -1,
];
$payment = new WP_Query($args);

if (!$commission_request) {
    echo '<div class="alert alert-danger" role="alert">This commission request does not exist.</div>';
    return;
}

if ($payment->have_posts()) {
    echo '<div class="alert alert-info" role="alert">This commission request has already been paid.</div>';
    return;
}

// Si llegamos aquí, el commission_request existe y no ha sido pagado
$items = carbon_get_post_meta($commission_request_id, 'items');
$contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
$total_cart = carbon_get_post_meta($commission_request_id, 'total_cart');

if (!$contract_id) {
    echo '<div class="alert alert-danger" role="alert">Contract does not exist.</div>';
    return;
}

$opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');
$company_id = carbon_get_post_meta($contract_id, 'company_id');
$sku = carbon_get_post_meta($contract_id, 'sku');
$minimal_price = carbon_get_post_meta($contract_id, "minimal_price");
$commission = carbon_get_post_meta($contract_id, "commission");

// Verificar la existencia de datos antes de renderizar
$has_opportunity = !empty($opportunity_id);
$has_company = !empty($company_id);
$has_sku = !empty($sku);
$has_minimal_price = !empty($minimal_price);
$has_commission = !empty($commission);

// Obtener el usuario y la ubicación
$user = wp_get_current_user();
$location = get_user_meta($user->ID, 'location', true); // Ejemplo para obtener la ubicación del usuario

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
                                <h4 class="mb-3">Contract</h4>
                                <?php if ($has_sku): ?>
                                    <p class="fw-bold">Sku #<?php echo esc_html($sku); ?></p>
                                <?php endif; ?>
                                <?php if ($user): ?>
                                    <p>Agent: <?php echo esc_html($user->first_name . " " . $user->last_name); ?></p>
                                    <p>Agent email: <?php echo esc_html($user->user_email); ?></p>
                                <?php endif; ?>
                                <?php if ($location): ?>
                                    <p>Agent location: <?php echo esc_html($location); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-3">Opportunity</h4>
                                <?php if ($has_opportunity): ?>
                                    <?php if (has_post_thumbnail($opportunity_id)) {
                                        echo get_the_post_thumbnail($opportunity_id);
                                    } ?>
                                    <h6><?php echo esc_html(get_the_title($opportunity_id)); ?></h6>
                                <?php endif; ?>
                                <?php if ($has_minimal_price): ?>
                                    <p>Minimum price: $<?php echo esc_html($minimal_price); ?></p>
                                <?php endif; ?>
                                <?php if ($has_commission): ?>
                                    <p>Commission: <?php echo esc_html($commission); ?>%</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="mb-4">
                        <h4 class="mb-3">Payment</h4>
                        <div class="d-block my-3">
                            <div class="custom-control custom-radio">
                                <input id="stripe" name="paymentMethod" value="stripe" type="radio" class="custom-control-input" checked required>
                                <label class="custom-control-label" for="stripe">Stripe</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="debit" name="paymentMethod" disabled type="radio" class="custom-control-input" required>
                                <label class="custom-control-label" for="debit">Deposit</label>
                            </div>
                           
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                <input type="hidden" name="action" value="create_payment">
                                <input type="hidden" name="amount" value="2000"> <!-- Cantidad en centavos -->
                                <input type="hidden" name="currency" value="usd"> <!-- Moneda -->
                                <input type="hidden" name="payment_init" value="on">
                                <input type="hidden" name="commission_request_id" value="<?php echo esc_attr($commission_request_id); ?>">
                                <input type="hidden" name="success_url" value="<?php echo esc_url(home_url('/dashboard/company/payment/success')); ?>">
                                <input type="hidden" name="cancel_url" value="<?php echo esc_url(home_url('/dashboard/company/payment/cancel')); ?>">
                                <button class="btn btn-primary" type="submit">Pagar con Stripe</button>
                            </form>
                        </div>
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
                                        <span class="text-muted">$<?php echo esc_html($item["price_paid"]); ?></span>
                                        <small class="text-muted">X <?php echo esc_html($item["quantity"]); ?></small>
                                    </div>
                                    <h6 class="my-0 text-muted">$<?php echo esc_html($item["subtotal"]); ?></h6>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total Sales (USD)</span>
                                <strong>$<?php echo esc_html($total_cart); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Commission of Agent </span>
                                <strong>$<?php echo esc_html(calculate_agent_price($commission_request_id)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Commission of Nexfy</span>
                                <strong>$<?php echo esc_html(calculate_platform_price($commission_request_id)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Stripe service fee</span>
                                <strong>$<?php echo esc_html(calculate_tax_stripe_price($commission_request_id)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold">$<?php echo esc_html(calculate_total($commission_request_id)); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
