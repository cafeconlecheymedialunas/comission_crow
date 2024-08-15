<?php
/**
 * The template for displaying content in the index.php template.
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class("blog-item"); ?>>
	<?php
		if (has_post_thumbnail()) {
			echo '<div class="post-thumbnail">' . get_the_post_thumbnail(get_the_ID(), 'large') . '</div>';
		}
	?>
	
	<div class="content">
		<div class="entry-content">
		<div class="entry-meta">
					<?php
					comission_crow_article_posted_on();

					$num_comments = get_comments_number();
					if (comments_open()) :
						echo ' <a href="' . esc_url(get_comments_link()) . '" title="' . esc_attr(sprintf(_n('%s Comment', '%s Comments', $num_comments, 'comission_crow'), $num_comments)) . '">' . $num_comments . ' Comments</a>';
					endif;
					?>
				</div><!-- /.entry-meta -->
			<h2 class="title">
				<a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'comission_crow'), the_title_attribute([ 'echo' => false ])); ?>" rel="bookmark">
					<?php
					// Limitar el título a 10 palabras
					$trimmed_title = wp_trim_words(get_the_title(), 10, '...');
					echo esc_html($trimmed_title);
					?>
				</a>
			</h2>

			<div class="post-content">
				<?php
				if (is_search()) {
					the_excerpt();
				} else {
					// Limitar el contenido a 30 palabras
					$trimmed_content = wp_trim_words(get_the_content(), 30, '...');
					echo '<div>' . $trimmed_content . '</div>';
				}
				?>
			</div><!-- /.post-content -->

				
		

			<?php wp_link_pages([ 'before' => '<div class="page-link"><span>' . esc_html__('Pages:', 'comission_crow') . '</span>', 'after' => '</div>' ]); ?>
		</div><!-- /.entry-content -->

		<footer class="footer">
			<a href="<?php the_permalink(); ?>" class="btn btn-info"><?php esc_html_e('Read more', 'comission_crow'); ?></a>
		</footer><!-- /.footer -->
	</div><!-- /.content -->

</article><!-- /#post-<?php the_ID(); ?> -->
