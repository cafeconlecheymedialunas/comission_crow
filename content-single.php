<?php
/**
 * The template for displaying content in the single.php template.
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
    if (has_post_thumbnail()) :
        echo '<div class="post-thumbnail">' . get_the_post_thumbnail(get_the_ID(), 'large') . '</div>';
    endif;?>

<div class="entry-content">
		
    
   <?php if ('post' === get_post_type()) :
        ?>
    <div class="entry-meta">
        <?php comission_crow_article_posted_on(); ?>
    </div><!-- /.entry-meta -->
<?php
    endif;


the_content();

wp_link_pages([ 'before' => '<div class="page-link"><span>' . esc_html__('Pages:', 'comission_crow') . '</span>', 'after' => '</div>' ]);
?>
	</div><!-- /.entry-content -->

	<?php
edit_post_link(__('Edit', 'comission_crow'), '<span class="edit-link">', '</span>');
?>

	<footer class="entry-meta">
		<hr>
		<?php
        /* translators: used between list items, there is a space after the comma */
        $category_list = get_the_category_list(__(', ', 'comission_crow'));

/* translators: used between list items, there is a space after the comma */
$tag_list = get_the_tag_list('', __(', ', 'comission_crow'));
if ('' != $tag_list) :
    $utility_text = __('This entry was posted in %1$s and tagged %2$s by <a href="%6$s">%5$s</a>. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'comission_crow');
elseif ('' != $category_list) :
    $utility_text = __('This entry was posted in %1$s by <a href="%6$s">%5$s</a>. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'comission_crow');
else :
    $utility_text = __('This entry was posted by <a href="%6$s">%5$s</a>. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'comission_crow');
endif;

printf(
    $utility_text,
    $category_list,
    $tag_list,
    esc_url(get_the_permalink()),
    the_title_attribute([ 'echo' => false ]),
    get_the_author(),
    esc_url(get_author_posts_url((int) get_the_author_meta('ID')))
);
?>
		<hr>
		<?php
    get_template_part('author', 'bio');
?>
	</footer><!-- /.entry-meta -->
</article><!-- /#post-<?php the_ID(); ?> -->
