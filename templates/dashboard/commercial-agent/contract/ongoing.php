<?php

$contracts = ProfileUser::get_instance()->get_contracts(["accepted","finishing"]);

$current_user = wp_get_current_user();
?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("Ongoing contracts"); ?></h2>
 
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-container">
            <?php $template_path = 'templates/dashboard/table-contract.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}


?>
            </div>
        </div>
    </div>
</div>





<?php $template_path = 'templates/dashboard/form-commission-request.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}


?>

<?php wp_reset_postdata(); ?>

