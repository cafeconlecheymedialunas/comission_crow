<?php

$associated_post = get_user_associated_post_type();
$current_user = wp_get_current_user();
?>
<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
	<meta charset="<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
	<?php wp_head();?>
</head>

<body <?php body_class();?>>

<?php wp_body_open();?>
<header class="dashboard-header">
		<div class="container-fluid">
			<div class="d-flex justify-content-between align-items-center">
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
				<?php
?>
               
				
				<div class="menu-account">
					<div class="d-flex jus">
						<?php if(in_array("company", $current_user->roles)):?>
						<span class="balance">123 $</span>
						<?php endif;?>
						
						
	                    <div class="notifications-messages">
	                        	<i class="fas fa-fw fa-envelope"></i>
								<?php echo do_shortcode('[better_messages_unread_counter hide_when_no_messages="0" preserve_space="1"]');?>
	                    </div>
	                    
						<div class="my-account">
						<?php if (is_user_logged_in()) : ?>
							<?php $post_thumbnail  = get_the_post_thumbnail($associated_post->ID, [ 50, 50], [ 'class' => 'rounded-circle' ]); ?>
							<div class="dropdown">
								<span class="dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
								<?php
                                $default = get_template_directory_uri() . "/assets/img/placeholder.png";
						    if ($post_thumbnail) {
						        echo $post_thumbnail;
						    } else {
						        echo '<img width="50" height="50" class="rounded-circle" src="' . $default . '"/>';
						    }
?>
								
								</span>
								<ul class="dropdown-menu" aria-labelledby="accountDropdown">
									<li><a class="dropdown-item" href="<?php echo esc_url(home_url('/dashboard')); ?>">Profile</a></li>
									<li><a class="dropdown-item" href="<?php echo esc_url(wp_logout_url(home_url())); ?>">Logout</a></li>
								</ul>
							</div>
						<?php else : ?>
							<a class="btn btn-primary" href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a>
							<a class="btn btn-secondary" href="<?php echo esc_url(home_url("/auth?action=register")); ?>">Register</a>
						<?php endif; ?>
	                    </div>
                    </div>
				</div>
				

			</div>
		</div>
	</header>