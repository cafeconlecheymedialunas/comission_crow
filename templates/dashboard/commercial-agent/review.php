<?php

$post = ProfileUser::get_instance()->get_user_associated_post_type();



$args = [
    'post_type'   => 'review',
    'meta_query'  => [
        [
            'key'     => "commercial_agent",
            'value'   => $post->ID,
            'compare' => '=',
        ],
    ],
];
$query = new Wp_Query($args);
$reviews = $query->posts;
?>
<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("Received Reviews"); ?></h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="table-container">
            <?php $template_path = 'templates/dashboard/table-review.php';
                if (locate_template($template_path)) {
                    include locate_template($template_path);
                }

                ?>
            </div>
        </div>
    </div>
</div>

