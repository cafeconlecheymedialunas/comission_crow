<table class="table custom-table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Opportunity</th>
                            <th scope="col">Commercial agent</th>
                            <th scope="col">Total</th>
                            <th scope="col">Total Agent</th>
                            <th scope="col">Status</th>
                            <th scope="col">Origin</th>
                            <th scope="col">Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contracts)) :
                            foreach ($contracts as $contract) :
                            
                                $commercial_agent = carbon_get_post_meta($contract->ID, 'commercial_agent');
                                

                                $counterparter_key = in_array("company", $current_user->roles)?"company":"commercial_agent";
                                
                                $counterparty = carbon_get_post_meta($contract->ID, $counterparter_key);

                                $counterparty = get_post($counterparty);
                                
                                

                                $user_counterparty_id = carbon_get_post_meta($counterparty->ID, 'user_id');

                                
                                
                                $opportunity = carbon_get_post_meta($contract->ID, 'opportunity');
                               
                            
                              

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

                                $commission_request =  $query->posts[0];

                                $status_commission_request = carbon_get_post_meta($commission_request->ID, 'status');
                                
                                $history_status_commission_request = carbon_get_post_meta($commission_request->ID, 'status_history');

                                if($history_status_commission_request) {
                                    $history_status_end = end($history_status_commission_request);
                                    $sender_commission_request = $history_status_end["changed_by"];
                                }
                                
                                

                                $total_cart_commission_request = carbon_get_post_meta($commission_request->ID, 'total_cart');
                                $total_agent_commission_request = carbon_get_post_meta($commission_request->ID, 'total_agent');
                                
                                $date_commission_request = carbon_get_post_meta($commission_request->ID, 'date');
                                
                                

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
                                    case 'dispute':
                                        $status_class = 'text-bg-danger';
                                        $status_text = "Dispute";
                                        break;

                                }
                               
                                if($commission_request):
                                    ?>
                                
                                    <tr>
                                        <th scope="row"><?php echo $contract->ID; ?></th>
                                        <td><?php echo get_the_title($opportunity->ID); ?></td>
                                        <td><?php echo get_the_title($commercial_agent->ID); ?></td>
                                        <td><?php echo esc_html($total_cart_commission_request); ?></td>
                                        <td><?php echo esc_html($total_agent_commission_request); ?></td>
                                        <td><span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span></td>
                                        <td><?php echo esc_html($current_user->ID === $sender_commission_request ? "Sent" : "Received"); ?></td>
                                        <td><?php echo esc_html($date_commission_request); ?></td>
                                        <td>
                                            <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                                        
                                                <li class="list-inline-item">
                                                    <button class="" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $user_counterparty_id ;?>" data-user-id="<?php echo esc_attr($user_counterparty_id); ?>">
                                                        <i class="chat text-secondary fa-solid fa-comments"></i>
                                                    </button>
                                                </li>
                                                
                                                <?php if(in_array("company", $current_user->roles) && $commission_request):?>
                                                 
                                                        <li class="list-inline-item">
                                                            <a class=""  href="<?php //echo home_url("".$contract->ID);?>"><i class="text-secondary view fa-regular fa-eye"></i></a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a class=""  href="<?php //echo home_url("".$contract->ID);?>"><i class="text-info fa-solid fa-money-check-dollar"></i></a>
                                                        </li>
                                                        <?php
                                                            
                                                        
                                                           
                                                    ?>
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php echo $url; ?>"><i class="text-primary fa-solid fa-scale-balanced"></i></a>
                                                        </li>
                                                        
                                                <?php endif;?>
                                                <div class="modal fade" id="chat-modal-<?php echo $user_counterparty_id ;?>" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo do_shortcode('[better_messages_user_conversation user_id="'.$user_counterparty_id .'"]');?>
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
                <?php
        $template_path = 'templates/dashboard/form-request-commission.php';
                        if (locate_template($template_path)) {
                            include locate_template($template_path);
                        }
                        ?> 