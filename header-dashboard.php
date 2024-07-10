<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
	<meta charset="<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<?php wp_head();?>
</head>

<body <?php body_class();?>>

<?php wp_body_open();?>
<header class="dashboard-header">
		<div class="container-fluid">
			<div class="d-flex justify-content-between">
				<div class="site-link">
                <a href="<?php echo home_url(); ?>" class="custom-logo-link" rel="home">
                <?php
					if (has_custom_logo()) {
						the_custom_logo();
					} else {
						// O simplemente muestra el nombre del sitio
						echo esc_attr(get_bloginfo('name', 'display'));
					}
				?>
                </a>
			    </div>
               
				<div class="menu-account">
					<div class="d-flex jus">
						<span class="balance">123 $</span>
						<div class="notifications-widget">
	                        <div class="notifications-button">
	                        	<i class="fas fa-fw fa-envelope"></i>

	                        </div>
	                    </div>
						<div class="notifications-widget">
	                        <div class="notifications-button">
							<i class="fa-solid fa-bell"></i>
	                        </div>
	                    </div>
						<div class="my-account">
						<?php if (is_user_logged_in()) : ?>
							<?php $current_user = wp_get_current_user(); ?>
							<div class="dropdown">
								<button class="btn btn-secondary dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
									<?php echo esc_html($current_user->display_name); ?>
								</button>
								<ul class="dropdown-menu" aria-labelledby="accountDropdown">
									<li><a class="dropdown-item" href="<?php echo esc_url(home_url('/dashboard')); ?>">Dashboard</a></li>
									<li><a class="dropdown-item" href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Logout</a></li>
								</ul>
							</div>
						<?php else : ?>
							<a class="btn btn-primary" href="<?php echo esc_url(wp_login_url()); ?>">Login</a>
							<a class="btn btn-secondary" href="<?php echo esc_url(wp_registration_url()); ?>">Register</a>
						<?php endif; ?>
	                    </div>
                    </div>
				</div>

			</div>
		</div>
	</header>