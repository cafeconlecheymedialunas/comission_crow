<?php 
$company_id = carbon_get_post_meta($contract_id, "company");
             
             $company_logo = get_the_post_thumbnail($company_id, [50, 50], [
                 'class' => 'rounded-circle company-logo',
                 'width' => '100',
                 'height' => '100',
             ]);

             $industry_terms = wp_get_post_terms($opportunity_id, "industry");
             $industry_names = array_map(fn($term) => esc_html($term->name), $industry_terms);

             $language_terms = wp_get_post_terms($opportunity_id, "language");
             $language_names = array_map(fn($term) => esc_html($term->name), $language_terms);
             
             $location_terms = wp_get_post_terms($opportunity_id, "location");
             $location_names = array_map(fn($term) => esc_html($term->name), $location_terms);


             $target_audience_terms = wp_get_post_terms($opportunity_id, "target_audience");
             $target_audience_names = array_map(fn($term) => esc_html($term->name), $target_audience_terms);
             
             $price = carbon_get_post_meta($opportunity_id, "price");
             $commission_value = carbon_get_post_meta($opportunity_id, "commission");
 
             ?>
             <div class="result-item align-items-center">
          
                 <?php if ($company_logo): ?>
                     <?php echo $company_logo; ?>
                 <?php endif;?>
              
          
                     <div class="detail">
                         <h3 class="title"><?php the_title();?></h3>
                         <ul class="list-inline">
                         <?php if ($industry_names): ?>
                             <li class="list-inline-item industry"><i class="fa-solid fa-list"></i><?php echo implode(', ', $industry_names); ?></li>
                         <?php endif;?>
                         <?php if ($target_audience_names): ?>
                             <li class="list-inline-item target-audience"><i class="fa-solid fa-location-crosshairs"></i><?php echo implode(', ', $target_audience_names); ?></li>
                         <?php endif;?>

                         <?php if ($location_names): ?>
                             <li class="list-inline-item location"><i class="fa-solid fa-location-crosshairs"></i> <?php echo implode(', ', $location_names); ?></li>
                         <?php endif;?>
                         <?php if ($language_names): ?>
                             <li class="list-inline-item language"><i class="fa-solid fa-language"></i><?php echo implode(', ', $language_names); ?></li>
                         <?php endif;?>
                         </ul>
                       
                     </div>
              
              
                     <div class="pricing">
                         <?php if ($price !== null): ?>
                             <h5 class="price"><?php echo Helper::convert_price_to_selected_currency($price); ?></h5>
                         <?php endif;?>
                         <?php if ($commission_value !== null): ?>
                             <p class="commissions">Commission: <?php echo esc_html($commission_value); ?> %</p>
                         <?php endif;?>
                     </div>
           
                     <a href="<?php echo home_url() . "/opportunity-item/?opportunity_id=" . get_the_ID(); ?>" class="btn btn-primary">Detail</a>
               
                
             </div>