<?php
$languages = $admin->get_languages();
$countries = $admin->get_countries();
$current_user = wp_get_current_user();

$industry_terms = get_terms([
    'taxonomy' => 'industry',
    'hide_empty' => false,
]);

$skill_terms = get_terms([
    'taxonomy' => 'skill',
    'hide_empty' => false,
]);

$selling_method_terms = get_terms([
    'taxonomy' => 'selling_method',
    'hide_empty' => false,
]);
?>

<form id="agent-profile-form">
    <div class="row">
        <!-- User Fields -->
        <div class="col-md-6">
            <input type="text" name="first_name" class="form-control" value="<?php echo esc_attr($current_user->first_name); ?>" placeholder="First Name">
        </div>
        <div class="col-md-6">
            <input type="text" name="last_name" class="form-control" value="<?php echo esc_attr($current_user->last_name); ?>" placeholder="Last Name">
        </div>
        <div class="col-md-6">
            <input type="email" name="user_email" class="form-control" value="<?php echo esc_attr($current_user->user_email); ?>" placeholder="User Email">
        </div>

        <div class="col-md-12">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="5"><?php echo esc_html(get_user_meta($current_user->ID, 'description', true)); ?></textarea>
        </div>

        <div class="col-md-6">
            <label>Languages</label>
            <select name="language[]" class="form-control custom-select" multiple>
                <option value="">Select a language</option>
                <?php
                $language = carbon_get_user_meta($current_user->ID, 'language') ?? [];
                foreach ($languages as $key => $value) {
                    echo '<option value="' . esc_attr($key) . '"' . (in_array($key, $language) ? ' selected' : '') . '>' . esc_html($value) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>Location</label>
            <select name="location" class="form-control custom-select">
                <option value="">Select a country</option>
                <?php
                $location = carbon_get_user_meta($current_user->ID, 'location');
                foreach ($countries as $key => $value) {
                    echo '<option value="' . esc_attr($key) . '"' . (esc_attr($key) === esc_attr($location) ? ' selected' : '') . '>' . esc_html($value) . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- New Fields -->
        <div class="col-md-6">
            <label>Skills</label>
            <select name="skills[]" class="form-control custom-select" multiple>
                <?php
                $skill = carbon_get_user_meta($current_user->ID, 'skills') ?? [];
                foreach ($skill_terms as $term) {
                    echo '<option value="' . esc_attr($term->term_id) . '"' . (in_array($term->term_id, $skill) ? ' selected' : '') . '>' . esc_html($term->name) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>Industry</label>
            <select name="industry[]" class="form-control custom-select">
                <?php
                $industry = carbon_get_user_meta($current_user->ID, 'industries') ?? [];
                foreach ($industry_terms as $term) {
                    echo '<option value="' . esc_attr($term->term_id) . '"' . (in_array($term->term_id, $industry) ? ' selected' : '') . '>' . esc_html($term->name) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>Seller Type</label>
            <select name="seller_type" class="form-control">
                <option value="agency" <?php selected(carbon_get_user_meta($current_user->ID, 'seller_type'), 'agency'); ?>>Agency</option>
                <option value="freelance" <?php selected(carbon_get_user_meta($current_user->ID, 'seller_type'), 'freelance'); ?>>Freelance</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Selling Methods</label>
            <select name="selling_methods[]" class="form-control custom-select" multiple>
                <?php
                $selling_method = carbon_get_user_meta($current_user->ID, 'selling_methods') ?? [];
                foreach ($selling_method_terms as $term) {
                    echo '<option value="' . esc_attr($term->term_id) . '"' . (in_array($term->term_id, $selling_method) ? ' selected' : '') . '>' . esc_html($term->name) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>Years of Experience</label>
            <input type="text" name="years_of_experience" class="form-control" value="<?php echo esc_attr(carbon_get_user_meta($current_user->ID, 'years_of_experience')); ?>" placeholder="Years of Experience">
        </div>

        <input type="hidden" name="user_id" value="<?php echo esc_attr($current_user->ID); ?>">
    </div>

    <input type="hidden" name="security" value="<?php echo wp_create_nonce('update-profile-agent-nonce'); ?>"/>
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </div>
</form>
<div id="profile-update-message"></div>
