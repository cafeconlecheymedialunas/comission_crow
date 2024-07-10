<?php
$languages = $admin->get_languages();
$countries = $admin->get_countries();
$selling_methods = $admin->get_selling_methods();
?>
<form id="seller-profile-form">
    <div class="row">
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
            <textarea name="description" class="form-control" rows="5"><?php echo esc_html(carbon_get_user_meta($current_user->ID, 'description')); ?></textarea>
        </div>
        <div class="col-md-6">
            <label>Languages</label>
            <select name="language[]" class="form-control" multiple>
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
            <select name="location" class="form-control">
                <?php
                $location = carbon_get_user_meta($current_user->ID, 'location');
                foreach ($countries as $key => $value) {
                    echo '<option value="' . esc_attr($key) . '"' . (esc_attr($key) === esc_attr($location) ? ' selected' : '') . '>' . esc_html($value) . '</option>';
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
            <select name="selling_methods[]" class="form-control" multiple>
                <?php
                $selling_method = carbon_get_user_meta($current_user->ID, 'selling_methods') ?? [];
                foreach ($selling_methods as $key => $value) {
                    echo '<option value="' . esc_attr($key) . '"' . (in_array($key, $selling_method) ? ' selected' : '') . '>' . esc_html($value) . '</option>';
                }
                ?>
            </select>
			<input type="hidden" name="user_id" value="<?php echo esc_attr($current_user->ID); ?>">
        </div>
      
    </div>

    <?php wp_nonce_field('update_seller_profile', 'update_seller_profile_nonce'); ?>
	<div class="col-md-12">
            <button type="submit" class="btn btn-primary">Update Profile</button>
    </div>
</form>
<div id="profile-update-message"></div>
