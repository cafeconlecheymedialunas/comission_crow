<div class="container mt-5" id="commission-request" data-id="<?php echo $commission_request->ID; ?>">
  <div class="row">
    <div class="col-md-8 order-md-1">
      
      <form method="POST">
        <div class="row">
          <div class="col-md-6 mb-3">
            <h4 class="mb-3">Contract</h4>
            <p class="fw-bold">Sku #<?php echo esc_html(carbon_get_post_meta($contract_id, "sku"));?></p>
            <p>Agent: <?php echo esc_html($user->display_name);?></p>
            <p>Agent email: <?php echo esc_html($user->user_email);?></p>
            <p>Agent location: <?php echo esc_html($location->name);?></p>
          </div>
          <div class="col-md-6 mb-3">
            <h4 class="mb3">Opportunity</h4>
            <?php the_post_thumbnail($opportunity_id);?>
            <h6><?php echo esc_html(get_the_title($opportunity_id));?></h6>
            <p>Minimun price: $<?php echo esc_html(carbon_get_post_meta($contract_id, "minimal_price"));?></p>
            <p>Commission: <?php echo esc_html(carbon_get_post_meta($contract_id, "commission"));?>%</p>
          </div>
        </div>
        <hr class="mb-4">

        <h4 class="mb-3">Payment</h4>

        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="stripe" name="paymentMethod" value="stripe" selected type="radio" class="custom-control-input" checked required>
            <label class="custom-control-label" for="stripe">Stripe</label>
          </div>
          <div class="custom-control custom-radio">
            <input id="debit" name="paymentMethod" disabled type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="debit">Deposit</label>
          </div>
          <input type="hidden" name="payment_init" value="on">
          <input type="hidden" name="commission_request_id" value="<?php echo $commission_request->ID; ?>">
        <input type="submit" value="Pay" class="btn btn-primary">
        </div>
      </form>
    </div>
    <div class="col-md-4 order-md-2 mb-4">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Your cart</span>
        <span class="badge badge-secondary badge-pill">3</span>
      </h4>
      <ul class="list-group mb-3">
        <?php foreach ($items as $item) :?>
            <li class="list-group-item d-flex justify-content-between lh-condensed">
                <div>
                    <span class="text-muted">$<?php echo $item["price_paid"];?></span>
                    <small class="text-muted">X <?php echo $item["quantity"];?></small>
                </div>
                <h6 class="my-0 text-muted">$<?php echo $item["subtotal"];?></h6>
                
            </li>
        <?php endforeach;?>
      
        
        <li class="list-group-item d-flex justify-content-between">
          <span>Total Sales (USD)</span>
          <strong>$<?php echo $total_cart;?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span>Commission of Agent </span>
          <strong>$<?php echo calculate_agent_price($commission_request->ID);?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span>Commission of Nexfy</span>
          <strong>$<?php echo calculate_platform_price($commission_request->ID);?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span>Stipe service fee</span>
          <strong>$<?php echo calculate_tax_stripe_price($commission_request->ID);?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span class="fw-bold">Total:</span>
            <span class="fw-bold">$<?php echo calculate_total($commission_request->ID);?></span>
        </li>
      </ul>
    </div>
  </div>
</div>
