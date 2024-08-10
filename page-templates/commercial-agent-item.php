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
$current_user = wp_get_current_user();


$commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();

$completed_commission_request = [];
foreach ($commission_requests as $commission_request) {
    $status = carbon_get_post_meta($commission_request->ID, "status");
    if ($status === "payment_completed") {
        $completed_commission_request[] = $commission_request;
    }

}
$on_going_contracts = ProfileUser::get_instance()->get_contracts(["accepted", "finishing", "finished"]);

$total_incomes = Deposit::get_instance()->calculate_total_income();


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
<section class="pb-95 bg-gray dashboard">
    <div class="seller-cover" style="background-image:url(<?php echo get_template_directory_uri() . "/assets/img/breadcrumb-bg.jpg"; ?>);"></div>
    <div class="container">
        <div class="seller-profile mb-5">
                <div class="row">
                    <div class="col-xl-2 my-auto">
                    <?php echo get_the_post_thumbnail($commercial_agent->ID, [250, 250], [
    'class' => 'attachment-250x250 size-250x250',
    "decoding" => "async",
]);
?>
                    </div>
                    <div class="col-xl-10 my-auto">
                        <div class="row">
                            <div class="col-xl-7 my-auto">
                                <h3 class="mb-0"><?php echo "$commercial_agent_user->first_name $commercial_agent_user->last_name"; ?></h3>

                                <ul class="list-inline mt-2 mb-2 badges">
                                    <li class="list-inline-item"><img src="https://nexfyapp-cp167.wordpresstemporal.com/subcarpeta/wp-content/uploads/2022/01/level3.png" title="New member" alt="badge"></li>
                                    <li class="list-inline-item"><img src="https://themebing.com/wp/prolancer/wp-content/uploads/2022/01/level2.png" title="Seller Level 2: Has sold 100+ Amount On Nexfy" alt="badge"></li>
                                </ul>
                                <h6 class="mb-0 mt-2"></h6>
                                <p>Member since <?php echo $commercial_agent_user->user_registered; ?></p>
                                <div class="stats-list">
                                    <?php ?>
                                    <div class="stats">
                                        <span><?php echo Helper::format_price($total_incomes); ?></span> in  earnings</div>
                                    <div class="stats">
                                        <span><?php echo count($on_going_contracts); ?></span> On Going Contracts</div>
                                    <div class="stats">
                                        <span><?php echo count($completed_commission_request); ?></span>Completed Orders                                 </div>
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <ul class="list-unstyled mid-meta">
                                    <li class="list-inline-item">
                                        <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#chat-modal" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                            Send Message
                                        </button>
                                    </li>
                                    <div class="modal fade" id="chat-modal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php echo do_shortcode('[better_messages_user_conversation user_id="' . $commercial_agent_user->ID . '"]'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $args = [
                                        'post_type' => 'company',
                                        'meta_query' => [
                                            [
                                                'key' => 'user_id',
                                                'value' => $current_user->ID,
                                                'compare' => '=',
                                            ],
                                        ],
                                        'posts_per_page' => -1,
                                    ];
                                    $query = new WP_Query($args);
                                
                                    $company = $query->posts[0];
                                     if(in_array("company",$current_user->roles) && $company):
                                       
                                     ?>
                                    <li class="list-inline-item">
                                        <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#rating" data-user-id="<?php echo esc_attr($user_commercial_agent->ID); ?>">
                                            Send a Rating
                                        </button>
                                    </li>
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
                                    <?php endif;?>
                                 
                                </ul>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-about-tab" data-bs-toggle="pill" data-bs-target="#pills-about" type="button" role="tab" aria-controls="pills-about" aria-selected="true">About Me</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Contact</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-review-tab" data-bs-toggle="pill" data-bs-target="#pills-review" type="button" role="tab" aria-controls="pills-review" aria-selected="false">Reviews</button>
                    </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-about" role="tabpanel" aria-labelledby="pills-about-tab">
                            <?php echo $commercial_agent->post_content; ?>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

                        </div>
                        <div class="tab-pane fade" id="pills-review" role="tabpanel" aria-labelledby="pills-review-tab">

                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-xl-4 position-relative card">
                <aside id="secondary" class="widget-area">
                	<div id="prolancer_seller_details-1" class="widget widget_prolancer_seller_details">
                        <h4 class="widget-title">About Me</h4>
						<div class="seller-detail d-flex">
				            <i class="fa fa-venus-mars"></i>
				            <div>
                                <h5>Selling Methods</h5>
                                <p><?php echo $selling_method[0]->name;?></p>
				            </div>
			            </div>

						<div class="seller-detail d-flex">
				            <i class="fa fa-user-shield"></i>
				            <div>
					            <h5>Seller Type</h5>
					            <p><?php echo $seller_type[0]->name;?></p>
				            </div>
			            </div>

						<div class="seller-detail d-flex">
				            <i class="fa fa-compass"></i>
				            <div>
                                <h5>Location</h5>
                                <p><?php echo $location[0]->name;?></p>
				            </div>
			            </div>

                        <div class="seller-detail d-flex">
                            <i class="fa fa-industry"></i>
				            <div>
                                <h5>Industry</h5>
                                <p><?php echo $industry[0]->name;?></p>
				            </div>
			            </div>

                        <div class="seller-detail d-flex">
                            <i class="fa fa-calendar"></i>
				            <div>
                                <h5>Years of experience</h5>
                                <p><?php echo $years_of_experience;?></p>
				            </div>
			            </div>




						<div class="seller-skills mt-5">
					        <h4 class="text-center">Skills</h4>

						    <div class="skill-item">
                                <?php foreach($skills as $skill):?>
							    <span><?php echo $skill->name;?></span>
                                <?php endforeach;?>
						    </div>
						</div>

			        </div>
			    </aside>
            </div>
        </div>
    </div>
</section>


<?php
ob_end_flush();
get_footer();
?>






