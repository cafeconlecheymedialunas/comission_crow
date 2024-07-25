<?php

$current_user = wp_get_current_user();


$industry_terms = get_terms([
    "taxonomy" => "industry",
    "hide_empty" => false,
]);
$activity_terms = get_terms([
    "taxonomy" => "activity",
    "hide_empty" => false,
]);
$type_of_company_terms = get_terms([
    "taxonomy" => "type_of_company",
    "hide_empty" => false,
]);

$country_terms = get_terms([
    "taxonomy" => "country",
    "hide_empty" => false,
]);


$company = Company::get_instance();
$company_post = $company->get_company();

$profile_image = isset($company_post) ? [get_post_thumbnail_id($company_post->ID)] : '';

$industry = wp_get_post_terms($company_post->ID, 'industry', ['fields' => 'ids']);
$activity = wp_get_post_terms($company_post->ID, 'activity', ['fields' => 'ids']);
$type_of_company = wp_get_post_terms($company_post->ID, 'type_of_company', ['fields' => 'ids']);
$country = wp_get_post_terms($company_post->ID, 'country', ['fields' => 'ids']);

$company_name = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'company_name') : '';
$employees_number = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'employees_number') : '';
$website_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'website_url') : '';
$facebook_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'facebook_url') : '';
$instagram_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'instagram_url') : '';
$twitter_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'twitter_url') : '';
$linkedin_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'linkedin_url') : '';
$tiktok_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'tiktok_url') : '';
$youtube_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'youtube_url') : '';

echo do_shortcode('[fullstripe_form name="Prueba" type="inline_save_card"]');


?>
<div class="row">
	<div class="col-md-8">
	    <div class="card">
                <h2>Company</h2>
            <form id="company-profile-form">
                <div class="row">
                    <!-- User Fields -->
                    <div class="col-md-6">
                        <label for="company_name">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="<?php echo esc_attr($company_name); ?>" placeholder="Company Name">
                    </div>
                    <div class="col-md-6">
                        <label for="image-ids" class="form-label">Company Logo:</label>
                        <input type="hidden" id="image-ids" value="<?php echo $profile_image;?>" name="company_logo" class="regular-text media-ids">
                        <button type="button" id="select-image-button" class="button select-media-button btn btn-secondary" data-media-type="image" data-multiple="false">Select Image</button>
                        <div class="image-preview row" style="<?php echo (empty($profile_image))?'display:none;':'';?>">
                            <?php
                            if($profile_image):
                                foreach($profile_image as $image):
                                    // Obtener metadatos de la imagen
                                    $attachment_metadata = wp_get_attachment_metadata($image);
                                    // Obtener la URL de la imagen
                                    $image_url = wp_get_attachment_url($image);
                                    if($image_url):?>
                            
                                    <div class="col-2 col-sm-3 col-md-4 preview-item d-flex flex-column justify-content-center align-items-center">
                                        <img width="100" src="<?php echo esc_url($image_url); ?>" style="max-width: 100%; height: auto;">
                                    </div>
                                    
                            <?php
                                    endif;
                                endforeach;
                            endif;
?>
                        </div>
                    </div>
                 
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <div class="editor-container" data-target="description"></div>
                        <input type="hidden" id="description" name="description" value="<?php echo isset($company_post) ? $company_post->post_content : ""; ?>">
                    </div>
                    
                    <?php if($industry_terms):?>
                    <div class="col-md-6">
                        <label for="industry" class="form-label">Industry:</label>
                        <select name="industry[]" class="custom-select">
                            <?php foreach ($industry_terms as $term): ?>
                                <option 
                                    value="<?php echo esc_attr($term->term_id); ?>" 
                                    <?php echo in_array($term->term_id, $industry) ? 'selected' : ''; ?>
                                    >
                                    <?php echo esc_html($term->name); ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <?php endif;?>
                    <?php if($country_terms):?>
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country:</label>
                            <select name="country[]" class="custom-select">
                                <option value="">Select an option</option>
                                <?php foreach ($country_terms as $term): ?>
                                    <option 
                                        value="<?php echo esc_attr($term->term_id); ?>" 
                                        <?php selected($country[0], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    <?php endif;?>
                    <?php if($activity_terms):?>
                    <div class="col-md-6">
                        <label for="activity" class="form-label">Activity:</label>
                        <select name="activity[]"class="custom-select">
                            <?php foreach ($activity_terms as $term): ?>
                                <option 
                                        value="<?php echo esc_attr($term->term_id); ?>" 
                                        <?php selected($activity[0], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <?php endif;?>
                  
                    <?php if($type_of_company_terms):?>
                        <div class="col-md-6">
                            <label for="type_of_company" class="form-label">Company Type:</label>
                            <select name="type_of_company[]" class="custom-select">
                                <option value="">Select an option</option>
                                <?php foreach ($type_of_company_terms as $term): ?>
                                    <option 
                                        value="<?php echo esc_attr($term->term_id); ?>" 
                                        <?php selected($type_of_company[0], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    <?php endif;?>
                 
                    <div class="col-md-6">
                        <label for="employees_number">Number of employees</label>
                        <input type="text" name="employees_number" class="form-control" value="<?php echo esc_attr($employees_number); ?>" placeholder="employees_number">
                    </div>
                    <div class="col-md-6">
                        <label for="website">Website</label>
                        <input type="url" name="website_url" class="form-control" value="<?php echo esc_attr($website_url); ?>" placeholder="Website">
                    </div>
                    <div class="col-md-6">
                        <label for="facebook">Facebook</label>
                        <input type="url" name="facebook_url" class="form-control" value="<?php echo esc_attr($facebook_url); ?>" placeholder="Facebook">
                    </div>
                    <div class="col-md-6">
                        <label for="instagram">Instagram</label>
                        <input type="url" name="instagram_url" class="form-control" value="<?php echo esc_attr($instagram_url); ?>" placeholder="Instagram">
                    </div>
                    <div class="col-md-6">
                        <label for="twitter">Twitter</label>
                        <input type="url" name="twitter_url" class="form-control" value="<?php echo esc_attr($twitter_url); ?>" placeholder="Twitter">
                    </div>
                    <div class="col-md-6">
                        <label for="linkedin">Linkedin</label>
                        <input type="url" name="linkedin_url" class="form-control" value="<?php echo esc_attr($linkedin_url); ?>" placeholder="Linkedin">
                    </div>
                    <div class="col-md-6">
                        <label for="tiktok">Tik Tok</label>
                        <input type="url" name="tiktok_url" class="form-control" value="<?php echo esc_attr($tiktok_url); ?>" placeholder="Tik Tok">
                    </div>

                    <div class="col-md-6">
                        <label for="youtube">You Tube</label>
                        <input type="url" name="youtube_url" class="form-control" value="<?php echo esc_attr($youtube_url); ?>" placeholder="You Tube">
                    </div>
                </div>
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('update-profile-nonce'); ?>"/>
                <input type="hidden" name="company_id" value="<?php echo $company_post->ID;?>">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
            <div id="profile-update-message"></div>
            </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <?php include get_template_directory() .
                "/templates/dashboard/form-password.php"; ?>
        </div>
    </div>
    <?php comments_template( '', true ); ?>
</div>
