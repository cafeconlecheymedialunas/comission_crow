 <!-- Hero Section -->
 <?php if ($hero_title || $hero_description || $hero_image): ?>
        <div class="section hero">
            <div class="container">
                <div class="row">
                    <?php if ($hero_title || $hero_description): ?>
                        <div class="col-md-6 d-flex flex-column justify-content-center">
                            <?php if ($hero_title): ?>
                                <h1 class="title"><?php echo esc_html($hero_title); ?></h1>
                            <?php endif;?>
                            <?php if ($hero_description): ?>
                                <div class="description">
                                    <?php echo wp_kses_post($hero_description); ?>
                                </div>
                            <?php endif;?>
                            <div class="brands_form">
                            <div class="prolancer-select-search">
                                <select name="post_type" class="form-select">
                                    <option value="commercial_agent">Commercial Agents</option>
                                    <option value="opportunity" selected>Opportunities</option>
                                </select>
                                <a id="view-button" class="btn btn-primary" href="#">View</a>
                            </div>
                            </div>
                        </div>
                    <?php endif;?>
                    <?php if ($hero_image): ?>
                        <div class="col-md-6">
                            <?php echo wp_get_attachment_image($hero_image, 'full', false, ['class' => 'img-fluid']); ?>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php endif;?>