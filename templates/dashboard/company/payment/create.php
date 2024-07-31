
<?php


$commission_request = ProfileUser::get_instance()->get_commission_request_for_current_user($_GET["commission_request_id"]);



if($commission_request):

    ?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Pay"); ?></h2>

</div>
<div class="row">
	<div class="col-md-12">
	    <div class="card">
        <?php $template_path = 'templates/dashboard/form-payment.php';
    if (locate_template($template_path)) {
        include locate_template($template_path);
    }

    ?>
      </div>
    </div>
  </div>
<?php else:?>

<div class="alert alert-danger" role="alert">
  This commission Request Does not Exist
</div>

<?php endif;?>

