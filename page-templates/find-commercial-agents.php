<?php
/**
 * Template Name: Find Commercial Agents
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
    get_header("dashboard");
    echo '<div class="alert alert-danger">Access denied. You do not have permission to access this page.</div>';
    get_footer();
    ob_end_flush();
    exit;
}

// Fetch terms for filters
$language_terms = get_terms([
    'taxonomy' => 'language',
    'hide_empty' => false,
]);
$industry_terms = get_terms([
    'taxonomy' => 'industry',
    'hide_empty' => false,
]);
$location_terms = get_terms([
    'taxonomy' => 'location',
    'hide_empty' => false,
]);
$selling_method_terms = get_terms([
    'taxonomy' => 'selling_method',
    'hide_empty' => false,
]);
$seller_type_terms = get_terms([
    'taxonomy' => 'seller_type',
    'hide_empty' => false,
]);

get_header("dashboard");
?>

<div class="dashboard find-agents">
    <div class="container pt-5 pb-5">
        <div class="row">
            <div class="col-md-8 result-section">
                <div id="results-section">
                    <div id="spinner" style="display: none;">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <style>
                        #spinner{
                            display:flex;
                            justify-content:center;
                            align-items:center;
                            min-height:500px;
                            
                        }
                    </style>
                </div>
            </div>

            <div class="col-md-4 filter-section">
                <div class="card">
                    <h4>Filter by Attributes</h4>

                    <form id="filters-form">
                        <div class="mb-3">
                            <button type="button" id="clear-filters" class="btn btn-secondary">Clear Filters</button>
                        </div>

                        <?php if ($language_terms): ?>
                            <label class="form-label" for="languages-select">Languages:</label>
                            <div class="mb-3 filter-container">
                                <select class="form-select filter" name="language[]" id="languages-select" multiple>
                                    <?php foreach ($language_terms as $term): ?>
                                        <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($industry_terms): ?>
                            <label class="form-label" for="industry-select">Industry:</label>
                            <div class="mb-3 filter-container">
                                <select class="form-select filter" name="industry[]" id="industry-select" multiple>
                                    <?php foreach ($industry_terms as $term): ?>
                                        <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($location_terms): ?>
                            <label class="form-label" for="location-select">Location:</label>
                            <div class="mb-3 filter-container">
                                <select class="form-select filter" name="location[]" id="location-select" multiple>
                                    <?php foreach ($location_terms as $term): ?>
                                        <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($selling_method_terms): ?>
                            <label class="form-label" for="selling-method-select">Selling Methods:</label>
                            <div class="mb-3 filter-container">
                                <select class="form-select filter" name="selling_method[]" id="selling-method-select" multiple>
                                    <?php foreach ($selling_method_terms as $item): ?>
                                        <option value="<?php echo esc_attr($item->term_id); ?>"><?php echo esc_html($item->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($seller_type_terms): ?>
                            <label class="form-label" for="seller-type-select">Seller Type:</label>
                            <div class="mb-3 filter-container">
                                <select class="form-select filter" name="seller_type[]" id="seller-type-select" multiple>
                                    <?php foreach ($seller_type_terms as $item): ?>
                                        <option value="<?php echo esc_attr($item->term_id); ?>"><?php echo esc_html($item->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="years_of_experience" class="form-label">Years of experience</label>
                            <input type="number" class="form-control filter" name="years_of_experience" id="years_of_experience">
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Flush the output buffer and send everything to the browser
ob_end_flush();
get_footer("dashboard");
?>