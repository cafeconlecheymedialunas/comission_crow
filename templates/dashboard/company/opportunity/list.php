<?php

$company = Company::get_instance();
$company_post = $company->get_company();
$opportunities = $company->get_opportunities();
$statuses = $admin->get_statuses();



?>
<div class="card mb-4">
	<h2 class="mb-0"><?php echo __("Opportunities"); ?><a href="<?php echo home_url("/dashboard/company/opportunity/create"); ?>">Add new</a></h2>
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
                            <th scope="col">Sector</th>
                            <th scope="col">Price</th>
                            <th scope="col">Commission</th>
                            <th scope="col">Location</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($opportunities)) :
                            foreach ($opportunities as $opportunity) :
                                $sector = carbon_get_post_meta($opportunity->ID, 'sector');
                                $sector = get_term_by('id', $sector, 'sector');
                                $price = carbon_get_post_meta($opportunity->ID, 'price');
                                $commission = carbon_get_post_meta($opportunity->ID, 'commission');
                                $location = carbon_get_post_meta($opportunity->ID, 'location');
                                $location = get_term_by('id', $location, 'country');
                                ?>
                                    <tr>
                                        <th scope="row"></th>
                                        <td><?php echo get_the_title($opportunity->ID); ?></td>
                                        <td><?php echo esc_html($sector->name); ?></td>
                                        <td><?php echo esc_html($price); ?></td>
                                        <td><?php echo esc_html($commission); ?></td>
                                        <td><?php echo esc_html($location->name); ?></td>
                                        <td>
                                        <td>
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item"><a href="<?php echo home_url("/dashboard/company/opportunity/edit"). "?opportunity_id=". esc_attr($opportunity->ID); ?>"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                <li class="list-inline-item">
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#delete<?php echo esc_attr($opportunity->ID); ?>">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>     
                                                    <form class="delete-opportunity-form">
                                                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("delete-opportunity-nonce"); ?>"/>
                                                        <input type="hidden" name="opportunity_id" value="<?php echo esc_attr($opportunity->ID); ?>">
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
