<?php

$features = carbon_get_post_meta(get_the_ID(), 'features');
?>
<!-- Features Section -->
<?php if (!empty($features)): ?>
    <div class="features">
        <div class="container">
            <div class="row">
                <?php foreach ($features as $feature): ?>
                    <?php
$feature_title = isset($feature['feature_title']) ? $feature['feature_title'] : '';
$feature_description = isset($feature['feature_description']) ? $feature['feature_description'] : '';
$feature_image = isset($feature['feature_image']) ? $feature['feature_image'] : '';

                    if ($feature_title || $feature_description || $feature_image): ?>
                        <div class="col-md-4">
                            <?php
if ($feature_image) {
    echo wp_get_attachment_image($feature_image, 'full', false, ['class' => 'img-fluid']);
}
?>
                            <?php if ($feature_title): ?>
                                <h3 class="title"><?php echo esc_html($feature_title); ?></h3>
                            <?php endif;?>
                            <?php if ($feature_description): ?>
                                <div class="description"><?php echo wp_kses_post($feature_description); ?></div>
                            <?php endif;?>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
            </div>
        </div>
    </div>
<?php endif;?>