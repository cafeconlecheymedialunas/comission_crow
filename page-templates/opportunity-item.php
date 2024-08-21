<?php
/**
 * Template Name: Opportunity
 * Description: Page template for Authenticated Users with roles.
 */

// Start output buffering to prevent header modification errors
ob_start();

if (!is_user_logged_in()) {
    wp_redirect(home_url("/auth"));
    exit;
}

$opportunity_id = isset($_GET['opportunity_id']) ? intval($_GET['opportunity_id']) : 0;

if (!$opportunity_id) {
    wp_redirect(home_url("/find-opportunities"));
    exit;
}

$opportunity = new WP_Query([
    "post_type" => "opportunity",
    "p" => $opportunity_id,
]);

if (!$opportunity->posts) {
    wp_redirect(home_url("/find-opportunities"));
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

$opportunity = $opportunity->posts[0];
$company = carbon_get_post_meta($opportunity->ID, "company");
$company_industry = wp_get_post_terms($company, 'industry');
$company_activity = wp_get_post_terms($company, 'activity');
$company_location = wp_get_post_terms($company, 'location');
$company_employees_number = carbon_get_post_meta($company, 'employees_number');
$company_type_of_company = wp_get_post_terms($company, 'type_of_company');

$company_street = carbon_get_post_meta($company, 'company_street');
$company_number = carbon_get_post_meta($company, 'company_number');
$company_street = carbon_get_post_meta($company, 'company_street');
$company_street = carbon_get_post_meta($company, 'company_street');
$company_city = carbon_get_post_meta($company, 'company_city');
$company_postalcode = carbon_get_post_meta($company, 'company_postalcode');
$company_country = carbon_get_post_meta($company, 'company_country');

$company_website_url = carbon_get_post_meta($company, 'website_url');
$company_facebook_url = carbon_get_post_meta($company, 'facebook_url');
$company_instagram_url = carbon_get_post_meta($company, 'instagram_url');
$company_twitter_url = carbon_get_post_meta($company, 'twitter_url');
$company_linkedin_url = carbon_get_post_meta($company, 'linkedin_url');
$company_tiktok_url = carbon_get_post_meta($company, 'tiktok_url');
$company_youtube_url = carbon_get_post_meta($company, 'youtube_url');
$address_parts = [];

// Agregar partes de la dirección solo si existen
if (!empty($company_street)) {
    $address_parts[] = $company_street;
}
if (!empty($company_number)) {
    $address_parts[] = $company_number;
}
if (!empty($company_city)) {
    $address_parts[] = $company_city;
}
if (!empty($company_postalcode)) {
    $address_parts[] = $company_postalcode;
}
if (!empty($company_country)) {
    $address_parts[] = $company_country;
}

// Unir las partes con comas
$company_full_address = implode(', ', $address_parts);


   

$sales_cycle_estimation = carbon_get_post_meta($opportunity->ID, "sales_cycle_estimation");
$price = carbon_get_post_meta($opportunity->ID, "price");
$commission = carbon_get_post_meta($opportunity->ID, "commission");
$deliver_leads = carbon_get_post_meta($opportunity->ID, 'deliver_leads') ?: 'no';
$date = carbon_get_post_meta($opportunity->ID, 'date');

$images = carbon_get_post_meta($opportunity->ID, 'images');
$supporting_materials = carbon_get_post_meta($opportunity->ID, 'supporting_materials');
$videos = carbon_get_post_meta($opportunity->ID, 'videos');
$tips = carbon_get_post_meta($opportunity->ID, 'tips');
$question_1 = carbon_get_post_meta($opportunity->ID, 'question_1');
$question_2 = carbon_get_post_meta($opportunity->ID, 'question_2');
$question_3 = carbon_get_post_meta($opportunity->ID, 'question_3');
$question_4 = carbon_get_post_meta($opportunity->ID, 'question_4');
$question_5 = carbon_get_post_meta($opportunity->ID, 'question_5');
$question_6 = carbon_get_post_meta($opportunity->ID, 'question_6');

$industry = wp_get_post_terms($opportunity->ID, 'industry');
$language = wp_get_post_terms($opportunity->ID, 'language');
$languages = !empty($language) ? array_map(function ($lang) {
    return esc_html($lang->name);
}, $language) : [];
$location = wp_get_post_terms($opportunity->ID, 'location');
$currency = wp_get_post_terms($opportunity->ID, 'currency');
$target_audience = wp_get_post_terms($opportunity->ID, 'target_audience');
$age = wp_get_post_terms($opportunity->ID, 'age');
$gender = wp_get_post_terms($opportunity->ID, 'gender');

get_header("dashboard");

$template = 'templates/page-header-title.php';
$page_custom_title = get_the_title($opportunity->ID);

if (locate_template($template)) {
    include locate_template($template);
}
?>


<section class="pt-5 pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
            <div class="row opportunity-meta-cards mb-3">
                    <?php if (!empty($target_audience)): ?>
                        <div class="col-xl-4 col-md-6 target-audience">
                            <div class="opportunity-meta">
                                <div class="my-auto">
                                    <i class="fa-solid fa-bullseye"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Target Audience</span>
                                    <h6><?php echo esc_html($target_audience[0]->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($industry)): ?>
                        <div class="col-xl-4 col-md-6 industry">
                            <div class="opportunity-meta">
                                <div class="my-auto">
                                    <i class="fa fa-clock"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Industry</span>
                                    <h6><?php echo esc_html($industry[0]->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($currency)): ?>
                        <div class="col-xl-4 col-md-6 currency">
                            <div class="opportunity-meta">
                                <div class="my-auto">
                                    <i class="fa fa-dollar-sign"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Currency</span>
                                    <h6><?php echo esc_html($currency[0]->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($age)): ?>
                        <div class="col-xl-4 col-md-6 age">
                            <div class="opportunity-meta">
                                <div class="my-auto">
                                    <i class="fa fa-calendar-alt"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Age Range</span>
                                    <h6><?php echo esc_html($age[0]->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($location)): ?>
                        <div class="col-xl-4 col-md-6 location">
                            <div class="opportunity-meta">
                                <div class="my-auto">
                                    <i class="fa fa-calendar-alt"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Location</span>
                                    <h6><?php echo esc_html($location[0]->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($gender)): ?>
                        <div class="col-xl-4 col-md-6 gender">
                            <div class="opportunity-meta">
                                <div class="my-auto">
                                    <i class="fa fa-venus-mars"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Gender</span>
                                    <h6><?php echo esc_html($gender[0]->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($languages)): ?>
                        <div class="col-xl-4 col-md-6 language">
                            <div class="opportunity-meta">
                                <div class="my-auto">
                                    <i class="fa fa-globe-asia"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Languages</span>
                                    <h6><?php echo esc_html(implode(', ', $languages)); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>


                <div class="opportunity-entry-content mb-3">
                    <?php echo$opportunity->post_content; ?>
                </div>

                <?php if (!empty($images)): ?>
                    <div class="mb-3 row">
                        <h4>Images</h4>
                        <?php foreach ($images as $image_id): ?>
                            <?php
                                $image_src = wp_get_attachment_image_src($image_id, 'full');
                                if ($image_src): ?>
                                <div class="col-md-4 mb-3">
                                    <img src="<?php echo esc_url($image_src[0]); ?>" alt="<?php echo esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)); ?>" class="img-fluid"/>
                                </div>
                            <?php endif;?>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>


                <?php if (!empty($videos)): ?>
    <div class="opportunity-videos mb-3">
        <h4>Videos</h4>
        <div class="row">
            <?php foreach ($videos as $video_data): ?>
                <?php
                    if (isset($video_data["video"]) && !empty($video_data["video"])):
                        $video_url = $video_data["video"];

                        // Analiza la URL y extrae el ID del video
                        $parsed_url = parse_url($video_url);
                        $video_id = '';

                        if (isset($parsed_url['query'])) {
                            parse_str($parsed_url['query'], $query_params);
                            $video_id = $query_params['v'] ?? '';
                        } elseif (isset($parsed_url['path'])) {
                            // Manejar URLs cortas (youtu.be)
                            $path_parts = explode('/', trim($parsed_url['path'], '/'));
                            $video_id = $path_parts[0] ?? '';
                        }

                        if (!empty($video_id)): ?>
                            <div class="col-sm-6 mb-3">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>" allowfullscreen></iframe>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endif;?>
            <?php endforeach;?>
        </div>
    </div>
                <?php endif;?>

<style>
    .embed-responsive {
        overflow: hidden;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        position: relative;
        height: 0;
    }

    .embed-responsive-item {
        border: 0;
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
    }

    .col-md-4, .col-sm-6 {
        padding-right: 15px;
        padding-left: 15px;
    }
</style>




                <?php if (!empty($supporting_materials)): ?>
                    <div class="supporting-materials mb-3">
                        <h6>Supporting Materials</h6>
                        <ul class="list-group">
                            <?php foreach ($supporting_materials as $material):
                            $material_src = wp_get_attachment_url($material); // URL completa del archivo
                            $file_info = pathinfo($material_src); // Extrae información del archivo
                            $file_name = isset($file_info['filename']) ? $file_info['filename'] : ''; // Nombre del archivo sin extensión
                            $file_extension = isset($file_info['extension']) ? $file_info['extension'] : ''; // Extensión del archivo
                            ?>
	                                <li class="list-group-item d-flex justify-content-between align-items-center">
	                                    <a href="<?php echo esc_url($material_src); ?>" download class="text-decoration-none text-dark">
	                                        <i class="fa fa-file-pdf-o fa-2x text-danger mr-2"></i>
	                                        <?php echo esc_html($file_name . '.' . $file_extension); ?>
	                                    </a>
	                                    <span class="badge bg-secondary rounded-pill"><?php echo esc_html($file_extension); ?></span>
	                                </li>
	                            <?php endforeach;?>
                        </ul>
                    </div>
                <?php endif;?>

                <?php if (!empty($tips)): ?>
                <div class="opportunity-entry-content mb-3">
                    <h6>Tips</h6>
                    <?php echo wp_kses_post($tips); ?>
                </div>
                <?php endif;?>
                <?php if (!empty($question_1)): ?>
                    <div class="opportunity-entry-content mb-3">
                        <h6>What is your company’s elevator pitch?</h6>
                        <?php echo wp_kses_post($question_1); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_2)): ?>
                    <div class="opportunity-entry-content mb-3">
                        <h6>Please complete the below value statement: Example: "We help (XXX) in the (XXX) industry (XXX) WITHOUT (XXX) & WITHOUT (XXX).</h6>
                        <?php echo wp_kses_post($question_2); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_3)): ?>
                    <div class="opportunity-entry-content mb-3">
                        <h6>How do you currently pitch your company to a prospect?</h6>
                        <?php echo wp_kses_post($question_3); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_4)): ?>
                    <div class="opportunity-entry-content mb-3">
                        <h6>What are the most common objections you face within your current sales cycle?</h6>
                        <?php echo wp_kses_post($question_4); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_5)): ?>
                    <div class="opportunity-entry-content mb-3">
                        <h6>What strategies do you employ to overcome the objections specified?</h6>
                        <?php echo wp_kses_post($question_5); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_6)): ?>
                    <div class="opportunity-entry-content mb-3">
                        <h6>Please give an overview of what company challenges you help your clients overcome?</h6>
                        <?php echo wp_kses_post($question_6); ?>
                    </div>
                <?php endif;?>
            </div>

            <div class="col-lg-4 position-relative">
                <div class="widget-area">
                <div class="opportunity-widget">
    <div class="text-center">
        <h5>Average deal value</h5>
        
        <?php if (!empty($price)): ?>
            <span class="price">
                <?php echo Helper::convert_price_to_selected_currency($price); ?>
            </span>
        <?php else: ?>
            <span class="price">Price not specified</span>
        <?php endif; ?>
        
        <?php if (!empty($commission)): ?>
            <p>Commission: <strong><?php echo esc_html($commission); ?>%</strong></p>
        <?php endif; ?>
        
        <?php if (isset($deliver_leads)): ?>
            <p>Deliver Leads?: <strong><?php echo esc_html($deliver_leads === 'yes' ? "Yes" : "No"); ?></strong></p>
        <?php endif; ?>
        
        <?php if (!empty($sales_cycle_estimation)): ?>
            <p>Sales Cycle Estimation: <strong><?php echo esc_html($sales_cycle_estimation); ?> Days</strong></p>
        <?php endif; ?>
        
        <?php if (!empty($opportunity)): ?>
            <p><i class="fa fa-calendar"></i> <span>Posted <?php echo esc_html(Helper::get_human_time_diff($opportunity->post_date) . " ago."); ?></span></p>
        <?php endif; ?>
        
        <?php if (!empty($company)): ?>
            <p><i class="fa fa-user"></i> <span>Posted By: <?php echo esc_html(get_the_title($company)); ?></span></p>
        <?php endif; ?>
    </div>
</div>

<div class="opportunity-widget">
    <div class="text-center">
        <span>
            <?php
            if ($company) {
                $post_thumbnail = get_the_post_thumbnail($company, [100, 100], ['class' => 'rounded-circle']);
                $default = get_template_directory_uri() . "/assets/img/placeholder.png";
                if ($post_thumbnail) {
                    echo $post_thumbnail;
                } else {
                    echo '<img class="rounded-circle" width="100" height="100" src="' . esc_url($default) . '"/>';
                }
            }
            ?>
        </span>

        <?php if ($company): ?>
            <h4 class="company-name"><?php echo esc_html(get_the_title($company)); ?></h4>
        <?php endif; ?>

        <?php if (!empty($company_industry)): ?>
            <p>Industry: <strong><?php echo esc_html($company_industry[0]->name); ?></strong></p>
        <?php endif; ?>

        <?php if (!empty($company_activity)): ?>
            <p>Activity: <strong><?php echo esc_html($company_activity[0]->name); ?></strong></p>
        <?php endif; ?>

        <?php if (!empty($company_location)): ?>
            <p>Location: <strong><?php echo esc_html($company_location[0]->name); ?></strong></p>
        <?php endif; ?>

        <?php if (!empty($company_type_of_company)): ?>
            <p>Type of Company: <strong><?php echo esc_html($company_type_of_company[0]->name); ?></strong></p>
        <?php endif; ?>

        <?php if (!empty($company_employees_number)): ?>
            <p>Number of Employees: <strong><?php echo esc_html($company_employees_number); ?></strong></p>
        <?php endif; ?>

        <?php if (!empty($company_full_address)): ?>
            <p><?php echo esc_html($company_full_address); ?></p>
        <?php endif; ?>

        <?php if (!empty($company_website_url) || !empty($company_facebook_url) || !empty($company_instagram_url) || !empty($company_twitter_url) || !empty($company_linkedin_url) || !empty($company_tiktok_url) || !empty($company_youtube_url)): ?>
            <div class="company-social-links">
                <ul class="list-inline">
                    <?php if (!empty($company_website_url)): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo esc_url($company_website_url); ?>" target="_blank">
                                <i class="fas fa-globe"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($company_facebook_url)): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo esc_url($company_facebook_url); ?>" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($company_instagram_url)): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo esc_url($company_instagram_url); ?>" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($company_twitter_url)): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo esc_url($company_twitter_url); ?>" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($company_linkedin_url)): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo esc_url($company_linkedin_url); ?>" target="_blank">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($company_tiktok_url)): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo esc_url($company_tiktok_url); ?>" target="_blank">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($company_youtube_url)): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo esc_url($company_youtube_url); ?>" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

                </div>
                </div>
        </div>
    </div>
</section>

<?php get_footer("dashboard");?>
