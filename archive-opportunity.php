<?php
/**
 * The Template for displaying Archive pages.
 */

get_header("dashboard");

if (have_posts()) :
    ?>
<header class="page-header">
	<h1 class="page-title">
		<?php
                if (is_day()) :
                    printf(esc_html__('Daily Archives: %s', 'comission_crow'), get_the_date());
                elseif (is_month()) :
                    printf(esc_html__('Monthly Archives: %s', 'comission_crow'), get_the_date(_x('F Y', 'monthly archives date format', 'comission_crow')));
                elseif (is_year()) :
                    printf(esc_html__('Yearly Archives: %s', 'comission_crow'), get_the_date(_x('Y', 'yearly archives date format', 'comission_crow')));
                else :
                   
                endif;
?>
	</h1>
</header>
<?php
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
                    <h4>Filter by atributtes</h4>
                    <form id="filters-form">
                        <div class="mb-3">
                            <button type="button" id="clear-filters" class="btn btn-secondary">Clear Filters</button>
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
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input filter" name="language[]" value="<?php echo esc_attr($term->term_id); ?>">
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
    </div><?php
else :
    // 404.
    get_template_part('content', 'none');
endif;

wp_reset_postdata(); // End of the loop.

get_footer();
