<?php if(!empty($hero_button_title) || !empty($hero_button_description)): ?>
    <div class="container">
        <div class="hero-button">
            <div class="row">
                <div class="col-md-8">
                    <?php if(!empty($hero_button_title)): ?>
                        <h2><?php echo $hero_button_title; ?></h2>
                    <?php endif; ?>
                    
                    <?php if(!empty($hero_button_description)): ?>
                        <div class="hero-button-description">
                            <?php echo $hero_button_description; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($hero_button_button_text)): ?>
                        <a class="btn btn-primary"><?php echo $hero_button_button_text; ?></a>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <?php if(!empty($hero_button_image)): ?>
                        <?php echo $hero_button_image; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
