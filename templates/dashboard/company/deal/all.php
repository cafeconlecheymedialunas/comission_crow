<?php

$company = Company::get_instance();
$company_post = $company->get_company();

$deals = $company->get_deals();



?>
<div class="card mb-4">
   
	<h2 class="mb-0"><?php echo __("All Deals"); ?></h2>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        Add new
    </button>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Opportunity</th>
                            <th scope="col">Commercial agent</th>
                            <th scope="col">Commission</th>
                            <th scope="col">Minimal Price</th>
                            <th scope="col">Status</th>
                            <th scope="col">Date</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deals)) :
                            foreach ($deals as $deal) :
                            
                                $commercial_agent = carbon_get_post_meta($deal->ID, 'commercial_agent');
                                $commercial_agent = get_post($commercial_agent);
                                
                                $opportunity = carbon_get_post_meta($deal->ID, 'opportunity');
                                $opportunity = get_post($opportunity);

                                $date = carbon_get_post_meta($deal->ID, 'date');
                                $commission = carbon_get_post_meta($deal->ID, 'commission');
                                $minimal_price = carbon_get_post_meta($deal->ID, 'minimal_price');
                                $status = carbon_get_post_meta($deal->ID, 'status');
                                //$industry = wp_get_post_terms($deal->ID, 'industry', ['fields' => 'names']);

                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $deal->ID; ?></th>
                                        <td><?php echo get_the_title($opportunity->ID); ?></td>
                                        <td><?php echo  get_the_title($commercial_agent->ID);  ?></td>
                                        <td><?php echo esc_html($commission); ?></td>
                                        <td><?php echo esc_html($minimal_price); ?></td>
                                        <td><?php echo esc_html($status); ?></td>
                                        
                                        <td><?php echo esc_html($date); ?></td>
                                        <td>
                                            <ul class="list-inline mb-0">
                                                <?php if($status === "requested"):?>
                                                <li class="list-inline-item">     
                                                    <form class="delete-deal-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete-deal-nonce"); ?>"/>
                                                        <input type="hidden" name="deal_id" value="<?php echo esc_attr($deal->ID); ?>">
                                                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                                    </form>
    
                                                </li>
                                                <li class="list-inline-item">  
                                                    <form class="delete-deal-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete-deal-nonce"); ?>"/>
                                                        <input type="hidden" name="deal_id" value="<?php echo esc_attr($deal->ID); ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Refuse</button>
                                                    </form>
                                                </li>
                                                <?php endif;?>
                                                <?php if($status === "accepted"):?>
                                                    <li class="list-inline-item"> 
                                                        <form class="delete-deal-form">
                                                            <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete-deal-nonce"); ?>"/>
                                                            <input type="hidden" name="deal_id" value="<?php echo esc_attr($deal->ID); ?>">
                                                            <button type="submit" class="btn btn-warning btn-sm">Finished</button>
                                                        </form>
                                                    </li>
                                                <?php endif;?>
                                            </ul>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add a proposal Deal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <?php $template_path = 'templates/dashboard/form-deal.php';
        if (locate_template($template_path)) {
            include locate_template($template_path);
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?php wp_reset_postdata(); ?>