<table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Opportunity</th>
                            <th scope="col">Commercial agent</th>
                            <th scope="col">Total</th>
                            <th scope="col">Commission</th>
                            <th scope="col">Status</th>
                            <th scope="col">Origin</th>
                            <th scope="col">Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($agreements)) :
                            foreach ($agreements as $agreement) :
                            
                                $commercial_agent = carbon_get_post_meta($agreement->ID, 'commercial_agent');
                                $commercial_agent = get_post($commercial_agent);

                                $user_commercial_agent_id = carbon_get_post_meta($commercial_agent->ID, 'user_id');

                                
                                
                                $opportunity = carbon_get_post_meta($agreement->ID, 'opportunity');
                                $opportunity = get_post($opportunity);

                                

                                $commission = carbon_get_post_meta($agreement->ID, 'commission');
                            
                                $status = carbon_get_post_meta($agreement->ID, 'status');
                                
                                $history_status = carbon_get_post_meta($agreement->ID, 'status_history');

                                $query = new WP_Query([
                                    'post_type'  => 'commission_request',
                                    'meta_query' => [
                                        [
                                            'key'   => 'agreement_id',
                                            'value' => $agreement->ID,
                                            'compare' => '=', // Comparar el valor exacto
                                        ]
                                    ]
                                ]);

                               $commission_request =  $query->posts[0];
                                
                                $history_status_end = end($history_status);

                                $total_commission_request = carbon_get_post_meta($commission_request->ID, 'total');
                                $date_commission_request = carbon_get_post_meta($commission_request->ID, 'date');
                                
                                $sender = $history_status_end["changed_by"];

                                $status_class = '';
                                $status_text = '';

                                switch ($status) {
                                    case 'requested':
                                        $status_class = 'text-bg-primary';
                                        $status_text = "Requested";
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
                                        $finalization_date = carbon_get_post_meta($agreement->ID, 'finalization_date');
                                        $status_text = "Finishing at $finalization_date";
                                        break;
                                    case 'finished':
                                        $status_class = 'text-bg-info';
                                        $status_text = "Finished";
                                        break;
                                }
                               
                                if($commission_request):
                                ?>
                                
                                    <tr>
                                        <th scope="row"><?php echo $agreement->ID; ?></th>
                                        <td><?php echo get_the_title($opportunity->ID); ?></td>
                                        <td><?php echo get_the_title($commercial_agent->ID); ?></td>
                                        <td><?php echo esc_html($total_commission_request); ?></td>
                                        <td><?php echo esc_html($commission); ?></td>
                                        <td><span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span></td>
                                        <td><?php echo esc_html($current_user->ID === $sender ? "Sent" : "Received"); ?></td>
                                        <td><?php echo esc_html($date_commission_request); ?></td>
                                        <td>
                                            <ul class="list-inline mb-0">
                                                <?php if ($status === "requested") : ?>
                                                <li class="list-inline-item">
                                                    <form class="update-status-agreement-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-agreement-nonce"); ?>"/>
                                                        <input type="hidden" name="agreement_id" value="<?php echo esc_attr($agreement->ID); ?>">
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                                    </form>
                                                </li>
                                                <li class="list-inline-item">
                                                    <form class="update-status-agreement-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-agreement-nonce"); ?>"/>
                                                        <input type="hidden" name="agreement_id" value="<?php echo esc_attr($agreement->ID); ?>">
                                                        <input type="hidden" name="status" value="refused">
                                                        <button type="submit" class="btn btn-danger btn-sm">Refuse</button>
                                                    </form>
                                                </li>
                                                <?php endif; ?>
                                                <?php if ($status === "accepted") : ?>
                                                <li class="list-inline-item">
                                                    <form class="update-status-agreement-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("update-status-agreement-nonce"); ?>"/>
                                                        <input type="hidden" name="agreement_id" value="<?php echo esc_attr($agreement->ID); ?>">
                                                        <input type="hidden" name="status" value="finished">
                                                        <button type="submit" class="btn btn-warning btn-sm">Finished</button>
                                                    </form>
                                                </li>
                                                <?php endif; ?>
                                                <li class="list-inline-item">
                                                    <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $user_commercial_agent_id;?>" data-user-id="<?php echo esc_attr($user_commercial_agent_id); ?>"><i class="fas fa-envelope"></i></button>
                                                </li>
                                                <?php if(in_array("commercial_agent",$current_user->roles)):?>
                                                    <?php if ($status === "accepted" || $status === "finished" || $status === "finishing") : ?>
                                                        <?php if(!$commission_request):?>
                                                        <li class="list-inline-item">
                                                            <button class="btn btn-secondary commission-request-button btn-sm" data-bs-toggle="modal" data-bs-target="#modal-commission" data-agreement-id="<?php echo esc_attr($agreement->ID); ?>">Request a Commission</button>
                                                        </li>
                                                        <?php else:?>
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php echo home_url("".$agreement->ID); ?>">View Commission request</a>
                                                        </li>
                                                        <?php endif;?>
                                                    <?php endif;?>
                                                <?php endif;?>
                                                <?php if(in_array("company",$current_user->roles) && $commission_request):?>
                                                 
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php echo home_url("".$agreement->ID); ?>"><i class="fas fa-eye"></i></a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php echo home_url("".$agreement->ID); ?>"><i class="fas fa-dollar-sign"></i></a>
                                                        </li>
                                                           
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php echo home_url("".$agreement->ID); ?>"><i class="fas fa-gavel"></i></a>
                                                        </li>
                                                        
                                                <?php endif;?>
                                                <div class="modal fade" id="chat-modal-<?php echo $user_commercial_agent_id;?>" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo do_shortcode('[better_messages_user_conversation user_id="'.$user_commercial_agent_id.'"]');?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php endif;?>
                            <?php endforeach; ?>
                            
                        <?php endif; ?>
                    </tbody>
                </table>