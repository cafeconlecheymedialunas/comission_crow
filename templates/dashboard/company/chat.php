<div class="card mb-4 flex-row d-flex justify-content-between align-items-center">
    <h2 class="mb-0 d-inline"><?php echo __("Chats"); ?></h2>
</div>
<div class="row">
    <div class="col-md-12">
       
<div class="card">
    <?php echo do_shortcode('[better_messages user_ids="78"]'); ?>
</div>
</div>
</div>
<?php echo do_shortcode('[better_messages_pm_button user_id="-1" text="Private Message" subject="Have a question to you" message="Lorem Ipsum is simply dummy text." target="_self" fast_start="0" url_only="0"]'); ?>
