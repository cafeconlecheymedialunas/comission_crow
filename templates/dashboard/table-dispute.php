<table class="table custom-table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">#Contract Sku</th>
            <th scope="col">Another Part</th>
            <th scope="col">Subject</th>
            <th scope="col">Status</th>
            <th scope="col">Date</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($disputes)) :
            foreach ($disputes as $dispute) :

                $commission_request_id = carbon_get_post_meta($dispute->ID, "commission_request_id");
                $initiating_user_id = carbon_get_post_meta($dispute->ID, "initiating_user");
                $commission_request = get_post($commission_request_id);
                $contract_id = carbon_get_post_meta($commission_request_id, "contract_id");
                $contract_sku = carbon_get_post_meta($contract_id, "sku");
                $another_part = get_another_part_of_contract($contract_id);
                $another_part_user = get_user_another_part_of_contract($contract_id);
                $subject = carbon_get_post_meta($dispute->ID, "subject");
                $status = carbon_get_post_meta($dispute->ID, "status_history");
                $status_end = end($status);
                $last_update = $status_end["date_status"];
                $last_status = $status_end["history_status"];
                $date_string = $last_update ? "Last update: " . $admin->get_human_time_diff($last_update) : "";

                $status_class = '';
                $status_text = '';

                switch ($last_status) {
                    case 'pending':
                        $status_class = 'text-bg-primary';
                        $status_text = "Pending";
                        break;
                    case 'resolved':
                        $status_class = 'text-bg-success';
                        $status_text = "Accepted";
                        break;
                }

                if ($dispute) :
                    // Obtener los usuarios administradores
                    $users = get_users(['role' => 'administrator']);
                    $admin_id = $users[0]->ID; // Usar el ID del primer administrador como ejemplo

                    ?>
                    <tr>
                        <th scope="row"><?php echo $dispute->ID; ?></th>
                        <td><?php echo esc_html($contract_sku); ?></td>
                        <td><?php echo esc_html($another_part->post_title); ?></td>
                        <td><?php echo esc_html($subject); ?></td>
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo esc_html($status_text); ?></span></td>
                        <td><?php echo esc_html($date_string); ?></td>
                        <td>
                            <ul class="list-inline mb-0">
                               
                              
                                <?php
                                
                                $user_ids   = [$admin_id, $current_user->ID];
                                $unique_key = $contract_sku;
                                // Subject will be used only if conversation not exists yet
                                $subject    = 'Questions about your Dispute about contract: '.$contract_sku;

                                $thread_id = Better_Messages()->functions->get_unique_conversation_id( $user_ids, $unique_key, $subject );
                               
                               
                                
                                ?>
                                <li class="list-inline-item">
                                    <a class="operation" data-bs-toggle="modal" data-bs-target="#dispute-messages<?php echo $thread_id;?>">
                                        <i class="fa-solid fa-headset"></i>
                                    </a>
                                </li>

                                


                                <?php if (in_array("company", $current_user->roles) && $commission_request) : ?>
                                    <?php if($current_user->ID === $initiating_user_id  ):?>
                                                 
                                                 <form class="delete-commission-request-form d-inline">
                                                     <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete_commission_request_nonce"); ?>"/>
                                                     <input type="hidden" name="commission_request_id" value="<?php echo $commission_request->ID;?>">
                                                     <button type="submit" class="operation"><i class="fa-solid text-danger fa-trash"></i></button>
                                                 </form>
                                                 
                                    <?php endif;?>
                                 
                                    <li class="list-inline-item">
                                        <a class="btn btn-secondary btn-sm" href="<?php //echo home_url("".$contract->ID);?>">Pay</a>
                                    </li>
                                    
                                <?php endif; ?>
                                <div class="modal fade" id="dispute-messages<?php echo  $thread_id;?>" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                          
                                            <?php echo Better_Messages()->functions->get_conversation_layout( $thread_id );?>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </ul>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>


<?php $template_path = 'templates/dashboard/form-dispute.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}


?>

