<table class="table custom-table">
                    <thead>
                        <tr>
                            <th scope="col">#Contract SKU</th>
                            <th scope="col">Agent</th>
                            <th scope="col">Opportunity</th>
                            <th scope="col">Total</th>
                            <th scope="col">Commissions</th>
                            <th scope="col">Total Agent</th>
                            <th scope="col">Status</th>
                            <th scope="col">Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contracts)) :
                            foreach ($contracts as $contract) :
                            
                                $commercial_agent_id = carbon_get_post_meta($contract->ID, 'commercial_agent');
                                

                                $counterparter_key = in_array("company", $current_user->roles)?"company":"commercial_agent";
                                
                                $counterparty_id = carbon_get_post_meta($contract->ID, $counterparter_key);

                                $counterparty = get_post($counterparty_id);
                                
                                

                                $user_counterparty_id = carbon_get_post_meta($counterparty_id, 'user_id');

                                
                                
                                $opportunity_id = carbon_get_post_meta($contract->ID, 'opportunity');


                               
                            
                              

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

                                if($commission_request):

                                    $status_commission_request = carbon_get_post_meta($commission_request->ID, 'status');
                                
                                    $history_status_commission_request = carbon_get_post_meta($commission_request->ID, 'status_history');

                                    if($history_status_commission_request) {
                                        $history_status_end = end($history_status_commission_request);
                                        $sender_commission_request = $history_status_end["changed_by"];
                                    }
                                
                                

                                    $total_cart_commission_request = carbon_get_post_meta($commission_request->ID, 'total_cart');
                                    $total_agent_commission_request = carbon_get_post_meta($commission_request->ID, 'total_agent');
                                
                                    $date_commission_request = carbon_get_post_meta($commission_request->ID, 'date');
                               
                                    $human_date = $admin->get_human_time_diff($date_commission_request);
                                
                                    $initiating_user_id = carbon_get_post_meta($commission_request->ID, "initiating_user");

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
                               

                                    ?>
                                
                                    <tr>
                                        
                                        <td><?php echo carbon_get_post_meta($contract->ID, "sku"); ?></td>
                                        <td><a href=""><?php echo get_the_title($commercial_agent_id); ?></a></td>
                                        <td><a href=""><?php echo get_the_title($opportunity_id);?></a></td>
                                        <td><?php echo esc_html("$".number_format($total_cart_commission_request, 2, ',', '')); ?></td>
                                        <td><?php echo esc_html(carbon_get_post_meta($contract->ID, "commission")."%"); ?></td>
                                        <td><?php echo esc_html("$".number_format($total_agent_commission_request, 2, ",", "")); ?></td>
                                      
                                        <td><span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span></td>
                                        <td><?php echo esc_html($human_date); ?></td>
                                        <td>
                                            <ul class="p-0 mb-0 d-flex justify-content-center align-items-center">
                                        
                                                <li class="list-inline-item">
                                                    <a class="" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $user_counterparty_id ;?>" data-user-id="<?php echo esc_attr($user_counterparty_id); ?>">
                                                        <i class="chat text-secondary fa-solid fa-comments"></i>
                                                    </a>
                                                </li>
                                                
                                                <?php if(in_array("company", $current_user->roles) && $commission_request):?>
                                                 
                                                        <li class="list-inline-item">
                                                            <a class=""  href="<?php //echo home_url("".$contract->ID);?>"><i class="text-secondary view fa-regular fa-eye"></i></a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a class=""  href="<?php //echo home_url("".$contract->ID);?>"><i class="text-success fa-solid fa-money-check-dollar"></i></a>
                                                        </li>
                                                       
                                                        <li class="list-inline-item">
                                                            <a data-bs-toggle="modal"  data-bs-target="#modal-dispute" data-commission-request="<?php echo $commission_request->ID;?>"><i class="text-warning fa-solid fa-scale-balanced"></i></a>
                                                        </li>
                                                       
                                                        
                                                <?php endif;?>
                                                <?php if(in_array("commercial_agent", $current_user->roles) && $commission_request && $current_user->ID === $initiating_user_id):?>
                                                 
                                                        <form class="delete-commission-request-form d-inline">
                                                            <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete_commission_request_nonce"); ?>"/>
                                                            <input type="hidden" name="commission_request_id" value="<?php echo $commission_request->ID;?>">
                                                            <button type="submit" class="operation"><i class="fa-solid text-danger fa-trash"></i></button>
                                                        </form>
                                                        
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
        $template_path = 'templates/dashboard/form-commission-request.php';
                        if (locate_template($template_path)) {
                            include locate_template($template_path);
                        }
                        ?> 

                        
<?php $template_path = 'templates/dashboard/form-dispute.php';
                        if (locate_template($template_path)) {
                            include locate_template($template_path);
                        }


                        ?>