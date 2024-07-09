<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
	<meta charset="<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<?php wp_head();?>
</head>

<body <?php body_class();?>>

<?php wp_body_open();?>
<header class="frontend-dashboard-header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xl-2 col-lg-2 col-md-3 col-sm-8 col-8 col-xs-10 my-auto">
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
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-1 col-1 d-none d-md-block my-auto offset-xl-4 offset-lg-3 text-center">
                    <div class="balance-in-navbar">
						<span class="woocommerce-Price-amount amount"><bdi>0,00&nbsp;<span class="woocommerce-Price-currencySymbol">€</span></bdi></span>                    </div>
                </div>
				<div class="col-md-2 col-sm-4 col-4 my-auto">
					<div class="d-flex float-end">
						<div class="notifications-widget">
	                        <div class="notifications-button">
	                        	<i class="fas fa-fw fa-envelope"></i>

	                        </div>
	                        	                    </div>
						<div class="notifications-widget">
	                        <div class="notifications-button">
	                        	<i class="fas fa-fw fa-bell-on"></i>
	                        		                        </div>
	                    		                    </div>
						<div class="my-account-widget">
	                        <div class="my-account-button">
	                        <img width="50" src="<?php echo carbon_get_post_meta(3966, "avatar"); ?>" alt="" style="cursor: default;">	                                	                        </div>
	                        <div class="my-account-content">
	                            <div class="header-profile">
	                                <div class="header-profile-content">
	                                    <h6>nicoreyes7</h6>
	                                    <p>nicoreyes7@hotmail.com</p>
	                                </div>
	                            </div>
	                            <ul class="list-unstyled">
	                            
	                                 <li>
	                                    <a href="<?php echo wp_logout_url() ?>">Logout</a>
	                                </li>
	                            </ul>
	                        </div>
	                    </div>
                    </div>
				</div>

			</div>
		</div>
	</header>