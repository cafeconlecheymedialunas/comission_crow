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

<div class="dashboard">
    <section style="background-image:url(<?php echo get_template_directory_uri() . "/assets/img/breadcrumb-bg.jpg"; ?>);">
        <div class="breadcrumbs">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 my-auto">
                        <h1 class="text-white">Find Commercial Agents</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container pt-30">
        <div class="row">
            <div class="col-md-9 result-section">
                <div id="results-section">
                    <div id="spinner" style="display: none;">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 filter-section">
                <h4>Filtros</h4>
                <form id="filters-form">
                    <div class="mb-3">
                        <button type="button" id="clear-filters" class="btn btn-secondary">Limpiar Filtros</button>
                    </div>

                    <?php if ($language_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Languages:</label>
                        <?php foreach ($language_terms as $term): ?>
                            <div class="form-check form-switch">
                                <input class="form-check-input filter" type="checkbox" name="language[]" id="language-<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->term_id); ?>">
                                <label class="form-check-label" for="language-<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($industry_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Industry:</label>
                        <?php foreach ($industry_terms as $term): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="industry" value="<?php echo esc_attr($term->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($location_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Location:</label>
                        <?php foreach ($location_terms as $term): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="location" value="<?php echo esc_attr($term->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($selling_method_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Selling Methods:</label>
                        <?php foreach ($selling_method_terms as $item): ?>
                            <div class="form-check form-switch">
                                <input class="form-check-input filter" type="checkbox" name="selling_method[]" id="selling_method-<?php echo esc_attr($item->term_id); ?>" value="<?php echo esc_attr($item->term_id); ?>">
                                <label class="form-check-label" for="selling_method-<?php echo esc_attr($item->term_id); ?>"><?php echo esc_html($item->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($seller_type_terms): ?>
                    <div class="mb-3">
                        <label class="form-label">Seller Type:</label>
                        <?php foreach ($seller_type_terms as $item): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="seller_type" value="<?php echo esc_attr($item->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($item->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="years_of_experience" class="form-label">Years of experience</label>
                        <input type="number" class="form-control filter" name="years_of_experience" id="years_of_experience" placeholder="Ej. 1 - 100%">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Flush the output buffer and send everything to the browser
ob_end_flush();
get_footer();
?>
