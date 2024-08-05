<?php

// Calcular el balance de la wallet
$current_user_id = get_current_user_id();

$post_associated_user = ProfileUser::get_instance()->get_user_associated_post_type();

$wallet_balance = ProfileUser::get_instance()->calculate_wallet_balance();

// Consultar depósitos para el usuario actual
$deposits = get_posts([
    'post_type' => 'deposit',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => 'user',
            'value' => $current_user_id,
            'compare' => '=',
        ]
    ],
]);

?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Payouts"); ?></h2>
</div>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card d-flex justify-content-between align-items-center">

            <h1 class="d-inline">Balance</h1>

            <h3 class="mb-0"><?php echo Helper::format_price($wallet_balance); ?></h3>

            
            <?php
            
            
            if (isset($wallet_balance) && $wallet_balance > 0): ?>
                <form id="withdraw-founds-form">
                    <input type="hidden" name="security" value="<?php echo wp_create_nonce('withdraw-founds'); ?>"/>
                    <input type="hidden" name="commercial_agent_id" value="<?php echo $post_associated_user->ID; ?>"> 
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Withdraw Funds</button>
                    </div>
                </form>
            <?php else: ?>
                <p class="alert">You have a withdrawal request in process. This may take 48 hours or more</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <form id="update-email-stripe-form">
                <div class="row">
                    <!-- User Fields -->
                    <div class="col-md-12">
                        <i class="fa-brands fs-2 fa-cc-stripe me-3" style></i><label for="stripe_email" class="form-label">  Stripe Email</label>
                        <input type="text" name="stripe_email" id="stripe_email" value="<?php echo carbon_get_post_meta($post_associated_user->ID, "stripe_email"); ?>" class="form-control w-100" >
                        <div class="error-message"></div>
                    </div>
                </div>
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('update_stripe_email_nonce'); ?>"/>
                <input type="hidden" name="commercial_agent_id" value="<?php echo $post_associated_user->ID; ?>">
                <div class="alert alert-danger general-errors" role="alert" style="display:none;"></div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Save Method</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Source</th>
                            <th scope="col">Date</th>
                            <th scope="col">Download Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deposits)):
                            foreach ($deposits as $deposit):
                                $total_paid = carbon_get_post_meta($deposit->ID, 'total_paid');
                                $date = carbon_get_post_meta($deposit->ID, 'date');
                                $source = carbon_get_post_meta($deposit->ID, 'source');
                                $invoice = carbon_get_post_meta($deposit->ID, 'invoice');
                                ?>
                            <tr>
                                <td><?php echo $deposit->ID; ?></td>
                                <td><?php echo esc_html(Helper::format_price($total_paid)); ?></td>
                                <td><i class="fa-brands fa-cc-stripe"></i></td>
                                <td><?php echo Helper::get_human_time_diff($date) . " ago"; ?></td>
                                <td>
                                    <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                                        <?php if (!empty($invoice)): ?>
                                            <li class="list-inline-item"></li>
                                            <a href="<?php echo wp_get_attachment_url($invoice[0]); ?>" download class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
