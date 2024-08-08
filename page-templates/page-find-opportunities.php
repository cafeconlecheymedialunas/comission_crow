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

$language_terms = get_terms([
    "taxonomy" => "language",
    "hide_empty" => false,
]);
$industry_terms = get_terms([
    "taxonomy" => "industry",
    "hide_empty" => false,
]);
$country_terms = get_terms([
    "taxonomy" => "country",
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

<div class="dashboard">
    <section style="background-image:url(<?php echo get_template_directory_uri() . "/assets/img/breadcrumb-bg.jpg"; ?>);">
        <div class="breadcrumbs">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 my-auto">
                        <h1 class="text-white">Find Opportunities</h1>
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

                    <?php if ($language_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Languages:</label>
                        <?php foreach ($language_terms as $term): ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input filter" name="language[]" value="<?php echo esc_attr($term->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($country_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Country:</label>
                        <?php foreach ($country_terms as $term): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="country" value="<?php echo esc_attr($term->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($currency_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Currency:</label>
                        <?php foreach ($currency_terms as $term): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="currency" value="<?php echo esc_attr($term->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($target_audience_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Target Audience:</label>
                        <?php foreach ($target_audience_terms as $item): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="target_audience" value="<?php echo esc_attr($item->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($item->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($gender_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Gender:</label>
                        <?php foreach ($gender_terms as $item): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="gender" value="<?php echo esc_attr($item->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($item->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($age_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Age:</label>
                        <?php foreach ($age_terms as $item): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="age" value="<?php echo esc_attr($item->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($item->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($type_of_company_terms): ?>
                    <div class="mb-3 filter-container">
                        <label class="form-label">Company Type:</label>
                        
                        <?php foreach ($type_of_company_terms as $term): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input filter" name="type_of_company" value="<?php echo esc_attr($term->term_id); ?>">
                                <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                   
                    <div class="mb-3 filter-container">
                        <label class="form-label">Deliver Leads?:</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input filter" name="deliver_leads" value="yes">
                            <label class="form-check-label">Yes</label>
                        </div>
                    </div>
                   
                    <div class="range_container">
                        <div class="sliders_control">
                            <input id="fromSlider" class="filter" type="range" value="10" min="0" max="100"/>
                            <input id="toSlider" class="filter" type="range" value="40" min="0" max="100"/>
                        </div>
                        <div class="form_control">
                            <div class="form_control_container">
                                <div class="form_control_container__time">Min</div>
                                <input class=" form_control_container__time__input" type="number" name="minimum_price" id="fromInput"  min="0" max="100"/>
                            </div>
                            <div class="form_control_container">
                                <div class="form_control_container__time">Max</div>
                                <input class="form_control_container__time__input" type="number" name="maximum_price" id="toInput" min="0" max="100"/>
                            </div>
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

<?php
// Flush the output buffer and send everything to the browser
ob_end_flush();
get_footer();
?>
