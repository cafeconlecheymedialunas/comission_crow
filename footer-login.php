
	
	
    <?php
        wp_footer();
    $spinner_template = 'templates/spinner.php';
    if (locate_template($spinner_template)) {
        include locate_template($spinner_template);
    }
    ?>
</body>
</html>
