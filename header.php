<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
	<?php wp_head(); ?>
</head>

<?php
    $navbar_scheme   = get_theme_mod('navbar_scheme', 'navbar-light bg-light'); // Get custom meta-value.
$navbar_position = get_theme_mod('navbar_position', 'static'); // Get custom meta-value.

$search_enabled  = get_theme_mod('search_enabled', '1'); // Get custom meta-value.
?>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a href="#main" class="visually-hidden-focusable"><?php esc_html_e('Skip to main content', 'comission_crow'); ?></a>

<div id="wrapper">
	<header>
		<nav id="header" class="navbar navbar-expand-md <?php echo esc_attr($navbar_scheme);
if (isset($navbar_position) && 'fixed_top' === $navbar_position) : echo ' fixed-top';
elseif (isset($navbar_position) && 'fixed_bottom' === $navbar_position) : echo ' fixed-bottom'; endif;
if (is_home() || is_front_page()) : echo ' home'; endif; ?>">
			<div class="container">
				<a class="navbar-brand" href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
					<?php
                        $header_logo = get_theme_mod('header_logo'); // Get custom meta-value.

if (! empty($header_logo)) :
    ?>
						<img src="<?php echo esc_url($header_logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" />
					<?php
else :
    echo esc_attr(get_bloginfo('name', 'display'));
endif;
?>
				</a>

				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'comission_crow'); ?>">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div id="navbar" class="collapse navbar-collapse">
					<?php
    // Loading WordPress Custom Menu (theme_location).
    wp_nav_menu(
        [
            'menu_class'     => 'navbar-nav me-auto',
            'container'      => '',
            'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
            'walker'         => new WP_Bootstrap_Navwalker(),
            'theme_location' => 'main-menu',
        ]
    );


?>
							 
								<ul class="navbar-nav">
									<?php if (!is_user_logged_in()): ?>
										<a class="nav-link" href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a>
										<a class="nav-link" href="<?php echo esc_url(home_url("/auth?action=register&role=commercial_agent")); ?>">Register as a Agent</a>
										<a class="nav-link" href="<?php echo esc_url(home_url("/auth?action=register&role=company")); ?>">Register as a Company</a>
									<?php else: ?>
										<li class="nav-item"><a class="nav-link" href="<?php echo site_url('/dashboard'); ?>">Dashboard</a></li>
										<li><a href="<?php echo site_url('/dashboard/overview'); ?>">Overview</a></li>
										<li><a href="<?php echo site_url('/dashboard/profile'); ?>">Profile</a></li>
										<li><a href="<?php echo site_url('/dashboard/settings'); ?>">Settings</a></li>
										<li class="nav-item"><a class="nav-link" href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
									<?php endif; ?>
								</ul>
							
					<?php

?>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container -->
		</nav><!-- /#header -->
	</header>

	<main id="main" class="container"<?php if (isset($navbar_position) && 'fixed_top' === $navbar_position) : echo ' style="padding-top: 100px;"';
	elseif (isset($navbar_position) && 'fixed_bottom' === $navbar_position) : echo ' style="padding-bottom: 100px;"'; endif; ?>>
		<?php
	        // If Single or Archive (Category, Tag, Author or a Date based page).
	        if (is_single() || is_archive()) :
	            ?>
			<div class="row">
				<div class="col-md-8 col-sm-12">
		<?php
	        endif;
?>
