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

                               

                                $dispute_query = new WP_Query([
                                    'post_type'  => 'dispute',
                                    'meta_query' => [
                                        [
                                            'key'   => 'commission_id',
                                            'value' => $commission_request->ID,
                                            'compare' => '=', // Comparar el valor exacto
                                        ]
                                    ]
                                ]);

                                $dispute =  $dispute_auery->posts[0];

                                if(is_wp_error($dispute)) {
                                    var_dump($dispute_query);
                                }



                               

                              
                             
                                

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
                               
                                if($dispute):
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
                                            <ul class="list-inline mb-0">
                                        
                                                <li class="list-inline-item">
                                                    <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#chat-modal-<?php echo $user_counterparty_id ;?>" data-user-id="<?php echo esc_attr($user_counterparty_id); ?>"><i class="fas fa-envelope"></i></button>
                                                </li>
                                                
                                                <?php if(in_array("company", $current_user->roles) && $commission_request):?>
                                                 
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php //echo home_url("".$contract->ID);?>">View</a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php //echo home_url("".$contract->ID);?>">Pay</a>
                                                        </li>
                                                        <?php
                                                            
                                                        
                                                           
                                                    ?>
                                                        <li class="list-inline-item">
                                                            <a class="btn btn-secondary btn-sm"  href="<?php echo $url; ?>">Dispute</a>
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
                $user_from_id = get_current_user_id(); // ID del usuario que envía el mensaje
                        $user_to_id = 48; // ID del usuario que recibe el mensaje, cámbialo según sea necesario

                        ?>
<form id="message-form">
    <?php wp_nonce_field('save_message_nonce', 'message_nonce'); ?>
    <input type="hidden" id="post_id" value="<?php echo get_the_ID(); ?>">
    <input type="hidden" id="from" name="from" value="<?php echo $user_from_id; ?>">
    <input type="hidden" id="to" name="to" value="<?php echo $user_to_id; ?>">
    <textarea id="message" name="message"></textarea>
    <button type="submit">Send Message</button>
</form>
<div id="chat-box">
    <?php
                            $messages = carbon_get_post_meta(get_the_ID(), 'messages');
                        if (!empty($messages)) {
                            foreach ($messages as $message) {
                                $from_user = get_user_by('id', $message['from']);
                                $from = ($from_user->ID === $user_from_id) ? 'You' : 'Other User';
                                echo '<div class="message">';
                                echo '<strong>' . esc_html($from) . ':</strong><p>' . wpautop(esc_html($message['message'])) . '</p>';
                                echo '</div>';
                            }
                        }
                        ?>
</div>
