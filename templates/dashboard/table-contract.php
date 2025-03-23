<div class="table-container">
    <table class="table default-table">
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col">#SKU</th>
                <th scope="col">Opportunity</th>
                <th scope="col"><?php echo in_array("commercial_agent", $current_user->roles) ? "Company" : "Commercial Agent"; ?></th>
                <th scope="col">Commission</th>
                <th scope="col">Minimal Price</th>
                <th scope="col">Status</th>
                <th scope="col">Last Update</th>
               
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($contracts)) :
                foreach ($contracts as $contract) :

                    if (in_array("company", $current_user->roles)) {
                        $another_part_id = carbon_get_post_meta($contract->ID, "commercial_agent");
                    } else {
                        $another_part_id = carbon_get_post_meta($contract->ID, "company");
                    }

                    $another_part = get_post($another_part_id);



                    $another_part_user_id = get_post_meta($another_part_id, "_user_id")[0];
                    $another_part_user = get_user_by("ID", $another_part_user_id);

                    $opportunity = carbon_get_post_meta($contract->ID, 'opportunity');

                    $opportunity = get_post($opportunity);

                    $date = carbon_get_post_meta($contract->ID, 'date');
                    $human_date = Helper::get_human_time_diff($date);
                    $commission = carbon_get_post_meta($contract->ID, 'commission');
                    $minimal_price = carbon_get_post_meta($contract->ID, 'minimal_price');
                    $status = carbon_get_post_meta($contract->ID, 'status');
                    $history_status = carbon_get_post_meta($contract->ID, 'status_history');

                    $query = new WP_Query([
                        'post_type'  => 'commission_request',
                        'meta_query' => [
                            [
                                'key'   => 'contract_id',
                                'value' => $contract->ID,
                                'compare' => '=', // Comparar el valor exacto
                            ]
                        ]
                    ]);

                    $commission_requests =  $query->posts;

                    $history_status_end = end($history_status);

                    $last_sender_history = $history_status_end["changed_by"];

                    $last_sender_history_user = get_user_by("ID", $last_sender_history);

                    $status_class = '';
                    $status_text = '';

                    switch ($status) {
                        case 'pending':
                            $status_class = 'text-bg-primary';
                            $status_text = "Pending";
                            break;
                        case 'accepted':
                            $status_class = 'text-bg-success';
                            $status_text = "Accepted";
                            break;
                        case 'refused':
                            $status_class = 'text-bg-danger';
                            $status_text = "Refused";
                            break;
                        case 'finishing':
                            $status_class = 'text-bg-warning';
                            $finalization_date = carbon_get_post_meta($contract->ID, 'finalization_date');
                            $human_finalization_date = Helper::get_human_time_diff($finalization_date);
                            $status_text = "Finishing in  $human_finalization_date";
                            break;
                        case 'finished':
                            $status_class = 'text-bg-info';
                            $status_text = "Finished";
                            break;
                    }
                    $last_update_text = $last_sender_history_user->first_name . " - " . Helper::get_human_time_diff($history_status_end["date_status"]) . " ago";

            ?>

                    <tr>
                    <td>
                            <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                                <?php if ($status === "pending" && $last_sender_history !== $current_user->ID) : ?>
                                    <li class="list-inline-item">
                                        <form class="update-status-contract-form">
                                            <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-contract-nonce"); ?>" />
                                            <input type="hidden" name="contract_id" value="<?php echo esc_attr($contract->ID); ?>">
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="operation" data-bs-toggle="tooltip" data-bs-placement="right" title="Accept Contract"><i class="accepted text-success fa-solid fa-check"></i></button>
                                        </form>
                                    </li>
                                    <li class="list-inline-item">
                                        <form class="update-status-contract-form">
                                            <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-contract-nonce"); ?>" />
                                            <input type="hidden" name="contract_id" value="<?php echo esc_attr($contract->ID); ?>">
                                            <input type="hidden" name="status" value="refused">
                                            <button type="submit" class="operation" data-bs-toggle="tooltip" data-bs-placement="right" title="Refuse contract"><i class="text-danger refused fa-solid fa-xmark"></i></button>
                                        </form>
                                    </li>
                                <?php endif; ?>
                                <?php if ($status === "accepted") :
                                    $finish_message_tooltip = in_array("commercial_agent", $current_user->roles) ?
                                        "Finish agreement. From the moment you terminate it is no longer valid but you can continue sending commission requests."
                                        : "Terminate contract. This will remain in effect for 30 days after which the agreement will be cancelled. In the meantime the agent can send you commission requests.";

                                ?>


                                    <li class="list-inline-item">
                                        <form class="update-status-contract-form">
                                            <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-contract-nonce"); ?>" />
                                            <input type="hidden" name="contract_id" value="<?php echo esc_attr($contract->ID); ?>">
                                            <input type="hidden" name="status" value="finished">
                                            <button type="submit" class="operation" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo $finish_message_tooltip; ?>"><i class="finished text-warning fa-solid fa-flag-checkered"></i></button>
                                        </form>
                                    </li>
                                <?php endif; ?>
                                <?php if ($another_part_user): ?>
                                    <li class="list-inline-item">
                                        <button class="operation" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $another_part_user->ID; ?>" data-user-id="<?php echo esc_attr($another_part_user->ID); ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Chat with the other party">
                                            <i class="chat text-secondary fa-solid fa-comments"></i>
                                        </button>
                                    </li>
                                <?php endif; ?>
                                <?php if (in_array("commercial_agent", $current_user->roles)): ?>
                                    <?php if ($status === "accepted" || $status === "finished" || $status === "finishing") : ?>
                                        <?php if (!$commission_requests): ?>
                                            <li class="list-inline-item">
                                                <button class="operation" data-bs-toggle="modal" data-bs-target="#modal-commission" data-contract-id="<?php echo esc_attr($contract->ID); ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Create a commission request">
                                                    <i class="commission-request  text-primary fas fa-percentage"></i>
                                                </button>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($another_part_user): ?>
                                    <div class="modal fade" id="chat-modal-<?php echo $another_part_user->ID; ?>" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php echo do_shortcode('[better_messages_user_conversation user_id="' . $another_part_user->ID . '"]'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <li class="list-inline-item">
                            <button class="operation" data-bs-toggle="modal" data-bs-target="#contract-modal-<?php echo $contract->ID; ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="View Contract">
                                <i class="fa-solid fa-eye text-primary"></i>
                            </button>
                                </li>
                                <!-- Modal Contract Content -->
<div class="modal fade" id="contract-modal-<?php echo $contract->ID; ?>" tabindex="-1" aria-labelledby="contractModalLabel-<?php echo $contract->ID; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contractModalLabel-<?php echo $contract->ID; ?>">Contract Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>SKU:</strong> <?php echo carbon_get_post_meta($contract->ID, "sku"); ?></p>
                <p><strong>Opportunity:</strong> <a href="<?php echo home_url()."/opportunity-item/?opportunity_id=".$opportunity->ID; ?>">
                    <?php echo get_the_title($opportunity->ID); ?>
                </a></p>
                <p><strong>Party Involved:</strong> 
                    <?php 
                    if (in_array('company', $current_user->roles)) {
                        $link = home_url() . "/commercial-agent-item/?commercial_agent_id=" . $another_part->ID;
                        $display_name = esc_html($another_part_user->data->display_name);
                        echo '<a href="' . esc_url($link) . '">' . $display_name . '</a>';
                    } else {
                        echo esc_html(carbon_get_post_meta($another_part->ID, "company_name"));
                    }
                    ?>
                </p>
                <p><strong>Commission:</strong> <?php echo esc_html($commission); ?> %</p>
                <p><strong>Minimal Price:</strong> <?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($minimal_price)); ?></p>
                <p><strong>Contract Message:</strong> <?php echo $contract->post_content; ?></p>
                <p><strong>Status:</strong> <span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span></p>
                <p><strong>Last Update:</strong> <?php echo ($last_sender_history_user && $history_status_end["date_status"]) ? $last_update_text : ""; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

                            </ul>
                        </td>
                        <td><span  style="font-size:12px;font-weight:500;">#<?php echo carbon_get_post_meta($contract->ID, "sku"); ?></span></td>
                        <td><a href="<?php echo home_url()."/opportunity-item/?opportunity_id=".$opportunity->ID; ?>">
                            <?php echo get_the_title($opportunity->ID); ?>
                        </a></td>
                        <td><?php if (in_array('company', $current_user->roles)) {

                            $link = home_url() . "/commercial-agent-item/?commercial_agent_id=" . $another_part->ID;
                            $display_name = esc_html($another_part_user->data->display_name);
                            echo '<a href="' . esc_url($link) . '">' . $display_name . '</a>';
                        } else {

                            echo esc_html(carbon_get_post_meta($another_part->ID, "company_name"));
                        } ?>
                        </td>


                        <td><?php echo esc_html($commission); ?> %</td>
                        <td><?php echo Helper::display_price_template(Helper::convert_price_to_selected_currency($minimal_price));
                            ?></td>
                            
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span></td>
                        <td><?php echo ($last_sender_history_user && $history_status_end["date_status"]) ? $last_update_text : ""; ?></td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>