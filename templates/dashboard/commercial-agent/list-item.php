<?php 
$user_id = get_post_meta($commercial_agent->ID, '_user_id',true);
                $user = get_user_by("ID",$user_id);
            
                $years_of_experience = get_post_meta($commercial_agent->ID, 'years_of_experience', true);
                $profile_image = get_the_post_thumbnail($commercial_agent->ID, "thumbnail", [
                    'class' => 'profile-image',
                ]);
                $industry_terms = wp_get_post_terms($commercial_agent->ID, 'industry');
                $selling_method_terms = wp_get_post_terms($commercial_agent->ID, 'selling_method');
                $seller_type_terms = wp_get_post_terms($commercial_agent->ID, 'seller_type');
                $location_terms = wp_get_post_terms($commercial_agent->ID, 'location');
                $language_terms = wp_get_post_terms($commercial_agent->ID, 'language');

                $industry_names = array_map(fn($term) => esc_html($term->name), $industry_terms);
                $selling_method_names = array_map(fn($term) => esc_html($term->name), $selling_method_terms);
                $seller_type_names = array_map(fn($term) => esc_html($term->name), $seller_type_terms);
                $location_names = array_map(fn($term) => esc_html($term->name), $location_terms);
                $language_names = array_map(fn($term) => esc_html($term->name), $language_terms);
                ?>
				            <div class="result-item row">
		                        <div class="col-md-3">
		                        <?php if ($profile_image): ?>
				                    <?php echo $profile_image; ?>
				                <?php endif;
                                $agent_id = $commercial_agent->ID;
                                $avg_rating_template = 'templates/avg-rating.php';
                                if (locate_template($avg_rating_template)) {
                                    include locate_template($avg_rating_template);
                                }
                                ?>
                            <a href="<?php echo home_url('/commercial-agent-item/?commercial_agent_id=' . $commercial_agent->ID); ?>" class="btn btn-primary">Detail</a>
                        </div>

                <div class="info col-md-9">
                    <div class="meta">
                        <h3 class="title"><?php echo esc_html($commercial_agent->post_title); ?></h3>
                        <?php if($user && $user->user_registered):?>
                        <p>Member since <?php echo $user->user_registered; ?></p>
                        <?php endif;?>
                        <ul class="list-inline">


                        <?php if ($industry_names): ?>
                            <li class="industry list-inline-item"><i class="fa-solid fa-list"></i><?php echo implode(', ', $industry_names); ?></li>
                        <?php endif;?>
                        <?php if ($location_names): ?>
                            <li class="location list-inline-item"><i class="fa-solid fa-location-crosshairs"></i><?php echo implode(', ', $location_names); ?></li>
                        <?php endif;?>
                        <?php if ($language_names): ?>
                            <li class="language list-inline-item"><i class="fa-solid fa-language"></i><?php echo implode(', ', $language_names); ?></li>
                        <?php endif;?>
                        </ul>
                    </div>
                    <div class="type">
                        <?php if ($selling_method_names): ?>
                            <p class="selling-methods">Selling methods: <?php echo implode(', ', $selling_method_names); ?></p>
                        <?php endif;?>
                        <?php if ($seller_type_names): ?>
                            <p class="seller-type">Seller Type: <?php echo implode(', ', $seller_type_names); ?></p>
                        <?php endif;?>
                    </div>
                </div>


            </div>