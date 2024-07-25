<?php
/**
 * Template Name: Page (Default)
 * Description: Page template with Sidebar on the left side.
 *
 */

get_header();

the_post();
?>
 <h1>Purchase your new kit</h1>
  <!-- Paste your embed code script here. -->
  <script
    async
    src="https://js.stripe.com/v3/buy-button.js">
  </script>
  <stripe-buy-button
    buy-button-id='{{BUY_BUTTON_ID}}'
    publishable-key="pk_test_51OuK5YKEo9Rkz0Ya0BQkqPPgH6AcSXrR6Jkpk9ZFlTNRM86QfhzUAM5UHqLiKjapPjgNPJM8z8PLxI2IqOoVTx9R00qyCHDnn9"
  >
  </stripe-buy-button>
<div class="row">
	<div class="col-md-8 order-md-2 col-sm-12">
		<div id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php
          
                the_content();

wp_link_pages(
    [
        'before'   => '<nav class="page-links" aria-label="' . esc_attr__('Page', 'comission_crow') . '">',
        'after'    => '</nav>',
        'pagelink' => esc_html__('Page %', 'comission_crow'),
    ]
);
edit_post_link(
    esc_attr__('Edit', 'comission_crow'),
    '<span class="edit-link">',
    '</span>'
);
?>
		</div><!-- /#post-<?php the_ID(); ?> -->
		<?php
// If comments are open or we have at least one comment, load up the comment template.
if (comments_open() || get_comments_number()) {
    comments_template();
}
?>
	</div><!-- /.col -->
	<?php
get_sidebar();
?>
</div><!-- /.row -->


<?php
get_footer();
