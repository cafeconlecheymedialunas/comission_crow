<?php 
// Verificamos si la variable $associated_post está definida y no es nula
if (isset($associated_post) && $associated_post) {
    // Obtenemos la moneda asociada al post
    $currency = wp_get_post_terms($associated_post->ID, "currency");

    // Verificamos si $currency no está vacía, es un array y tiene al menos un elemento
    if (!empty($currency) && is_array($currency) && isset($currency[0])) {
        $currency_id = $currency[0]->term_id;
        $exchange_rate = carbon_get_term_meta($currency_id, "currency_exhange_rate");

        // Si existe una tasa de cambio válida, mostramos el botón con la información
        if (!empty($exchange_rate)) :
            ?>
            <button type="button" class="operation ms-2" 
                    data-bs-toggle="tooltip" data-bs-html="true" 
                    title="This value may vary according to the exchange rate of your currency against the USD (1 USD = <?php echo esc_html($exchange_rate); ?>). The platform operates in USD. Values in other currencies are for illustrative purposes only and may not reflect the exact amount you will be billed or receive.">
                <i class="fa-solid fa-circle-info text-primary"></i>
            </button>
            <?php
        endif;
    }
}
?>
