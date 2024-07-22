<?php
$deal_id = $_GET['deal_id'];

if (isset($deal_id) && !empty($deal_id)) {
    
    $deal_post = get_post($deal_id);
    ?>
    <div class="card mb-4">
        <h2 class="mb-0"><?php echo __("Edit deal"); ?></h2>
    </div>
    <div class="row">
	    <div class="col-md-12">
		    <div class="card">
                <?php
                $template_path = 'templates/dashboard/form-deal.php';
    if (locate_template($template_path)) {
        include locate_template($template_path);
    }
    ?>
            </div>
        </div>
    </div>
<?php

} else {
    echo "deal does not exists";
}
