<?php

$payments = ProfileUser::get_instance()->get_payments_for_user();
$commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user()



?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("Payments"); ?></h2>
    <?php if(in_array("company", $current_user->roles)):?>
        <a hre="<?php echo $dasboard->get_role_url_link_dashboard_page("payment_create");?>" class="btn btn-primary btn-sm">Add new</button>
    <?php endif;?>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-container">
            <?php $template_path = 'templates/dashboard/table-payment.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}

?>
            </div>
        </div>
    </div>
</div>








<?php wp_reset_postdata(); ?>

