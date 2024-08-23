<?php

$payments = ProfileUser::get_instance()->get_payments_for_user();

?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Payments"); ?></h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-container">

<table class="table custom-table">
    <thead>
        <tr>
            <th scope="col">#ID</th>
            <th scope="col">Commission Request</th>
            <th scope="col">Amount</th>
            <th scope="col">Source</th>
            <th scope="col">Status</th>
            <th scope="col">Date</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($payments)):
            foreach ($payments as $payment):

                $payment_stripe_id = carbon_get_post_meta($payment->ID, 'payment_stripe_id');
                $commission_request_id = carbon_get_post_meta($payment->ID, 'commission_request_id');

                $contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');

                $sku = carbon_get_post_meta($contract_id, 'sku');

                $total_paid = carbon_get_post_meta($payment->ID, 'total_paid');

                $date = carbon_get_post_meta($payment->ID, 'date');

                $status = 'succeeded';

                $source = carbon_get_post_meta($payment->ID, 'source');

                $invoice = carbon_get_post_meta($payment->ID, 'invoice');

                switch ($status) {
                    case 'succeeded':
                        $payment_badge = '<span class="badge bg-success">Payment Success</span>';
                        break;
                    case 'pending':
                        $payment_badge = '<span class="badge bg-warning">Pending Payment</span>';
                        break;
                    case 'failed':
                        $payment_badge = '<span class="badge bg-danger">Failed Payment</span>';
                        break;
                    case 'canceled':
                        $payment_badge = '<span class="badge bg-secondary">Canceled Payment</span>';
                        break;
                    case 'requires_payment_method':
                        $payment_badge = '<span class="badge bg-info">Payment Requires Payment Method</span>';
                        break;
                    case 'requires_confirmation':
                        $payment_badge = '<span class="badge bg-info">Payment Requires Confirmation</span>';
                        break;
                    case 'requires_action':
                        $payment_badge = '<span class="badge bg-info">Payment Requires Action</span>';
                        break;
                    case 'in_process':
                        $payment_badge = '<span class="badge bg-info">Payment In Process</span>';
                        break;
                    case 'authorized':
                        $payment_badge = '<span class="badge bg-info">Authorized</span>';
                        break;
                }

                ?>

				                <tr>
		                            <td><?php echo $payment->ID; ?></td>
				                    <td><span class="txt-sm"><?php echo $commission_request_id; ?></span></td>
		                            <td><?php echo esc_html(Helper::convert_price_to_selected_currency($total_paid)); $template = 'templates/info-price.php';
            if (locate_template($template)) {
                include locate_template($template);
            }?></td>
				                    <td>Stripe</td>

				                    <td>
				                        <?php if ($status) {
				                            echo $payment_badge;
				                        }?>

				                    </td>
		                            <td><?php echo $date; ?></td>
				                    <td>
				                        <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                                        
                                              <?php if(!empty($invoice)):?>
                                                <li class="list-inline-item"></li>
                                                <a href="<?php echo wp_get_attachment_url($invoice[0]); ?>" download class="operation">
                                                    <i class="fa-solid fa-file-invoice"></i> 
                                                </a>
                                                </li>
                                                <?php endif;?>
                                                <li class="list-inline-item"></li>
                                                <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('payment_show')); ?>?session_id=<?php echo $payment_stripe_id;?>" class="operation">
                                                 <i class="text-secondary view fa-regular fa-eye"></i>
                                                </a>
                                                </li>
                                        </ul>
		                            </td>
		                        </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                    </table>
                </div>
            </div>
        </div>
</div>



