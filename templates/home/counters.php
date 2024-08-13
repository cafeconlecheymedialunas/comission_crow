   <!-- Counters Section -->
   <div class="counters">
        <div class="container">
            <div class="row">
                <?php foreach ($counters as $counter): ?>
                    <div class="counter col-md-3">
                        <div class="border">
                            <span class="icon">
                                <?php if ($counter["icon"]) {
    echo $counter["icon"];
}?>
                            </span>
                            <div class="content text-center">
                                <?php if ($counter['counter']): ?>
                                    <h3 class="count mb-2" data-count="<?php echo esc_attr($counter["counter"]); ?>">
                                        <span>0</span>
                                        <?php if ($counter['counter_unit']): ?>
                                            <span><?php echo esc_html($counter["counter_unit"]); ?></span>
                                        <?php endif;?>
                                    </h3>
                                <?php endif;?>
                                <?php if ($counter['title']): ?>
                                    <p class="title mb-2"><?php echo esc_html($counter["title"]); ?><p/>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>