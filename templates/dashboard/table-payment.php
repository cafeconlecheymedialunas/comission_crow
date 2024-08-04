
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


                $status = carbon_get_post_meta($payment->ID, 'status');
               
             

                $total_cart_commission_request = carbon_get_post_meta($commission_request->ID, 'total_cart');
                $total_agent_commission_request = carbon_get_post_meta($commission_request->ID, 'total_agent');
                $date_commission_request = carbon_get_post_meta($commission_request->ID, 'date');
                $human_date = Helper::get_human_time_diff($date_commission_request);

                $initiating_user_id = carbon_get_post_meta($commission_request->ID, "initiating_user");
                
                


                $last_update_text = Helper::get_last_update_by_and_date($commission_request->ID);

                $status_class = '';
                $status_text = '';

              

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
                    <td><?php echo $commission_request->ID;?></td>
                    <td><span class="txt-sm"><?php echo carbon_get_post_meta($contract_id, "sku"); ?></span></td>
                    <td><a href=""><?php echo $another_part->post_title; ?></a></td>
                    <td><a href=""><?php echo get_the_title($opportunity_id); ?></a></td>
                    <td><?php echo esc_html(Helper::format_price($total_cart_commission_request)); ?></td>
                    <td><?php echo esc_html(carbon_get_post_meta($contract_id, "commission") . "%"); ?></td>
                    <td><?php echo esc_html(Helper::format_price($total_agent_commission_request)); ?></td>
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

