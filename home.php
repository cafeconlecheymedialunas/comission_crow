<?php
/**
 * Template Name: Blog Index
 * Description: The template for displaying the Blog index /blog.
 *
 */

get_header();

$page_id = get_option('page_for_posts');
?>

<div class="container">



	<div class="post_content">
		<?php
            echo apply_filters('the_content', get_post_field('post_content', $page_id));

edit_post_link(__('Edit', 'comission_crow'), '<span class="edit-link">', '</span>', $page_id);
?>
	</div><!-- /.col -->
	<div class="blog-post row">
    
            <?php
            get_template_part('archive', 'loop');
        ?>
         
            
            <?php
                get_sidebar();
            ?>
            
     
	
      
	</div><!-- /.col -->
</div>
<?php

get_footer();
