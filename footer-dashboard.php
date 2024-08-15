
		<footer id="footer">
			<div class="container">
				
                <div class="row">
						<p class="copy"><?php printf(esc_html__('&copy; %1$s %2$s. All rights reserved.', 'comission_crow'), wp_date('Y'), get_bloginfo('name', 'display')); ?></p>
					</div>
			</div><!-- /.container -->

		</footer><!-- /#footer -->
	</div><!-- /#wrapper -->
	<?php
        wp_footer();

$spinner_template = 'templates/spinner.php';
if (locate_template($spinner_template)) {
    include locate_template($spinner_template);
}
?>


</body>
</html>
