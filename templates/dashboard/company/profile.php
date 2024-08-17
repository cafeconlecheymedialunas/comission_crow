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

$location_terms = get_terms([
    "taxonomy" => "location",
    "hide_empty" => false,
]);

$currency_terms = get_terms([
    "taxonomy" => "currency",
    "hide_empty" => false,
]);

$company = Company::get_instance();
$company_post = $company->get_company();

$profile_image = isset($company_post) ? [get_post_thumbnail_id($company_post->ID)] : '';

$industry = wp_get_post_terms($company_post->ID, 'industry', ['fields' => 'ids']) ?? [];
$activity = wp_get_post_terms($company_post->ID, 'activity', ['fields' => 'ids']) ?? [];
$type_of_company = wp_get_post_terms($company_post->ID, 'type_of_company', ['fields' => 'ids']) ?? [];
$location = wp_get_post_terms($company_post->ID, 'location', ['fields' => 'ids']) ?? [];
$currency = wp_get_post_terms($company_post->ID, 'currency', ['fields' => 'ids']) ?? [];

$company_name = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'company_name') : '';
$company_street = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'company_street') : '';
$company_number = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'company_number') : '';
$company_city = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'company_city') : '';
$company_state = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'company_state') : '';
$company_postalcode = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'company_postalcode') : '';
$employees_number = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'employees_number') : '';
$website_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'website_url') : '';
$facebook_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'facebook_url') : '';
$instagram_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'instagram_url') : '';
$twitter_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'twitter_url') : '';
$linkedin_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'linkedin_url') : '';
$tiktok_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'tiktok_url') : '';
$youtube_url = isset($company_post) ? carbon_get_post_meta($company_post->ID, 'youtube_url') : '';

//echo do_shortcode('[fullstripe_form name="Prueba" type="inline_save_card"]');

?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Profile"); ?></h2>

</div>
<div class="row">
	<div class="col-md-8">
	    <div class="card">
            <h4>Company</h4>
            <form id="company-profile-form">
                <div class="row">
                    <div class="col-md-12">
                        <label for="company_name">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="<?php echo esc_attr($company_name); ?>" placeholder="Company Name">
                        <div class="error-message"></div>
                    </div>
                    <div class="col-md-12">
                        <label for="company_Logo" class="form-label">Company Logo:</label>
                        <input type="file" id="company_logo" name="company_logo" class="form-control">
                        <div class="error-message"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <div class="editor-container" data-target="post_content"></div>
                        <input type="hidden" id="post_content" name="post_content" value="<?php echo isset($company_post) ? $company_post->post_content : ""; ?>">
                        <div class="error-message"></div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <h5>Billing Company Address</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="company_street">Street</label>
                                <input type="text" name="company_street" id="company_street" class="form-control" value="<?php echo esc_attr($company_street); ?>" placeholder="Street">
                                <div class="error-message"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="company_number">Number</label>
                                <input type="text" name="company_number" id="company_number" class="form-control" value="<?php echo esc_attr($company_number); ?>" placeholder="Number">
                                <div class="error-message"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="company_city">City</label>
                                <input type="text" name="company_city" id="company_city" class="form-control" value="<?php echo esc_attr($company_city); ?>" placeholder="City">
                                <div class="error-message"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="company_state">State</label>
                                <input type="text" name="company_state" id="company_state" class="form-control" value="<?php echo esc_attr($company_state); ?>" placeholder="State">
                                <div class="error-message"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="company_postalcode">Postal Code</label>
                                <input type="text" name="company_postalcode" id="company_postalcode" class="form-control" value="<?php echo esc_attr($company_postalcode); ?>" placeholder="Postal Code">
                                <div class="error-message"></div>
                            </div>
                        </div>
                        <hr>
                    </div>


                    <?php if ($industry_terms): ?>
                    <div class="col-md-6">
                        <label for="industry" class="form-label">Industry:</label>
                        <select name="industry[]" class="form-select">
                            <?php foreach ($industry_terms as $term): ?>
                                <option
                                    value="<?php echo esc_attr($term->term_id); ?>"
                                    <?php echo in_array($term->term_id, $industry) ? 'selected' : ''; ?>
                                    >
                                    <?php echo esc_html($term->name); ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                        <div class="error-message"></div>
                    </div>
                    <?php endif;?>
                    <?php if ($location_terms): ?>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location:</label>
                            <select name="location[]" class="form-select">
                                <option value="">Select an option</option>
                                <?php foreach ($location_terms as $term): ?>
                                    <option
                                        value="<?php echo esc_attr($term->term_id); ?>"
                                        <?php echo in_array($term->term_id, $location) ? 'selected' : ''; ?>
                                        >
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                    <?php endif;?>
                    <?php if ($currency_terms): ?>
    <div class="col-md-6">
        <label for="currency" class="form-label">Currency<button type="button" class="operation ms-2" data-bs-toggle="tooltip" data-bs-html="true" title="
              Select the currency you wish to use to display all prices on our platform.<br><br>
What does this mean?<br>
The currency you choose will be used to show the prices of all products and services in your account. This includes prices on invoices, quotes, and any other cost details you see on the platform.<br><br>
Once you select a currency, all prices will be displayed in that currency. Make sure to choose the currency that you prefer or that best fits your country or region.<br><br>
If you need to change the currency in the future, you can do so from this same section of your profile.

            ">
            <i class="fa-solid fa-circle-info text-primary"></i>
            </button>:</label>
        <div class="d-flex align-items-center">
            <select name="currency[]" class="form-select">
                <option value="">Select an option</option>
                <?php foreach ($currency_terms as $term): ?>
                    <option
                        value="<?php echo esc_attr($term->term_id); ?>"
                        <?php echo in_array($term->term_id, $currency) ? 'selected' : ''; ?>
                    >
                        <?php echo esc_html($term->name); ?>
                    </option>
                <?php endforeach;?>
            </select>
            
        </div>
        <div class="error-message"></div>
    </div>
<?php endif; ?>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(function (tooltip) {
            new bootstrap.Tooltip(tooltip);
        });
    });
</script>

                    <?php if ($activity_terms): ?>
                    <div class="col-md-6">
                        <label for="activity" class="form-label">Activity:</label>
                        <select name="activity[]" class="form-select">
                            <option value="">Select an option</option>
                            <?php foreach ($activity_terms as $term): ?>
                                <option
                                    value="<?php echo esc_attr($term->term_id); ?>"
                                    <?php if (isset($activity[0]) && $activity[0] == $term->term_id) echo 'selected'; ?>>
                                    <?php echo esc_html($term->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message"></div>
                    </div>
                <?php endif; ?>


                    <?php if ($type_of_company_terms): ?>
                        <div class="col-md-6">
                            <label for="type_of_company" class="form-label">Company Type:</label>
                            <select name="type_of_company[]" class="form-select">
                                <option value="">Select an option</option>
                                <?php foreach ($type_of_company_terms as $term): ?>
                                    <option
                                        value="<?php echo esc_attr($term->term_id); ?>"
                                        <?php if (isset($type_of_company[0]) && $type_of_company[0] == $term->term_id) echo 'selected'; ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                            <div class="error-message"></div>
                        </div>
                    <?php endif;?>
                    <div class="col-md-6">
                        <label for="employees_number">Number of employees</label>
                        <input type="text" name="employees_number" class="form-control" value="<?php echo esc_attr($employees_number); ?>" placeholder="Number of Employees">
                        <div class="error-message"></div>
                    </div>


                    <div class="col-md-12">
                        <hr>
                        <h5>Social Media</h5>
                        <div class="row">
                        <div class="col-md-6">
                            <label for="website">Website</label>
                            <input type="text" name="website_url" class="form-control" value="<?php echo esc_attr($website_url); ?>" placeholder="Website">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="facebook">Facebook</label>
                            <input type="text" name="facebook_url" class="form-control" value="<?php echo esc_attr($facebook_url); ?>" placeholder="Facebook">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="instagram">Instagram</label>
                            <input type="text" name="instagram_url" class="form-control" value="<?php echo esc_attr($instagram_url); ?>" placeholder="Instagram">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="twitter">Twitter</label>
                            <input type="text" name="twitter_url" class="form-control" value="<?php echo esc_attr($twitter_url); ?>" placeholder="Twitter">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="linkedin">Linkedin</label>
                            <input type="text" name="linkedin_url" class="form-control" value="<?php echo esc_attr($linkedin_url); ?>" placeholder="Linkedin">
                            <div class="error-message"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="tiktok">Tik Tok</label>
                            <input type="text" name="tiktok_url" class="form-control" value="<?php echo esc_attr($tiktok_url); ?>" placeholder="Tik Tok">
                            <div class="error-message"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="youtube">You Tube</label>
                            <input type="text" name="youtube_url" class="form-control" value="<?php echo esc_attr($youtube_url); ?>" placeholder="You Tube">
                            <div class="error-message"></div>
                        </div>
                        </div>
                    </div>



                </div>
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('update-profile-nonce'); ?>"/>
                <input type="hidden" name="company_id" value="<?php echo $company_post->ID; ?>">
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
    "/templates/dashboard/form-password.php";?>
        </div>
    </div>
    <?php comments_template('', true);?>
</div>
