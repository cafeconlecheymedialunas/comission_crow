<?php

get_header();

the_post();
?>


		<div id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
            <div class="section hero">
            <div class="row">
                <div class="col-md-6">
                <div class="elementor-widget-container">
                    <section class="banner text-start">
                    <div class="banner-content">
                    <h1 style="color: #FFFFFF ">
                        Find your commission-only sales agents          </h1>
                    <p style="color: #FFFFFF ">Connecting businesses with top sales talent, risk-free.</p>
                    </div>
                            <div class="brands_form">
                                <form class="prolancer-select-search" method="GET" action="https://nexfyapp-cp167.wordpresstemporal.com/subcarpeta/">
                                <input type="text" name="s" placeholder="Search for...">
                                <select name="post_type" class="form-select">
                                    <option value="services" selected="">Services</option>
                                    <option value="projects">Projects</option>
                                    <option value="sellers">Talent</option>
                                </select>
                                <input type="submit" value="Search">
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="" alt="">
                </div>
            </div>
            </div>
			
			<?php

        edit_post_link(
            esc_attr__('Edit', 'comission_crow'),
            '<span class="edit-link">',
            '</span>'
        );
        ?>
		</div><!-- /#post-<?php the_ID(); ?> -->


<?php
get_footer();
