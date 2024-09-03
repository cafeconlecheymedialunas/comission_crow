<?php
/**
 * Template Name: Commercial Agent
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


$commercial_agent_id = isset($_GET['commercial_agent_id']) ? intval($_GET['commercial_agent_id']) : 0;

$commercial_agent = new WP_Query([
    "post_type" => "commercial_agent",
    "p" => $commercial_agent_id,
]);

if (!$commercial_agent_id || empty($commercial_agent->posts)) {
    wp_redirect(home_url("/find-agents"));
    exit;
}
$commercial_agent = $commercial_agent->posts[0];
get_header("dashboard");

$user_id = carbon_get_post_meta($commercial_agent->ID, "user_id");
$user_commercial_agent = get_user_by("ID", $user_id);



$commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();

$completed_commission_request = [];
$pending_commission_request = [];
foreach ($commission_requests as $commission_request) {
    $status = carbon_get_post_meta($commission_request->ID, "status");
    if ($status === "payment_completed") {
        $completed_commission_request[] = $commission_request;
    }

    if ($status !== "payment_completed") {
        $completed_commission_request[] = $commission_request;
    }

}
$on_going_contracts = ProfileUser::get_instance()->get_contracts(["accepted", "finishing", "finished"]);

$total_incomes = Deposit::get_instance()->calculate_total_incomes();


$language = wp_get_post_terms($commercial_agent->ID, 'language');
$languages = !empty($language) ? array_map(function ($lang) {
    return esc_html($lang->name);
}, $language) : [];
$location = wp_get_post_terms($commercial_agent->ID, 'location');
$skills = wp_get_post_terms($commercial_agent->ID, 'skill');
$industry = wp_get_post_terms($commercial_agent->ID, 'industry');
$selling_method = wp_get_post_terms($commercial_agent->ID, 'selling_method');
$seller_type = wp_get_post_terms($commercial_agent->ID, 'seller_type');
$years_of_experience = carbon_get_post_meta($commercial_agent->ID, "years_of_experience");

?>
<section class="commercial-agent-item bg-gray dashboard ">
    <div class="seller-cover" style="background-image:url(<?php echo get_template_directory_uri() . "/assets/img/breadcrumb-bg.jpg"; ?>);"></div>
    <div class="container pt-4">

        <?php

        $template = 'templates/dashboard/commercial-agent/agent-profile.php';
        if (locate_template($template)) {
            include locate_template($template);
        }
        ?>
            <div class="row pb-4">
                <div class="col-md-8">
                    <div class="card">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-info active" id="pills-about-tab" data-bs-toggle="pill" data-bs-target="#pills-about" type="button" role="tab" aria-controls="pills-about" aria-selected="true">About Me</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-info" id="pills-review-tab" data-bs-toggle="pill" data-bs-target="#pills-review" type="button" role="tab" aria-controls="pills-review" aria-selected="false">Reviews</button>
                    </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-about" role="tabpanel" aria-labelledby="pills-about-tab">
                            <?php echo $commercial_agent->post_content; ?>
                        </div>
                 
                        <div class="tab-pane fade" id="pills-review" role="tabpanel" aria-labelledby="pills-review-tab">
                            <div class="reviews">
                            <?php 
                                    $reviews = new WP_Query([
                                        'post_type'  => 'review',
                                        'meta_query' => 
                                            [
                                                'key'   => 'commercial_agent',
                                                'value' => $commercial_agent_id,
                                                'compare' => '='
                                            ]
                                    ]);

                                    
                                    foreach($reviews->posts as $review):
                                    $company_id = carbon_get_post_meta($review->ID,"company");
                                    $company_avatar = get_the_post_thumbnail($company_id, [30, 30], [
                                        'class' => 'attachment-250x250 size-250x250 rounded-circle',
                                        "decoding" => "async",
                                    ]);
                                    $user_id = get_post_meta($company_id,"_user_id");
                                  
                                    $user = get_user_by("ID",$user_id[0]);
    
                                    ?>


                                        <div class="comment mb-4 card">
                                            <div class="user d-flex align-items-center">
                                            <?php echo $company_avatar;?>
                                            <p class="mt-0 mb-0 ms-2"><?php echo "$user->first_name $user->last_name";?> - <?php echo get_the_title($company_id);?></p>
                                            </div>
                                            <div class="meta d-flex align-items-center">
                                           
                                            <?php $score = carbon_get_post_meta($review->ID, "score");
                                            $score_text = "";
                                            for ($i = 0; $i < $score; $i++) {
                                                $score_text .= '<span class="fa fa-star filled" style="  color: #ffc000;"></span>';
                                            }
                                            
                                            echo $score_text;?>
                                             <small class="text-muted mb-0 ms-2"><?php echo Helper::get_human_time_diff($review->post_date) . " ago";?></small>
                                            </div>
                                           
                                            <div class="media-body">
                                                
                                                <?php echo $review->post_content;?>
                                                
                                            </div>
                                        </div>
                                    <?php
                                    endforeach;?>
                                        
                                    
                                    </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-md-4 position-relative card">
                <aside id="secondary" class="widget-area">
                <?php

                $template = 'templates/dashboard/commercial-agent/agent-detail.php';
                if (locate_template($template)) {
                    include locate_template($template);
                }
                ?>
			    </aside>
            </div>
        </div>
    </div>
</section>
<?php 
$company = ProfileUser::get_instance()->get_user_associated_post_type();
if(in_array("company",$current_user->roles) && $company): 
?>
<div class="modal fade" id="rating" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="chatModalLabel">Send a rating</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                
                                                <form id="rating-form">
                                                    
                                                    <input type="hidden" name="action" value="submit_rating">
                                                    <input type="hidden" name="commercial_agent" value="<?php echo esc_attr($commercial_agent->ID); ?>">
                                                    <input type="hidden" name="company" value="<?php echo esc_attr($company->ID); ?>">
                                                    <div class="rating-stars">
                                                        <span class="star" data-value="1">&#9733;</span>
                                                        <span class="star" data-value="2">&#9733;</span>
                                                        <span class="star" data-value="3">&#9733;</span>
                                                        <span class="star" data-value="4">&#9733;</span>
                                                        <span class="star" data-value="5">&#9733;</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                            <label for="content" class="form-label">Content:</label>
                                                            <div class="editor-container" data-target="content"></div>
                                                            <input type="hidden" id="content" name="content">
                                                            <div class="error-message"></div>
                                                    </div>

                                                    <input type="hidden" id="rating-score" name="score" value="">
                                                    
                                                    <button type="submit" class="btn btn-primary">Submit Rating</button>
                                                </form>
                                             
                                                </div>
                                            </div>
                                        </div>
                                    </div>

<?php
endif;
ob_end_flush();
get_footer("dashboard");
?>






