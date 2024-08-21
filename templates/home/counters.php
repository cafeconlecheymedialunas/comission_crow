   <!-- Counters Section -->
   <?php if($counters):?>
    <div class="counters">
        <div class="container">
            <div class="row">
                <?php foreach ($counters as $counter): ?>
                    <div class="counter col-md-3">
                      
                            <span class="icon">
                                <?php if ($counter["icon"]) {
                                    echo $counter["icon"];
                                }?>
                            </span>
                            <div class="content text-center">
                                <div class="count-container">
                                <?php if ($counter['counter']): ?>
                                    <span class="count mb-2" data-count="<?php echo esc_attr($counter["counter"]); ?>">
                                        <span>0</span>
                                    </span>
                                 
                                <?php endif;?>
                                <?php if ($counter['counter_unit']): ?>
                                            <span><?php echo esc_html($counter["counter_unit"]); ?></span>
                                        <?php endif;?>
                                </div>
                               
                                <?php if ($counter['title']): ?>
                                    <p class="title mb-2"><?php echo esc_html($counter["title"]); ?><p/>
                                <?php endif;?>
                            </div>
                        
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
    <?php endif;?>