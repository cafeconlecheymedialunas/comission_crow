<?php
/**
 * Template Name: Opportunity
 * Description: Page template for Authentificated Company Agent Users.
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
							                        <h2><?php echo $current_user->first_name . " " .$current_user->last_name;?></h2>
							                        <h3><?php echo $current_user->email;?></h3>
							                    </div>
							                    <?php include get_template_directory() . '/templates/dashboard/menu.php';?>
							                </div>
							                <div class="col-md-9">
						                    <div class="card mb-4">
						                        <h2 class="mb-0">Create Opportunity</h2>
						                    </div>
						                        <div class="row">
						                            <div class="col-md-12">
														<div class="card">
														<?php
                        
                                                            // Es un commercial_agent, cargar el formulario de perfil de agente
                                                        include get_template_directory() . '/templates/opportunity/create.php';
                                                        
                                                        
    ?>

													</div>

					                            </div>
					                           
					                        </div>
					                    </div>
						            </div>
						        </div>
						    </div>
						<?php else: ?>
    <p>You need to <a class="btn btn-link" href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a> to access the dashboard.</p>
<?php endif;?>

<?php get_footer();?>
