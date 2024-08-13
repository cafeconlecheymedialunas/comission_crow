 <!-- Opportunities Section -->
 <?php if ($opportunities_title || $opportunities_description || !empty($opportunities_select)): ?>
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
                        $company_logo = get_the_post_thumbnail($company_id, [70, 70], ['class' => 'rounded-circle']);
                    ?>
		                        <div class="opportunity container mb-4 py-4 mx-auto d-flex justify-content-between align-items-center border">
		                            <?php if ($company_logo): ?>
		                                <?php echo $company_logo; ?>
		                            <?php endif;?>
                            <div class="content">
                                <?php if ($post->post_title): ?>
                                    <h3 class="title mb-2"><?php echo esc_html($post->post_title); ?></h3>
                                <?php endif;?>
                                <?php if ($sales_cycle_estimation): ?>
                                    <p class="sales-cycle mb-0"><i class="fa-solid fa-stopwatch-20"></i> Sales Cycle: <?php echo esc_html($sales_cycle_estimation); ?> days</p>
                                <?php endif;?>
                            </div>
                            <div class="price">
                                <?php if ($price): ?>
                                    <p class="price mb-2"><?php echo esc_html(Helper::format_price($price)); ?></p>
                                <?php endif;?>
                                <?php if ($commission): ?>
                                    <p class="commission mb-0"><?php echo esc_html($commission); ?>%</p>
                                <?php endif;?>
                            </div>
                            <button>Detail</button>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>
    <?php endif;?>