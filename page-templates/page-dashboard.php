<?php
/**
 * Template Name: Dashboard
 * Description: Page template with no sidebar.
 *
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}



get_header("dashboard");

if (is_user_logged_in()):
    $current_user = wp_get_current_user();?>
							    <div class="dashboard">
							        <div class="container-fluid">
							            <div class="row">
							                <div class="col-md-3">
							                    <div class="user-profile card mb-4">
							                        <img src="" alt="">
							                        <h2>nicoreyes7</h2>
							                        <h3>nicoreyes7@hotmail.com</h3>
							                    </div>
							                    <?php include get_template_directory() . '/templates/dashboard/menu.php';?>
							                </div>
							                <div class="col-md-9">
						                    <div class="card mb-4">
						                        <h2 class="mb-0">Seller Profile</h2>
						                    </div>
						                        <div class="row">
						                            <div class="col-md-8">
														<div class="card">
														<?php include get_template_directory() . '/templates/dashboard/profile-form.php';?>
													</div>

					                            </div>
					                            <div class="col-md-4">
					                               <div class="card">
												   	<?php include get_template_directory() . '/templates/dashboard/form-password.php';?>
												   </div>
					                            </div>
					                        </div>
					                    </div>
						            </div>
						        </div>
						    </div>
						<?php else: ?>
    <p>You need to <a href="/login">login</a> to access the dashboard.</p>
<?php endif;?>

<?php get_footer();?>
