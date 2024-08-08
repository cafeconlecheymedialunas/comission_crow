<div class="table-container">
<table class="table default-table">
                    <thead>
                        <tr>
                            <th scope="col">#SKU</th>
                            <th scope="col">Opportunity</th>
                            <th scope="col"><?php echo in_array("commercial_agent", $current_user->roles)?"Company":"Commercial Agent";?></th>
                            <th scope="col">Commission</th>
                            <th scope="col">Minimal Price</th>
                            <th scope="col">Status</th>
                            <th scope="col">Last Update</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contracts)) :
                            foreach ($contracts as $contract) :
                                
                                $another_part = ProfileUser::get_instance()->get_another_part_of_contract($contract->ID);
                                $another_part_user = ProfileUser::get_instance()->get_user_another_part_of_contract($contract->ID);

                                

                                
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
                                $last_update_text = $last_sender_history_user->first_name ." - ". Helper::get_human_time_diff($history_status_end["date_status"]). " ago";
                            
                                ?>
                                
                                    <tr>
                                        <th scope="row"><span class="text-sm"><?php echo carbon_get_post_meta($contract->ID, "sku"); ?></span></th>
                                        <td><a href=""><?php echo esc_html($opportunity->post_title); ?></a></td>
                                        
                                        <td><?php echo esc_html($another_part->post_title); ?></td>
                                    
                                        <td><?php echo esc_html($commission); ?> %</td>
                                        <td><?php echo esc_html(Helper::format_price($minimal_price)); ?></td>
                                        <td><span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span></td>
                                        <td><?php echo ($last_sender_history_user && $history_status_end["date_status"])?$last_update_text:""; ?></td>
                                        <td>
                                            <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                                                <?php if ($status === "pending" && $last_sender_history !== $current_user->ID) : ?>
                                                <li class="list-inline-item">
                                                    <form class="update-status-contract-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-contract-nonce"); ?>"/>
                                                        <input type="hidden" name="contract_id" value="<?php echo esc_attr($contract->ID); ?>">
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button type="submit" class="operation"><i class="accepted text-success fa-solid fa-check"></i></button>
                                                    </form>
                                                </li>
                                                <li class="list-inline-item">
                                                    <form class="update-status-contract-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-contract-nonce"); ?>"/>
                                                        <input type="hidden" name="contract_id" value="<?php echo esc_attr($contract->ID); ?>">
                                                        <input type="hidden" name="status" value="refused">
                                                        <button type="submit" class="operation"><i class="text-danger refused fa-solid fa-xmark"></i></button>
                                                    </form>
                                                </li>
                                                <?php endif; ?>
                                                <?php if ($status === "accepted") : ?>
                                                <li class="list-inline-item">
                                                    <form class="update-status-contract-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-contract-nonce"); ?>"/>
                                                        <input type="hidden" name="contract_id" value="<?php echo esc_attr($contract->ID); ?>">
                                                        <input type="hidden" name="status" value="finished">
                                                        <button type="submit" class="operation"><i class="finished text-warning fa-solid fa-flag-checkered"></i></button>
                                                    </form>
                                                </li>
                                                <?php endif; ?>
                                                <li class="list-inline-item">
                                                    <button class="operation" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $another_part_user->ID;?>" data-user-id="<?php echo esc_attr($another_part_user->ID); ?>">
                                                        <i class="chat text-secondary fa-solid fa-comments"></i>
                                                    </button>
                                                </li>
                                                <?php if(in_array("commercial_agent", $current_user->roles)):?>
                                                    <?php if ($status === "accepted" || $status === "finished" || $status === "finishing") : ?>
                                                        <?php if(!$commission_requests):?>
                                                        <li class="list-inline-item">
                                                            <button class="operation" data-bs-toggle="modal" data-bs-target="#modal-commission" data-contract-id="<?php echo esc_attr($contract->ID); ?>">
                                                                <i class="commission-request  text-primaryfas fa-percentage"></i>
                                                            </button>
                                                        </li>
                                                        <?php endif;?>
                                                    <?php endif;?>
                                                <?php endif;?>
                                                
                                                <div class="modal fade" id="chat-modal-<?php echo $another_part_user->ID;?>" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo do_shortcode('[better_messages_user_conversation user_id="'.$another_part_user->ID.'"]');?>
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
</div>