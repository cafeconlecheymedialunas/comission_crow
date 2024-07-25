<?php

$company = Company::get_instance();
$company_post = $company->get_company();

$agreements = $company->get_agreements(["accepted","finishing"]);

$current_user = wp_get_current_user();

?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Ongoing agreements"); ?></h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Add new
    </button>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive">
            <?php $template_path = 'templates/dashboard/table-agreement.php';
                if (locate_template($template_path)) {
                    include locate_template($template_path);
                }


                ?>
            </div>
        </div>
    </div>
</div>




<?php $template_path = 'templates/dashboard/form-agreement.php';
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

