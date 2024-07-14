<?php
/**
 * Template Name: Dashboard
 * Description: Page template for Authentificated Company Agent Users.
 *
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}



get_header("dashboard");
$subpage = get_query_var('subpage');
if (is_user_logged_in()):
    $current_user = wp_get_current_user();?>
							    <div class="dashboard">
							        <div class="container-fluid">
							            <div class="row">
							                <div class="col-md-3">
							                    <div class="user-profile card mb-4">
							                        <img src="" alt="">
							                        <h2><?php echo $current_user->display_name;?></h2>
							                        <h3><?php echo $current_user->email;?></h3>
							                    </div>
							                    <?php include get_template_directory() . '/templates/dashboard/menu.php';?>
							                </div>
							                <div class="col-md-9">
											<?php if($subpage):
											    $subpage_title = ucfirst(str_replace('-', ' ', $subpage));?>
												<div class="card mb-4">
													<h2 class="mb-0"><?php echo esc_html($subpage_title);?></h2>
												</div>
											<?php endif;?>
						                 
														<?php
            
                                                        
                                                        
											            if ($subpage) {

											                $template_path = 'templates/dashboard/dashboard-' . $subpage . '.php';
											                if (locate_template($template_path)) {
											                    include(locate_template($template_path));
											                } else {
											                    // Página no encontrada
											                    echo '<h1>Página no encontrada</h1>';
											                }
											                // Cargar contenido específico para cada subpantalla
                    
											                // Añade más subpantallas según sea necesario
											            } else {
											                // Cargar contenido predeterminado del dashboard
											                if (have_posts()) :
											                    while (have_posts()) : the_post();
											                        the_content();
											                    endwhile;
											                endif;
											            }
?>

							
					                    </div>
						            </div>
						        </div>
						    </div>
						<?php else: ?>
    <p>You need to <a class="btn btn-link" href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a> to access the dashboard.</p>
<?php endif;?>

<?php get_footer();?>
