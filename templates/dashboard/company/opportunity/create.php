<?php



?>
<div class="card mb-4">
	<h2 class="mb-0"><?php echo __("Create Opportunity"); ?></h2>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card">
        <?php $template_path = 'templates/dashboard/form-opportunity.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}
?>
        </div>
    </div>
</div>


