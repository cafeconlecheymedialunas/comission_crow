<?php

$company = Company::get_instance();
$company_post = $company->get_company();

$contracts = $company->get_contracts();

$current_user = wp_get_current_user();

?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Received commission requests"); ?></h2>
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




<?php $template_path = 'templates/dashboard/form-contract.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}


?>
<?php $template_path = 'templates/dashboard/form-commission-request.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}


?>

<?php wp_reset_postdata(); ?>

