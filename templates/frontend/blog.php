<?php
$query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => 5
));

if ($query->have_posts()): ?>
<div class="blog">
            <div class="container">
                <?php if ($blog_title): ?>
                    <h2 class="title"><?php echo esc_html($blog_title); ?></h2>
                <?php endif;?>
                <?php if ($blog_description): ?>
                    <div class="description"><?php echo wp_kses_post($blog_description); ?></div>
                <?php endif;?>
                <?php if ($blog_image): ?>
                    <div class="blog-image">
                        <?php echo wp_get_attachment_image($blog_image, 'full', false, ['class' => 'img-fluid']); ?>
                    </div>
                <?php endif;?>
                <div class="blog-posts">
                        <div class="row posts-list">
                            <?php while ($query->have_posts()): $query->the_post(); ?>
                                <div class="col-sm-6 col-md-4">
                                    <div class="post-item">
                                    <?php if (has_post_thumbnail()): ?>
                                        <a href="<?php the_permalink();?>">
                                       


                 
                        <?php 
                        $feature_image_url = get_the_post_thumbnail_url();
                        if ($feature_image_url) : ?>
    <div class="image" style="background-image: url('<?php echo $feature_image_url; ?>'); width: 300px; 
         height: 200px; 
         background-size: cover; 
         background-position: center; 
         background-repeat: no-repeat; 
          "></div>
<?php endif; ?>
                                            <?php get_the_post_thumbnail_url(); ?>
                                        </a>
                                    <?php endif;?>
                                    <div class="post-content">
                                        <h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h3>
                                        
                                        <!-- Agregar descripción o extracto del post -->
                                        <div class="post-description">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                        </div>

                                        <div class="post-meta d-flex">
                                            <!-- Puedes agregar más meta datos aquí si lo deseas -->
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            <?php endwhile;?>
                        </div>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
<?php endif; ?>
