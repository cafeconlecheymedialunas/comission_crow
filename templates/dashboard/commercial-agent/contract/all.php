<?php

$post_type = get_user_associated_post_type();



$args = [
    'post_type'   => 'contract',
    'meta_query'  => [
        [
            'key'     => $post_type->post_type,
            'value'   => $post_type->ID,
            'compare' => '=',
        ],
    ],
];
$query = new Wp_Query($args);

$contracts = $query->posts;




?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("All contracts"); ?></h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Add new
    </button>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive">
            <?php $template_path = 'templates/dashboard/table-contract.php';
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

