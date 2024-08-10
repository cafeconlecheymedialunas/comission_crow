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

$opportunity = $opportunity->posts[0];
$company = carbon_get_post_meta($opportunity->ID, "company");
$company_industry = wp_get_post_terms($company, 'industry');
$company_activity = wp_get_post_terms($company, 'activity');
$company_location = wp_get_post_terms($company, 'location');
$company_employees_number = carbon_get_post_meta($company, 'employees_number');

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
$type_of_company = wp_get_post_terms($opportunity->ID, 'type_of_company');
$currency = wp_get_post_terms($opportunity->ID, 'currency');
$target_audience = wp_get_post_terms($opportunity->ID, 'target_audience');
$age = wp_get_post_terms($opportunity->ID, 'age');
$gender = wp_get_post_terms($opportunity->ID, 'gender');

get_header("dashboard");
?>

<section style="background-image:url(<?php echo get_template_directory_uri() . "/assets/img/breadcrumb-bg.jpg"; ?>);">
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-md-12 my-auto">
                    <h1 class="text-white"><?php echo esc_html($opportunity->post_title); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pt-120 pb-95">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row project-meta-cards mb-3">
                    <!-- Display meta information -->
                    <?php if (!empty($type_of_company)): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="project-meta">
                                <div class="my-auto">
                                    <i class="fa fa-id-card-alt"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Type of Company</span>
                                    <h6><?php echo esc_html($type_of_company[0]->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($target_audience)): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="project-meta">
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

                    <?php if (!empty($sales_cycle_estimation)): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="project-meta">
                                <div class="my-auto">
                                    <i class="fa fa-clock"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Sales Cycle Estimation</span>
                                    <h6><?php echo esc_html($sales_cycle_estimation); ?> Days</h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($currency)): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="project-meta">
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
                        <div class="col-xl-4 col-md-6">
                            <div class="project-meta">
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

                    <?php if (!empty($gender)): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="project-meta">
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

                    <div class="col-xl-4 col-md-6">
                        <div class="project-meta">
                            <div class="my-auto">
                                <i class="fa fa-shield-alt"></i>
                            </div>
                            <div class="my-auto">
                                <span>Project Level</span>
                                <h6>Expensive</h6>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($languages)): ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="project-meta">
                                <div class="my-auto">
                                    <i class="fa fa-globe-asia"></i>
                                </div>
                                <div class="my-auto">
                                    <span>Languages</span>
                                    <h6><?php echo implode(', ', $languages); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>

                <div class="project-entry-content mb-3">
                    <h4>Description</h4>
                    <?php echo wp_kses_post($opportunity->post_content); ?>
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
                    <div class="project-videos mb-3">
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
                                        <div class="col-md-6 mb-3">
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



                <?php if (!empty($supporting_materials)): ?>
                    <div class="supporting-materials mb-3">
                        <h4>Supporting Materials</h4>
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


                <div class="project-entry-content mb-3">
                    <h6>Tips</h6>
                    <?php echo wp_kses_post($tips); ?>
                </div>
                <?php if (!empty($question_1)): ?>
                    <div class="project-entry-content mb-3">
                        <h6>What is your company’s elevator pitch?</h6>
                        <?php echo wp_kses_post($question_1); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_2)): ?>
                    <div class="project-entry-content mb-3">
                        <h6>Please complete the below value statement: Example: "We help (XXX) in the (XXX) industry (XXX) WITHOUT (XXX) & WITHOUT (XXX).</h6>
                        <?php echo wp_kses_post($question_2); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_3)): ?>
                    <div class="project-entry-content mb-3">
                        <h6>How do you currently pitch your company to a prospect?</h6>
                        <?php echo wp_kses_post($question_3); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_4)): ?>
                    <div class="project-entry-content mb-3">
                        <h6>What are the most common objections you face within your current sales cycle?</h6>
                        <?php echo wp_kses_post($question_4); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_5)): ?>
                    <div class="project-entry-content mb-3">
                        <h6>What strategies do you employ to overcome the objections specified?</h6>
                        <?php echo wp_kses_post($question_5); ?>
                    </div>
                <?php endif;?>
                <?php if (!empty($question_6)): ?>
                    <div class="project-entry-content mb-3">
                        <h6>Please give an overview of what company challenges you help your clients overcome?</h6>
                        <?php echo wp_kses_post($question_6); ?>
                    </div>
                <?php endif;?>
            </div>

            <div class="col-lg-4 position-relative">
                <div class="widget-area">
                    <div class="project-widget">
                        <div class="text-center">
                            <h5>Price</h5>
                            <h1>
                                <span class="woocommerce-Price-amount amount">
                                    <bdi><?php echo Helper::format_price($price); ?></bdi>
                                </span>
                            </h1>
                            <p>Commission: <?php echo esc_html($commission); ?>%</p>
                            <p>Deliver Leads?: <span><?php echo esc_html($deliver_leads === 'yes' ? "Yes" : "No"); ?></span></p>
                            <p>Sales Cycle Estimation: <span><?php echo esc_html($sales_cycle_estimation); ?> Days</span></p>
                        </div>
                        <ul class="list-unstyled">
                            <li><i class="fa fa-calendar"></i> <span>Posted <?php echo esc_html(Helper::get_human_time_diff($date) . " ago."); ?></span></li>
                            <li><i class="fa fa-user"></i> <span>Posted By: <?php echo esc_html(get_the_title($company)); ?></span></li>
                            <li><i class="fa fa-map-marker-alt"></i> <span>Location: <a href="#"><?php echo esc_html($location[0]->name ?? "Not Specified"); ?></a></span></li>
                            <li><i class="fa fa-briefcase"></i> <span>Industry: <a href="#"><?php echo esc_html($industry[0]->name ?? 'Not Specified'); ?></a></span></li>
                        </ul>
                    </div>
                    <div class="project-widget">
                        <div class="text-center">
                            <h3 class="project-widget-title">About Company</h3>
                            <a href="https://nexfyapp-cp167.wordpresstemporal.com/subcarpeta/buyers/nicoreyes7/">
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
                            </a>
                            <a href="https://nexfyapp-cp167.wordpresstemporal.com/subcarpeta/buyers/nicoreyes7/" target="_blank">
                                <h4><?php echo esc_html(get_the_title($company)); ?></h4>
                            </a>
                            <p>Industry: <?php echo esc_html($company_industry[0]->name ?? 'Not Specified'); ?></p>
                            <p>Activity: <?php echo esc_html($company_activity[0]->name ?? 'Not Specified'); ?></p>
                            <p>Number of Employees: <?php echo esc_html($company_employees_number[0]->name ?? 'Not Specified'); ?></p>
                        </div>

                        <ul class="list-unstyled mt-4 meta">
                            <!-- Add any additional meta items here if needed -->
                        </ul>
                        <div class="text-center">
                            <a href="#" class="btn btn-primary mt-5" data-bs-toggle="modal" data-bs-target="#message69">
                                Contact Company
                            </a>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
</section>

<?php get_footer();?>
