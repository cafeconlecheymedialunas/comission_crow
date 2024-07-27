<?php

$commercial_agent = CommercialAgent::get_instance();
$company_post = $commercial_agent->get_commercial_agent();

$contracts = $commercial_agent->get_contracts();

$current_user = wp_get_current_user();

?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("Disputes"); ?></h2>
    <button class="btn btn-primary commission-request-button btn-sm" data-bs-toggle="modal" data-bs-target="#modal-commission">Add new</button>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive">
            <?php $template_path = 'templates/dashboard/table-dispute.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}

?>
            </div>
        </div>
    </div>
</div>




<?php $template_path = 'templates/dashboard/form-contract.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}


?>
<?php $template_path = 'templates/dashboard/form-request-commission.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}


?>

<?php wp_reset_postdata(); ?>

