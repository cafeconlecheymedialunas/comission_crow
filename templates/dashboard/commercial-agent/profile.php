<?php

$current_user = wp_get_current_user();

$language_terms = get_terms([
    "taxonomy" => "language",
    "hide_empty" => false,
]);

$country_terms = get_terms([
    "taxonomy" => "country",
    "hide_empty" => false,
]);
$industry_terms = get_terms([
    "taxonomy" => "industry",
    "hide_empty" => false,
]);

$skill_terms = get_terms([
    "taxonomy" => "skill",
    "hide_empty" => false,
]);

$selling_method_terms = get_terms([
    "taxonomy" => "selling_method",
    "hide_empty" => false,
]);

$seller_type_terms = get_terms([
    "taxonomy" => "seller_type",
    "hide_empty" => false,
]);
$commercial_agent = CommercialAgent::get_instance();
$commercial_agent_post = $commercial_agent->get_commercial_agent();


$profile_image = isset($commercial_agent_post) ? [get_post_thumbnail_id($commercial_agent_post->ID)] : '';
$selected_years_of_experience = isset($commercial_agent_post) ? carbon_get_post_meta($commercial_agent_post->ID, 'years_of_experience') : '';

$selected_languages = wp_get_post_terms($commercial_agent_post->ID, 'language', ['fields' => 'ids']);
$selected_country = wp_get_post_terms($commercial_agent_post->ID, 'country', ['fields' => 'ids']);
$selected_skills = wp_get_post_terms($commercial_agent_post->ID, 'skill', ['fields' => 'ids']);
$selected_industry = wp_get_post_terms($commercial_agent_post->ID, 'industry', ['fields' => 'ids']);
$selected_selling_method = wp_get_post_terms($commercial_agent_post->ID, 'selling_method', ['fields' => 'ids']);
$selected_seller_type = wp_get_post_terms($commercial_agent_post->ID, 'seller_type', ['fields' => 'ids']);



?>
<div class="row">
	<div class="col-md-8">
	    <div class="card">
            <h2>Commercial Agent</h2>
            <form id="agent-profile-form">
                <div class="row">
                    <!-- User Fields -->
                    <div class="col-md-12">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" id="profile_image" class="form-control" name="profile_image">
                        <div class="error-message"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <div class="editor-container" data-target="description"></div>
                        <input type="hidden" id="description" name="description" value="<?php echo isset($commercial_agent_post) ? $commercial_agent_post->post_content: ""; ?>">
                        <div class="error-message"></div>
                    </div>
                    
                    <?php if($language_terms):?>
                    <div class="col-md-6">
                        <label for="language" class="form-label">Languages:</label>
                        <select name="language[]" multiple class="form-select">
                            <?php foreach ($language_terms as $term): ?>
                                <option 
                                    value="<?php echo esc_attr($term->term_id); ?>" 
                                    <?php echo in_array($term->term_id, $selected_languages) ? 'selected' : ''; ?>
                                    >
                                    <?php echo esc_html($term->name); ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                        <div class="error-message"></div>
                    </div>
                    <?php endif;?>
                    <?php if($country_terms):?>
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country:</label>
                            <select name="country[]" class="form-select">
                                <option value="">Select an option</option>
                                <?php foreach ($country_terms as $term): ?>
                                    <option 
                                        value="<?php echo esc_attr($term->term_id); ?>" 
                                        <?php selected($selected_country[0], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                    <?php endif;?>
                    <?php if($skill_terms):?>
                    <div class="col-md-6">
                        <label for="skill" class="form-label">Skills:</label>
                        <select name="skill[]" multiple class="form-select">
                            <?php foreach ($skill_terms as $term): ?>
                                <option 
                                    value="<?php echo esc_attr($term->term_id); ?>" 
                                    <?php echo in_array($term->term_id, $selected_skills) ? 'selected' : ''; ?>
                                    >
                                    <?php echo esc_html($term->name); ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                        <div class="error-message"></div>
                    </div>
                    <?php endif;?>
                    <?php if($industry_terms):?>
                        <div class="col-md-6">
                            <label for="industry" class="form-label">Industry:</label>
                            <select name="industry[]" class="form-select">
                                <option value="">Select an option</option>
                                <?php foreach ($industry_terms as $term): ?>
                                    <option 
                                        value="<?php echo esc_attr($term->term_id); ?>" 
                                        <?php selected($selected_industry[0], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                    <?php endif;?>
                    <?php if($seller_type_terms):?>
                        <div class="col-md-6">
                            <label for="seller_type" class="form-label">Seller Type:</label>
                            <select name="seller_type[]" class="form-select">
                                <option value="">Select an option</option>
                                <?php foreach ($seller_type_terms as $term): ?>
                                    <option 
                                        value="<?php echo esc_attr($term->term_id); ?>" 
                                        <?php selected($selected_seller_type[0], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                    <?php endif;?>
                    <?php if($selling_method_terms):?>
                        <div class="col-md-6">
                            <label for="selling_method[]" class="form-label">Selling Methods:</label>
                            <select name="selling_method[]" class="form-select">
                                <option value="">Select an option</option>
                                <?php foreach ($selling_method_terms as $term): ?>
                                    <option 
                                        value="<?php echo esc_attr($term->term_id); ?>" 
                                        <?php selected($selected_selling_method[0], $term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                    <?php endif;?>
                    <div class="col-md-6">
                        <label for="years_of_experience">Years of Experience</label>
                        <input type="text" name="years_of_experience" class="form-control" value="<?php echo esc_attr($selected_years_of_experience); ?>" placeholder="Years of Experience">
                        <div class="error-message"></div>
                    </div>
                </div>
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('update-profile-nonce'); ?>"/>
                <input type="hidden" name="commercial_agent_id" value="<?php echo $commercial_agent_post->ID;?>">
                <div class="alert alert-danger general-errors" role="alert" style="display:none;"></div>
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
</div>
