<?php
$opportunity_id = $_GET['opportunity_id'];

if (isset($opportunity_id) && !empty($opportunity_id)) {
    
    $opportunity_post = get_post($opportunity_id);

    ?>
    <div class="card mb-4">
        <h2 class="mb-0"><?php echo __("Edit Opportunity"); ?></h2>
    </div>
    <div class="row">
	    <div class="col-md-12">
		    <div class="card">
                <?php
                $template_path = 'templates/dashboard/form-opportunity.php';
    if (locate_template($template_path)) {
        include locate_template($template_path);
    }
    ?>
            </div>
        </div>
    </div>
<?php

} else {
    echo "Opportunity does not exists";
}
