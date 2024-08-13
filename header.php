<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php 
$current_user = wp_get_current_user();
?>

<a href="#main" class="visually-hidden-focusable"><?php esc_html_e('Skip to main content', 'comission_crow'); ?></a>

<div id="wrapper">
    <header class="desktop-header d-none d-md-block" id="desktop-header">
        <nav id="header" class="navbar navbar-expand-md <?php echo esc_attr($navbar_scheme); 
        if (isset($navbar_position) && 'fixed_top' === $navbar_position) echo ' fixed-top'; 
        elseif (isset($navbar_position) && 'fixed_bottom' === $navbar_position) echo ' fixed-bottom'; ?>">
            <div class="container d-flex justify-content-between align-items-center">
			<a class="navbar-brand" href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
				<?php
				$image = carbon_get_theme_option("site_header_logo");
				$image_url = wp_get_attachment_image_src($image);
				$image_sticky = carbon_get_theme_option("site_header_sticky_logo");
				$image_sticky_url = wp_get_attachment_image_src($image_sticky);

				if (isset($image_url[0])) {
					?>
					<img src="<?php echo esc_url($image_url[0]); ?>" alt="Brand" class="site-logo img-fluid default-logo">
					<?php if (isset($image_sticky_url[0])): ?>
						<img src="<?php echo esc_url($image_sticky_url[0]); ?>" alt="Brand" class="site-logo img-fluid sticky-logo">
					<?php endif; ?>
					<?php
				} else {
					echo esc_attr(get_bloginfo('name', 'display'));
				}
				?>
			</a>


                <div class="mx-auto">
                    <?php
                    wp_nav_menu([
                        'menu_class' => 'navbar-nav',
                        'container' => '',
                        'fallback_cb' => 'WP_Bootstrap_Navwalker::fallback',
                        'walker' => new WP_Bootstrap_Navwalker(),
                        'theme_location' => 'main-menu',
                    ]);
                    ?>
                </div>

                <div class="dropdown">
                    <?php if (!is_user_logged_in()): ?>
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Login / Register
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a></li>
                            <li><a class="dropdown-item" href="<?php echo esc_url(home_url("/auth?action=register&role=commercial_agent")); ?>">Register as an Agent</a></li>
                            <li><a class="dropdown-item" href="<?php echo esc_url(home_url("/auth?action=register&role=company")); ?>">Register as a Company</a></li>
                        </ul>
                    <?php else: ?>
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Dashboard
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
                            <?php $role_url = in_array("commercial_agent", $current_user->roles) ? "commercial-agent" : "company"; ?>
                            <li><a class="dropdown-item" href="<?php echo home_url() . "/dashboard/$role_url/profile"; ?>">Profile</a></li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <header class="offcanvas-menu d-block d-md-none">
        <div class="header-nav-default">
            <a class="navbar-brand" href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
                <?php
                if (has_custom_logo()) {
                    $image = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
                    ?>
                    <img src="<?php echo $image[0]; ?>" alt="Brand" class="site-logo img-fluid">
                    <?php
                } else {
                    echo esc_attr(get_bloginfo('name', 'display'));
                }
                ?>
            </a>
        </div>
        <input type="checkbox" id="toogle-menu" />
        <label for="toogle-menu" class="toogle-open"><span></span></label>

        <nav>
			<div>
			<label for="toogle-menu" class="toogle-close">
                    <span></span>
            </label>
            <div class="image">
                <?php
                if (has_custom_logo()) {
                    $image = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
                    ?>
                    <img src="<?php echo $image[0]; ?>" alt="Brand" class="site-logo img-fluid">
                    <?php
                } else {
                    echo esc_attr(get_bloginfo('name', 'display'));
                }
                ?>
              
            </div>
            <div class="mobile-nav">
                <?php
                wp_nav_menu([
                    'menu_class' => 'navbar-nav',
                    'container' => '',
                    'fallback_cb' => 'WP_Bootstrap_Navwalker::fallback',
                    'walker' => new WP_Bootstrap_Navwalker(),
                    'theme_location' => 'main-menu',
                ]);
                ?>
				
                <div class="dropdown">
                    <?php if (!is_user_logged_in()): ?>
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Login / Register
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a></li>
                            <li><a class="dropdown-item" href="<?php echo esc_url(home_url("/auth?action=register&role=commercial_agent")); ?>">Register as an Agent</a></li>
                            <li><a class="dropdown-item" href="<?php echo esc_url(home_url("/auth?action=register&role=company")); ?>">Register as a Company</a></li>
                        </ul>
                    <?php else: ?>
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Dashboard
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
                            <?php $role_url = in_array("commercial_agent", $current_user->roles) ? "commercial-agent" : "company"; ?>
                            <li><a class="dropdown-item" href="<?php echo home_url() . "/dashboard/$role_url/profile"; ?>">Profile</a></li>
                        </ul>
                    <?php endif; ?>
                </div>
			</div>
			
            </div>
        </nav>
    </header>

    <main id="main" <?php if (isset($navbar_position) && 'fixed_top' === $navbar_position): echo ' style="padding-top: 100px;"';
    elseif (isset($navbar_position) && 'fixed_bottom' === $navbar_position): echo ' style="padding-bottom: 100px;"'; endif; ?>>
        <?php
        if (is_single() || is_archive()):
        ?>
        <div class="row">
            <div class="col-md-8 col-sm-12">
                <?php endif; ?>
