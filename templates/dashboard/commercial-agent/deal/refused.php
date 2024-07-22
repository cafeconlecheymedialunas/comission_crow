<?php

$commercial_agent = CommercialAgent::get_instance();
$commercial_agent_post = $commercial_agent->get_commercial_agent();

$deals = $commercial_agent->get_deals("refused");

?>
<div class="card mb-4">
	<h2 class="mb-0"><?php echo __("Refused Deals"); ?><a href="<?php echo home_url("/dashboard/commercial_agent/deal/create"); ?>">Add new</a></h2>
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
                            <th scope="col">Company</th>
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
                            
                                $company = carbon_get_post_meta($deal->ID, 'company');
                                $company = get_post($company);
                                
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
                                        <td><?php echo  get_the_title($company->ID);  ?></td>
                                        <td><?php echo esc_html($commission); ?></td>
                                        <td><?php echo esc_html($minimal_price); ?></td>
                                        <td><?php echo esc_html($status); ?></td>
                                        
                                        <td><?php echo esc_html($date); ?></td>
                                        <td>
                                        <td>
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item"><a href="<?php echo home_url("/dashboard/commercial_agent/deal/edit"). "?deal_id=". esc_attr($deal->ID); ?>"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                <li class="list-inline-item">
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#delete<?php echo esc_attr($deal->ID); ?>">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>     
                                                    <form class="delete-deal-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete-deal-nonce"); ?>"/>
                                                        <input type="hidden" name="deal_id" value="<?php echo esc_attr($deal->ID); ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                    </form>
   
                                                </li>
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

<?php wp_reset_postdata(); ?>