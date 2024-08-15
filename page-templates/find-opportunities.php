<?php
/**
 * Template Name: Find Opportunities
 * Description: Page template for Authenticated Users with roles.
 */

// Start output buffering to prevent header modification errors
ob_start();

if (!is_user_logged_in()) {
    wp_redirect(home_url("/auth"));
    exit;
}
$current_user = wp_get_current_user();
$allowed_roles = ["commercial_agent", "company"];

// Check if user has a permitted role
if (!in_array($current_user->roles[0], $allowed_roles)) {
    get_header();
    echo '<div class="container alert alert-danger">Access denied. You do not have permission to access this page.</div>';
    get_footer();
    ob_end_flush(); // Flush output buffer
    exit;
}
$language_terms = get_terms([
    "taxonomy" => "language",
    "hide_empty" => false,
]);
$industry_terms = get_terms([
    "taxonomy" => "industry",
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

$type_of_company_terms = get_terms([
    "taxonomy" => "type_of_company",
    "hide_empty" => false,
]);

$age_terms = get_terms([
    "taxonomy" => "age",
    "hide_empty" => false,
]);

$target_audience_terms = get_terms([
    "taxonomy" => "target_audience",
    "hide_empty" => false,
]);

$gender_terms = get_terms([
    "taxonomy" => "gender",
    "hide_empty" => false,
]);
get_header("dashboard");
?>

<div class="dashboard find-opportunities">
    <?php
    $spinner_template = 'templates/page-header-title.php';
    if (locate_template($spinner_template)) {
        include locate_template($spinner_template);
    }
    ?>
    <div class="container pt-5 pb-5">
        <div class="row">
            <div class="col-xl-8 result-section mb-5">
                <div id="results-section">
                    <div id="spinner" style="display: none;">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 filter-section">
                <div class="card">
                    <h4 class="filter-title">Filter by attributes</h4>
                    <form id="filters-form">
                        <div class="mb-3">
                            <button type="button" id="clear-filters" class="btn btn-secondary">Clear Filters</button>
                        </div>

                        <?php if ($industry_terms): ?>
                        <label class="form-label">Industry:</label>
                        <div class="mb-3 filter-container">
                         
                            <?php foreach ($industry_terms as $term): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="industry[]" value="<?php echo esc_attr($term->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($language_terms): ?>
                        <label class="form-label">Languages:</label>
                        <div class="mb-3 filter-container">
                            
                            <?php foreach ($language_terms as $term): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="language[]" value="<?php echo esc_attr($term->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($location_terms): ?>
                        <label class="form-label">Location:</label>
                        <div class="mb-3 filter-container">
                          
                            <?php foreach ($location_terms as $term): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="location[]" value="<?php echo esc_attr($term->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($currency_terms): ?>
                        <label class="form-label">Currency:</label>
                        <div class="mb-3 filter-container">
                            
                            <?php foreach ($currency_terms as $term): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="currency[]" value="<?php echo esc_attr($term->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($target_audience_terms): ?>
                        <label class="form-label">Target Audience:</label>
                        <div class="mb-3 filter-container">
                            
                            <?php foreach ($target_audience_terms as $item): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="target_audience[]" value="<?php echo esc_attr($item->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($item->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($gender_terms): ?>
                        <label class="form-label">Gender:</label>
                        <div class="mb-3 filter-container">
                            
                            <?php foreach ($gender_terms as $item): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="gender[]" value="<?php echo esc_attr($item->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($item->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($age_terms): ?>
                        <label class="form-label">Age:</label>
                        <div class="mb-3 filter-container">
                           
                            <?php foreach ($age_terms as $item): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="age[]" value="<?php echo esc_attr($item->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($item->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($type_of_company_terms): ?>
                        <label class="form-label">Company Type:</label>
                        <div class="mb-3 filter-container">
                            
                            <?php foreach ($type_of_company_terms as $term): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="type_of_company[]" value="<?php echo esc_attr($term->term_id); ?>">
                                    <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Deliver Leads?:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input filter" name="deliver_leads" value="yes">
                                <label class="form-check-label">Yes</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="minimum_price" class="form-label">Minimum price</label>
                                <input type="number" class="form-control filter" name="minimum_price" id="minimum_price" placeholder="Ej. $0 - $1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="maximum_price" class="form-label">Maximum price</label>
                                <input type="number" class="form-control filter" name="maximum_price" id="maximum_price" placeholder="Ej. $0 - $1000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="commission" class="form-label">Commission</label>
                            <input type="number" class="form-control filter" name="commission" id="commission" placeholder="Ej. 1 - 100%">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer("dashboard");
ob_end_flush(); // Flush output buffer
?>
