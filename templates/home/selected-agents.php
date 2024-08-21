<!-- Selected Agents Section -->
<?php if (!empty($selected_agents) && is_array($selected_agents)): ?>
    <div class="selected-agents position-relative d-flex flex-column align-items-center">
        <?php if ($selected_agents_title): ?>
            <h2 class="title"><?php echo esc_html($selected_agents_title); ?></h2>
        <?php endif;?>
        
        <?php if ($selected_agents_description): ?>
            <p class="description"><?php echo wp_kses_post($selected_agents_description); ?></p>
        <?php endif;?>
        
        <div class="container">
            <div class="row justify-content-center">
                <?php foreach ($selected_agents as $agent):?>
                    <?php
        
            
                        $agent_post = get_post($agent_id);
                        
                        $cover_image = get_the_post_thumbnail_url($agent_id);
                        
                
                    ?>
                            <div class="agent border col-md-6 col-lg-3">
                            
                                <?php if ($cover_image): ?>
                                    <img src="<?php echo esc_url($cover_image); ?>" class="rounded-circle" alt="">
                                <?php endif; ?>
                                
                                <div class="content">
                                    <?php if ($agent_post->post_title): ?>
                                        <h3 class="title mb-2"><?php echo esc_html($agent_post->post_title); ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $avg_rating_template = 'templates/avg-rating.php';
                                    if (locate_template($avg_rating_template)) {
                                        include locate_template($avg_rating_template);
                                    }
                                    ?>
                                </div>
                            </div>

                <?php endforeach; ?>
            </div>
        </div>
        
        <?php
        $overlay = wp_get_attachment_url(2952);
        if ($overlay): ?>
            <div class="elementor-background-overlay position-absolute" style="background-image: url('<?php echo esc_url($overlay); ?>');"></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
