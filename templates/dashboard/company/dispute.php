<?php

$disputes = ProfileUser::get_instance()->get_disputes_for_user();

?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("Disputes"); ?></h2>
    <?php if(in_array("company", $current_user->roles)):?>
        <button class="btn btn-primary btn-sm" id="open-dispute-modal">Add new</button>
    <?php endif;?>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-container">
            <?php $template_path = 'templates/dashboard/table-dispute.php';
if (locate_template($template_path)) {
    include locate_template($template_path);
}

?>
            </div>
        </div>
    </div>
</div>








<?php wp_reset_postdata(); ?>

