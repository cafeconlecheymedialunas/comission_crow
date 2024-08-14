 <!-- Opportunities Section -->
 <?php if (!empty($opportunities_select)): ?>
        <div class="opportunities d-flex flex-column align-items-center">
            <?php if ($opportunities_title): ?>
                <h2 class="title"><?php echo esc_html($opportunities_title); ?></h2>
            <?php endif;?>
            <?php if ($opportunities_description): ?>
                <p class="description"><?php echo wp_kses_post($opportunities_description); ?></p>
            <?php endif;?>
            <div class="container">
                <?php if (!empty($opportunities_select)): ?>
                    <?php foreach ($opportunities_select as $opportunity):
                        $post = get_post($opportunity['id']);
                        $company_id = carbon_get_post_meta($post->ID, 'company');
                        $sales_cycle_estimation = carbon_get_post_meta($post->ID, 'sales_cycle_estimation');
                        $commission = carbon_get_post_meta($post->ID, 'commission');
                        $price = carbon_get_post_meta($post->ID, 'price');
                        $company_logo = get_the_post_thumbnail($company_id, [200, 200], ['class' => 'rounded-circle']);
                    ?>
		                        <div class="opportunity row">
                                    <div class="col-md-1">
		                            <?php if ($company_logo): ?>
		                                <?php echo $company_logo; ?>
		                            <?php endif;?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="content">
                                            <?php if ($post->post_title): ?>
                                                <h3 class="title mb-2"><?php echo esc_html($post->post_title); ?></h3>
                                            <?php endif;?>
                                            <?php if ($sales_cycle_estimation): ?>
                                                <p class="sales-cycle"><i class="fa-solid fa-stopwatch-20"></i> Sales Cycle: <?php echo esc_html($sales_cycle_estimation); ?> days</p>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="pricing">
                                            <?php if ($price): ?>
                                                <p class="price"><?php echo esc_html(Helper::format_price($price)); ?></p>
                                            <?php endif;?>
                                            <?php if ($commission): ?>
                                                <p class="commission">Commission: <?php echo esc_html($commission); ?>%</p>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <button class="btn btn-info">Detail</button>
                                    </div>
                           
                           
                         
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>
    <?php endif;?>