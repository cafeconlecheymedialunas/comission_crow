<?php

$company = Company::get_instance();
$company_post = $company->get_company();
$opportunities = $company->get_opportunities();




?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
	<h2 class="mb-0 d-inline"><?php echo __("Opportunities"); ?></h2>
    <a class="btn btn-primary" href="<?php echo home_url("/dashboard/company/opportunity/create"); ?>">Add new</a>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table custom-table " id="opportunity-table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Opportunity</th>
                            <th scope="col">Industry</th>
                            <th scope="col">Price</th>
                            <th scope="col">Commission</th>
                            <th scope="col">Country</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($opportunities)) :
                            foreach ($opportunities as $opportunity) :
                            
                                $price = carbon_get_post_meta($opportunity->ID, 'price');
                                $commission = carbon_get_post_meta($opportunity->ID, 'commission');
                              
                                $industry = wp_get_post_terms($opportunity->ID, 'industry', ['fields' => 'names']);

                                $country = wp_get_post_terms($opportunity->ID, 'country', ['fields' => 'names']);

                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $opportunity->ID; ?></th>
                                        <td><?php echo get_the_title($opportunity->ID); ?></td>
                                        <td><?php echo esc_html($industry[0]); ?></td>
                                        <td><?php echo esc_html($price); ?></td>
                                        <td><?php echo esc_html($commission); ?></td>
                                        <td><?php echo esc_html($country[0]); ?></td>
                                        
                                        <td>
                                            <div class="mb-0 d-flex justify-content-center align-items-center">
                                                    <a class="operation" href="<?php echo home_url("/dashboard/company/opportunity/edit"). "?opportunity_id=". esc_attr($opportunity->ID); ?>"><i class="text-primary fa-regular fa-pen-to-square"></i></a>
                                             
                                                    <a class="operation" href="<?php echo home_url("/dashboard/company/opportunity/view"). "?opportunity_id=". esc_attr($opportunity->ID); ?>"><i class="text-secondary text-secondary view fa-regular fa-eye"></i></a>
                                                
                                                 
                                                    <form class="delete-opportunity-form d-inline">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete-opportunity-nonce"); ?>"/>
                                                        <input type="hidden" name="opportunity_id" value="<?php echo esc_attr($opportunity->ID); ?>">
                                                        <button type="submit" class="operation"><i class="fa-solid text-danger fa-trash"></i></button>
                                                    </form>
   
                                                
                                            </div>
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
