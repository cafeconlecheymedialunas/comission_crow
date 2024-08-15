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
    get_header();
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
            <div class="col-xl-8 result-section">
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
                <h4>Filter by Attributes</h4>

<form id="filters-form">
    <div class="mb-3">
        <button type="button" id="clear-filters" class="btn btn-secondary">Clear Filters</button>
    </div>

    <?php if ($language_terms): ?>
        <label class="form-label">Languages:</label>
    <div class="mb-3 filter-container">

        <?php foreach ($language_terms as $term): ?>
            <div class="form-check form-switch">
                <input class="form-check-input filter" type="checkbox" name="language[]" id="language-<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->term_id); ?>">
                <label class="form-check-label" for="language-<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></label>
            </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if ($industry_terms): ?>
        <label class="form-label">Industry:</label>
    <div class="mb-3 filter-container">

        <?php foreach ($industry_terms as $term): ?>
            <div class="form-check form-switch">
                <input class="form-check-input filter" type="checkbox" name="industry[]" id="industry-<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->term_id); ?>">
                <label class="form-check-label" for="industry-<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></label>
            </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if ($location_terms): ?>
        <label class="form-label">Location:</label>
    <div class="mb-3 filter-container">

        <?php foreach ($location_terms as $term): ?>
            <div class="form-check form-switch">
                <input class="form-check-input filter" type="checkbox" name="location[]" id="location-<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->term_id); ?>">
                <label class="form-check-label" for="location-<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></label>
            </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if ($selling_method_terms): ?>
        <label class="form-label">Selling Methods:</label>
    <div class="mb-3 filter-container">

        <?php foreach ($selling_method_terms as $item): ?>
            <div class="form-check form-switch">
                <input class="form-check-input filter" type="checkbox" name="selling_method[]" id="selling_method-<?php echo esc_attr($item->term_id); ?>" value="<?php echo esc_attr($item->term_id); ?>">
                <label class="form-check-label" for="selling_method-<?php echo esc_attr($item->term_id); ?>"><?php echo esc_html($item->name); ?></label>
            </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if ($seller_type_terms): ?>
        <label class="form-label">Seller Type:</label>
    <div class="mb-3 filter-container">

        <?php foreach ($seller_type_terms as $item): ?>
            <div class="form-check form-switch">
                <input class="form-check-input filter" type="checkbox" name="seller_type[]" id="seller_type-<?php echo esc_attr($item->term_id); ?>" value="<?php echo esc_attr($item->term_id); ?>">
                <label class="form-check-label" for="seller_type-<?php echo esc_attr($item->term_id); ?>"><?php echo esc_html($item->name); ?></label>
            </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>

    <div class="mb-3">
        <label for="years_of_experience" class="form-label">Years of experience</label>
        <input type="number" class="form-control filter" name="years_of_experience" id="years_of_experience" placeholder="Ej. 1 - 100%">
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
