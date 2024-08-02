<?php

$commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();


$contracts = ProfileUser::get_instance()->get_contracts(["accepted","finishing"]);
?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("Sended commission requests"); ?></h2>
    <?php if(in_array("commercial_agent", $current_user->roles)):?>
        <button class="btn btn-primary commission-request-button btn-sm" data-bs-toggle="modal" data-bs-target="#modal-commission">Add new</button>
    <?php endif;?>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive">
            <?php $template_path = 'templates/dashboard/table-commission.php';
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

