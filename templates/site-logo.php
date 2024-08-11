<?php
		if (has_custom_logo()) {
			$image = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
			?>
																<img src="<?php echo $image[0]; ?>" alt="Brand" class="site-logo img-fluid">
																<?php
		} else {
			// O simplemente muestra el nombre del sitio
			echo esc_attr(get_bloginfo('name', 'display'));
		}
		?>