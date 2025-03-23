<div id="prolancer_seller_details-1" class="widget widget_prolancer_seller_details">
    <h4 class="widget-title">About Me</h4>

    <?php
    // Prepare the values with default handling
    $selling_method_name = isset($selling_method[0]->name) ? $selling_method[0]->name : null;
    $seller_type_name = isset($seller_type[0]->name) ? $seller_type[0]->name : null;
    $location_name = isset($location[0]->name) ? $location[0]->name : null;
    $industry_name = isset($industry[0]->name) ? $industry[0]->name : null;
    $years_of_experience = isset($years_of_experience) ? $years_of_experience : null;
    ?>

    <?php if ($selling_method_name): ?>
    <div class="seller-detail d-flex">
        <div class="icon">
            <i class="fa-solid fa-shrimp"></i>
        </div>
        <div>
            <h5>Selling Methods</h5>
            <p><?php echo htmlspecialchars($selling_method_name); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($seller_type_name): ?>
    <div class="seller-detail d-flex">
        <div class="icon">
            <i class="fa fa-user-shield"></i>
        </div>
        <div>
            <h5>Seller Type</h5>
            <p><?php echo htmlspecialchars($seller_type_name); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($location_name): ?>
    <div class="seller-detail d-flex">
        <div class="icon">
            <i class="fa fa-compass"></i>
        </div>
        <div>
            <h5>Location</h5>
            <p><?php echo htmlspecialchars($location_name); ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($industry_name): ?>
    <div class="seller-detail d-flex">
        <div class="icon">
            <i class="fa fa-industry"></i>
        </div>
        <div>
            <h5>Industry</h5>
            <p><?php echo htmlspecialchars($industry_name); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($years_of_experience): ?>
    <div class="seller-detail d-flex">
        <div class="icon">
            <i class="fa fa-calendar"></i>
        </div>
        <div>
            <h5>Years of Experience</h5>
            <p><?php echo htmlspecialchars($years_of_experience); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($skills) && is_array($skills) && !empty($skills)): ?>
    <div class="seller-skills mt-5">
        <h4 class="text-center">Skills</h4>
        <div class="skill-item">
            <?php foreach($skills as $skill): ?>
                <?php if (isset($skill->name)): ?>
                    <span class="badge badge-primary" style="background-color:#4a6375"><?php echo htmlspecialchars($skill->name); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
