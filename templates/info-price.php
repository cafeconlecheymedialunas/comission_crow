<?php 
$associated_post = ProfileUser::get_instance()->get_user_associated_post_type();

$currency = wp_get_post_terms($associated_post->ID,"currency");


$exchange_rate = carbon_get_term_meta($currency[0]->term_id,"currency_exhange_rate");

if(!empty($currency) && !empty($exchange_rate)):?>

<button type="button" class="operation ms-2" 
        data-bs-toggle="tooltip" data-bs-html="true" 
        title="This value may vary according to the exchange rate of your currency against the USD (1 USD = <?php echo $exchange_rate;?>). The platform operates in USD. Values in other currencies are for illustrative purposes only and may not reflect the exact amount you will be billed or receive.">
        <i class="fa-solid fa-circle-info text-primary"></i>
    </button>
<?php endif;?>