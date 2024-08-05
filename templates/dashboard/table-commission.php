<?php
// Obtén el usuario actual al inicio
$current_user = wp_get_current_user();

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
                $another_part_user_id = get_post_meta($another_part->ID, "_user_id")[0];
                $another_user = get_user_by("ID", $another_part_user_id);

                $opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');
                $status = get_post_meta($commission_request->ID, "_status")[0];
                
                // Verifica si hay una disputa abierta
                $dispute_args = [
                    'post_type' => 'dispute',
                    'meta_query' => [
                        [
                            'key' => 'commission_request_id',
                            'value' => $commission_request->ID,
                        ],
                        [
                            'key' => 'status',
                            'value' => ['dispute_pending', 'dispute_accepted'],
                            'compare' => 'IN',
                        ],
                    ],
                ];
                $existing_dispute = get_posts($dispute_args);
                
                // Verifica si hay un pago registrado (que no sea cancelado)
                $payment_args = [
                    'post_type' => 'payment',
                    'meta_query' => [
                        [
                            'key' => 'commission_request_id',
                            'value' => $commission_request->ID,
                        ],
                        [
                            'key' => 'status',
                            'value' => ['payment_completed', 'payment_failed', 'payment_pending'],
                            'compare' => 'IN',
                        ],
                    ],
                ];
                $existing_payment = get_posts($payment_args);
                
                $total_cart_commission_request = carbon_get_post_meta($commission_request->ID, 'total_cart');
                $total_agent_commission_request = carbon_get_post_meta($commission_request->ID, 'total_agent');
                $date_commission_request = carbon_get_post_meta($commission_request->ID, 'date');
                $human_date = Helper::get_human_time_diff($date_commission_request);
                $initiating_user_id = carbon_get_post_meta($commission_request->ID, "initiating_user");
                $last_update_text = Helper::get_last_update_by_and_date($commission_request->ID);

                ?>

                <tr>
                    <td><?php echo $commission_request->ID; ?></td>
                    <td><span class="txt-sm"><?php echo carbon_get_post_meta($contract_id, "sku"); ?></span></td>
                    <td><a href=""><?php echo esc_html($another_user->data->display_name); ?></a></td>
                    <td><a href=""><?php echo get_the_title($opportunity_id); ?></a></td>
                    <td><?php echo esc_html(Helper::format_price($total_cart_commission_request)); ?></td>
                    <td><?php echo esc_html(carbon_get_post_meta($contract_id, "commission") . "%"); ?></td>
                    <td><?php echo esc_html(Helper::format_price($total_agent_commission_request)); ?></td>
                    <td>
                        <span class=""><?php echo $status; ?></span>
                    </td>
                    <td><?php echo esc_html($last_update_text); ?></td>
                    <td>
                        <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                            <li class="list-inline-item">
                                <a class="operation" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $another_user->data->ID; ?>" data-user-id="<?php echo esc_attr($another_user->data->ID); ?>">
                                    <i class="chat text-secondary fa-solid fa-comments"></i>
                                </a>
                            </li>

                            <?php if (in_array("company", $current_user->roles) && $commission_request):
                                // Mostrar botón de disputa si no hay disputa abierta
                                if (empty($existing_dispute) && empty($existing_payment)): ?>
                                    <li class="list-inline-item">
                                        <a class="operation" data-bs-toggle="modal" data-bs-target="#modal-dispute" data-commission-request="<?php echo $commission_request->ID; ?>">
                                            <i class="text-warning fa-solid fa-scale-balanced"></i>
                                        </a>
                                    </li>
                                <?php endif;
                             
                                if (empty($existing_dispute) && (empty($existing_payment) || in_array($status, ['payment_canceled']))): ?>
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

                            <div class="modal fade" id="chat-modal-<?php echo $another_user->data->ID; ?>" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php echo do_shortcode('[better_messages_user_conversation user_id="' . $another_user->data->ID . '"]'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </ul>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php endif; ?>
    </tbody>
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
