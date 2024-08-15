<?php
/**
 * The Template for displaying Search Results pages.
 */

get_header();

?>
<div class="container">
<?php

if (have_posts()) :?>
	<div class="blog-post row">
    <div class="col-md-8">
	<?php
	get_template_part('archive', 'loop');
?>
 </div>
	
	<?php
		get_sidebar();
	?>
	



</div><!-- /.col -->
<?php
else :
    ?>
	<article id="post-0" class="post no-results not-found">
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e('Nothing Found', 'comission_crow'); ?></h1>
		</header><!-- /.entry-header -->
		<p><?php esc_html_e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'comission_crow'); ?></p>
		<?php
                get_search_form();
    ?>
	</article><!-- /#post-0 -->
<?php
endif;
?>
</div>
<?php
wp_reset_postdata();

get_footer();
