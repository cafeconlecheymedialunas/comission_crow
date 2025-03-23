   <!-- Brands Section -->
   <?php if (!empty($brands)): ?>
        <div class="brands">
         
           
                <section id="slider">
                    <div class="slider-container">
                        <?php foreach ($brands as $brand):
                       

                            $cover_image_url = wp_get_attachment_image_url($brand["brand_image"]);


                            ?>
		                            <div class="slider-item">
		                                <?php if ($cover_image_url): ?>
		                                    <img src="<?php echo esc_url($cover_image_url); ?>">
		                                <?php endif;?>
                                <?php if ($brand["brand_title"]): ?>
                                    <figcaption><?php echo esc_html($brand["brand_title"]); ?></figcaption>
                                <?php endif;?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </section>
         
        </div>
    <?php endif;?>