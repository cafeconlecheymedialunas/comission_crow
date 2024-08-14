  <!-- Industry Section -->
  <?php if (!empty($industry_select)): ?>
        <div class="industry d-flex flex-column align-items-center">
            <?php if ($industry_title): ?>
                <h2 class="title"><?php echo esc_html($industry_title); ?></h2>
            <?php endif;?>
            <?php if ($industry_description): ?>
                <div class="description"><?php echo wp_kses_post($industry_description); ?></div>
            <?php endif;?>
            <?php if (!empty($industry_select)): ?>
                <section id="slider">
                    <div class="slider-container">
                        <?php foreach ($industry_select as $industry):
                        $term = get_term_by('id', $industry['id'], 'industry');
                        $cover_image = carbon_get_term_meta($industry["id"], "cover_image");

                        $cover_image_url = wp_get_attachment_image_url($cover_image);

                        ?>
		                            <div class="slider-item position-relative">
		                                <?php if ($cover_image_url): ?>
		                                    <img src="<?php echo esc_url($cover_image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
		                                <?php endif;?>
                                <?php if ($term->name): ?>
                                    <figcaption><?php echo esc_html($term->name); ?></figcaption>
                                <?php endif;?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </section>
            <?php endif;?>
        </div>
    <?php endif;?>