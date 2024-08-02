
<?php




?>

<table class="table custom-table">
    <thead>
        <tr>
            <th scope="col">#ID</th>
            <th scope="col">#Contract SKU</th>
            <th scope="col"><?php echo in_array("commercial_agent", $current_user->roles) ? "Company" : "Agent"; ?></th>
            <th scope="col">Opportunity</th>
            <th scope="col">Total</th>
            <th scope="col">Commissions</th>
            <th scope="col">Total Agent</th>
            <th scope="col">Status</th>
            <th scope="col">Last Update</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($commission_requests)):
            foreach ($commission_requests as $commission_request):

                $contract_id = carbon_get_post_meta($commission_request->ID, 'contract_id');
                $another_part = ProfileUser::get_instance()->get_another_part_of_contract($contract_id);
                $opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');

                $dispute_query = new WP_Query([
                    'post_type'  => 'dispute',
                    'meta_query' => [
                        [
                            'key'   => 'commission_request_id',
                            'value' => $commission_request->ID,
                        ]
                    ],
                ]);

                $payment_query = new WP_Query([
                    'post_type'  => 'payment',
                    'meta_query' => [
                        [
                            'key'   => 'commission_request_id',
                            'value' => $commission_request->ID,
                        ]
                    ],
                ]);

                if($payment_query->have_posts()) {
                    $payment = $payment_query->posts[0];
                    $status_payment = carbon_get_post_meta($payment->ID, 'status');
                }

                if($dispute_query->have_posts()) {
                    $dispute = $dispute_query->posts[0];
                    $status_dispute = carbon_get_post_meta($dispute->ID, 'status');
                }

                $is_paid = isset($status_payment) && $status_payment === "succeeded"? true: false;
                $has_open_dispute = isset($status_dispute) &&  $status_dispute === "pending"?true:false;

                $total_cart_commission_request = carbon_get_post_meta($commission_request->ID, 'total_cart');
                $total_agent_commission_request = carbon_get_post_meta($commission_request->ID, 'total_agent');
                $date_commission_request = carbon_get_post_meta($commission_request->ID, 'date');
                $human_date = Helper::get_human_time_diff($date_commission_request);

                $initiating_user_id = carbon_get_post_meta($commission_request->ID, "initiating_user");
                
                $status_commission_request = carbon_get_post_meta($commission_request->ID, 'status');


                $last_update_text = Helper::get_last_update_by_and_date($commission_request->ID);

                $status_class = '';
                $status_text = '';

                switch ($status_commission_request) {
                    case 'pending':
                        $status_class = 'text-bg-primary';
                        $status_text = "Pending";
                        break;
                    case 'accepted':
                        $status_class = 'text-bg-success';
                        $status_text = "Accepted";
                        break;
                    case 'in_dispute':
                        $status_class = 'text-bg-danger';
                        $status_text = "In Dispute";
                        break;
                }

                switch ($status_payment) {
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

                // Status for dispute
                $dispute_badge = '';

                switch ($status_dispute) {
                    case 'pending':
                        $dispute_badge = '<span class="badge bg-warning">Dispute Pending</span>';
                        break;
                    case 'decline':
                        $dispute_badge = '<span class="badge bg-danger">Dispute Declined</span>';
                        break;
                    case 'resolve':
                        $dispute_badge = '<span class="badge bg-success">Dispute Resolved</span>';
                        break;
                }

             
                ?>

                <tr>
                    <td><?php echo $commission_request->ID;?></td>
                    <td><span class="txt-sm"><?php echo carbon_get_post_meta($contract_id, "sku"); ?></span></td>
                    <td><a href=""><?php echo $another_part->post_title; ?></a></td>
                    <td><a href=""><?php echo get_the_title($opportunity_id); ?></a></td>
                    <td><?php echo esc_html("$" . number_format($total_cart_commission_request, 2, ',', '')); ?></td>
                    <td><?php echo esc_html(carbon_get_post_meta($contract_id, "commission") . "%"); ?></td>
                    <td><?php echo esc_html("$" . number_format($total_agent_commission_request, 2, ",", "")); ?></td>
                    <td>
                        <span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span>
                        <?php if ($status_dispute) {
                            echo $dispute_badge;
                        }?>
                        <?php if ($status_payment) {
                            echo $dispute_payment;
                        }?>
                   
                    </td>
                    <td><?php echo esc_html($last_update_text); ?></td>
                    <td>
                        <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                            <li class="list-inline-item">
                                <a class="operation" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $user_counterparty_id; ?>" data-user-id="<?php echo esc_attr($user_counterparty_id); ?>">
                                    <i class="chat text-secondary fa-solid fa-comments"></i>
                                </a>
                            </li>

                            <?php if (in_array("company", $current_user->roles) && $commission_request):
                                if (!$has_open_dispute && !$is_paid): ?>
                                    <li class="list-inline-item">
                                        <a class="operation" data-bs-toggle="modal" data-bs-target="#modal-dispute" data-commission-request="<?php echo $commission_request->ID; ?>">
                                            <i class="text-warning fa-solid fa-scale-balanced"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!$has_open_dispute && !$is_paid):?>
                                  
                                    <li class="list-inline-item">
                                        <a class="operation" href="<?php echo $dasboard->get_role_url_link_dashboard_page("payment_create"); ?>?commission_request_id=<?php echo $commission_request->ID; ?>">
                                            <i class="text-success fa-solid fa-money-check-dollar"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (in_array("commercial_agent", $current_user->roles) && $commission_request && $current_user->ID === $initiating_user_id): ?>
                                <form class="operation delete-commission-request-form d-inline">
                                    <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete_commission_request_nonce"); ?>"/>
                                    <input type="hidden" name="commission_request_id" value="<?php echo $commission_request->ID; ?>">
                                    <button type="submit" class="operation"><i class="fa-solid text-danger fa-trash"></i></button>
                                </form>
                            <?php endif; ?>

                            <div class="modal fade" id="chat-modal-<?php echo $user_counterparty_id; ?>" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php echo do_shortcode('[better_messages_user_conversation user_id="' . $user_counterparty_id . '"]'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </ul>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php endif; ?>
</table>

<?php
$template_path = 'templates/dashboard/form-commission-request.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}

$template_path = 'templates/dashboard/form-dispute.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}
?>

