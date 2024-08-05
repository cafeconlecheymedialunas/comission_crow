<?php

$commission_request_id = isset($_GET["commission_request_id"]) ? intval($_GET["commission_request_id"]) : 0;

if (!$commission_request_id) {
    echo '<div class="alert alert-danger" role="alert">Invalid commission request ID.</div>';
    return;
}

$commission_request = get_post($commission_request_id);

$args = [
    'post_type' => 'payment',
    'meta_query' => [
        [
            'key' => "commission_request_id",
            'value' => $commission_request_id,
            'compare' => '=',
        ],
    ],
    'posts_per_page' => -1,
];
$payment = new WP_Query($args);

if (!$commission_request) {
    echo '<div class="alert alert-danger" role="alert">This commission request does not exist.</div>';
    return;
}

if ($payment->have_posts()) {
    echo '<div class="alert alert-info" role="alert">This commission request has already been paid.</div>';
    return;
}

// Si llegamos aquí, el commission_request existe y no ha sido pagado
$items = carbon_get_post_meta($commission_request_id, 'items');
$contract_id = carbon_get_post_meta($commission_request_id, 'contract_id');
$total_cart = carbon_get_post_meta($commission_request_id, 'total_cart');
$total_agent = carbon_get_post_meta($commission_request_id, "total_agent");
$total_platform = carbon_get_post_meta($commission_request_id, "total_platform");
$total_tax_service = carbon_get_post_meta($commission_request_id, "total_tax_service");
$total_to_pay = carbon_get_post_meta($commission_request_id, "total_to_pay");
if (!$contract_id) {
    echo '<div class="alert alert-danger" role="alert">Contract does not exist.</div>';
    return;
}

$opportunity_id = carbon_get_post_meta($contract_id, 'opportunity');
$company_id = carbon_get_post_meta($contract_id, 'company_id');
$sku = carbon_get_post_meta($contract_id, 'sku');
$minimal_price = carbon_get_post_meta($contract_id, "minimal_price");
$commission = carbon_get_post_meta($contract_id, "commission");

// Verificar la existencia de datos antes de renderizar
$has_opportunity = !empty($opportunity_id);
$has_company = !empty($company_id);
$has_sku = !empty($sku);
$has_minimal_price = !empty($minimal_price);
$has_commission = !empty($commission);

// Obtener el usuario y la ubicación
$user = wp_get_current_user();
$location = get_user_meta($user->ID, 'location', true); // Ejemplo para obtener la ubicación del usuario

?>
<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Pay"); ?></h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="container mt-5" id="commission-request" data-id="<?php echo esc_attr($commission_request_id); ?>">
                <div class="row">
                    <div class="col-md-8 order-md-1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-3">Contract</h4>
                                <?php if ($has_sku): ?>
                                    <p class="fw-bold">Sku #<?php echo esc_html($sku); ?></p>
                                <?php endif;?>
                                <?php if ($user): ?>
                                    <p>Agent: <?php echo esc_html($user->first_name . " " . $user->last_name); ?></p>
                                    <p>Agent email: <?php echo esc_html($user->user_email); ?></p>
                                <?php endif;?>
                                <?php if ($location): ?>
                                    <p>Agent location: <?php echo esc_html($location); ?></p>
                                <?php endif;?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-3">Opportunity</h4>
                                <?php if ($has_opportunity): ?>
                                    <?php if (has_post_thumbnail($opportunity_id)) {
                                        echo get_the_post_thumbnail($opportunity_id);
                                    }?>
                                    <h6><?php echo esc_html(get_the_title($opportunity_id)); ?></h6>
                                <?php endif;?>
                                <?php if ($has_minimal_price): ?>
                                    <p>Minimum price: <?php echo esc_html(Helper::format_price($minimal_price)); ?></p>
                                <?php endif;?>
                                <?php if ($has_commission): ?>
                                    <p>Commission: <?php echo esc_html($commission); ?>%</p>
                                <?php endif;?>
                            </div>
                        </div>
                        <hr class="mb-4">
                        <h4 class="mb-3">Payment</h4>
                        <div class="d-block my-3">
                            <div class="custom-control custom-radio">
                                <input id="stripe" name="paymentMethod" value="stripe" type="radio" class="custom-control-input" checked required>
                                <label class="custom-control-label" for="stripe">Stripe</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="debit" name="paymentMethod" disabled type="radio" class="custom-control-input" required>
                                <label class="custom-control-label" for="debit">Deposit</label>
                            </div>

                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                            <select class="form-control" name="currency" id="currency">
    <option value="USD">US Dollar (USD)</option>
    <option value="AED">United Arab Emirates Dirham (AED)</option>
    <option value="AFN">Afghan Afghani (AFN)</option>
    <option value="ALL">Albanian Lek (ALL)</option>
    <option value="AMD">Armenian Dram (AMD)</option>
    <option value="ANG">Netherlands Antillean Guilder (ANG)</option>
    <option value="AOA">Angolan Kwanza (AOA)</option>
    <option value="ARS">Argentine Peso (ARS)</option>
    <option value="AUD">Australian Dollar (AUD)</option>
    <option value="AWG">Aruban Florin (AWG)</option>
    <option value="AZN">Azerbaijani Manat (AZN)</option>
    <option value="BAM">Bosnia and Herzegovina Convertible Mark (BAM)</option>
    <option value="BBD">Barbadian Dollar (BBD)</option>
    <option value="BDT">Bangladeshi Taka (BDT)</option>
    <option value="BGN">Bulgarian Lev (BGN)</option>
    <option value="BIF">Burundian Franc (BIF)</option>
    <option value="BMD">Bermudian Dollar (BMD)</option>
    <option value="BND">Brunei Dollar (BND)</option>
    <option value="BOB">Bolivian Boliviano (BOB)</option>
    <option value="BRL">Brazilian Real (BRL)</option>
    <option value="BSD">Bahamian Dollar (BSD)</option>
    <option value="BWP">Botswana Pula (BWP)</option>
    <option value="BYN">Belarusian Ruble (BYN)</option>
    <option value="BZD">Belize Dollar (BZD)</option>
    <option value="CAD">Canadian Dollar (CAD)</option>
    <option value="CDF">Congolese Franc (CDF)</option>
    <option value="CHF">Swiss Franc (CHF)</option>
    <option value="CLP">Chilean Peso (CLP)</option>
    <option value="CNY">Chinese Yuan (CNY)</option>
    <option value="COP">Colombian Peso (COP)</option>
    <option value="CRC">Costa Rican Colón (CRC)</option>
    <option value="CVE">Cape Verdean Escudo (CVE)</option>
    <option value="CZK">Czech Koruna (CZK)</option>
    <option value="DJF">Djiboutian Franc (DJF)</option>
    <option value="DKK">Danish Krone (DKK)</option>
    <option value="DOP">Dominican Peso (DOP)</option>
    <option value="DZD">Algerian Dinar (DZD)</option>
    <option value="EGP">Egyptian Pound (EGP)</option>
    <option value="ETB">Ethiopian Birr (ETB)</option>
    <option value="EUR">Euro (EUR)</option>
    <option value="FJD">Fijian Dollar (FJD)</option>
    <option value="FKP">Falkland Islands Pound (FKP)</option>
    <option value="GBP">British Pound Sterling (GBP)</option>
    <option value="GEL">Georgian Lari (GEL)</option>
    <option value="GIP">Gibraltar Pound (GIP)</option>
    <option value="GMD">Gambian Dalasi (GMD)</option>
    <option value="GNF">Guinean Franc (GNF)</option>
    <option value="GTQ">Guatemalan Quetzal (GTQ)</option>
    <option value="GYD">Guyanese Dollar (GYD)</option>
    <option value="HKD">Hong Kong Dollar (HKD)</option>
    <option value="HNL">Honduran Lempira (HNL)</option>
    <option value="HTG">Haitian Gourde (HTG)</option>
    <option value="HUF">Hungarian Forint (HUF)</option>
    <option value="IDR">Indonesian Rupiah (IDR)</option>
    <option value="ILS">Israeli New Shekel (ILS)</option>
    <option value="INR">Indian Rupee (INR)</option>
    <option value="ISK">Icelandic Króna (ISK)</option>
    <option value="JMD">Jamaican Dollar (JMD)</option>
    <option value="JPY">Japanese Yen (JPY)</option>
    <option value="KES">Kenyan Shilling (KES)</option>
    <option value="KGS">Kyrgystani Som (KGS)</option>
    <option value="KHR">Cambodian Riel (KHR)</option>
    <option value="KMF">Comorian Franc (KMF)</option>
    <option value="KRW">South Korean Won (KRW)</option>
    <option value="KYD">Cayman Islands Dollar (KYD)</option>
    <option value="KZT">Kazakhstani Tenge (KZT)</option>
    <option value="LAK">Laotian Kip (LAK)</option>
    <option value="LBP">Lebanese Pound (LBP)</option>
    <option value="LKR">Sri Lankan Rupee (LKR)</option>
    <option value="LRD">Liberian Dollar (LRD)</option>
    <option value="LSL">Lesotho Loti (LSL)</option>
    <option value="MAD">Moroccan Dirham (MAD)</option>
    <option value="MDL">Moldovan Leu (MDL)</option>
    <option value="MGA">Malagasy Ariary (MGA)</option>
    <option value="MKD">Macedonian Denar (MKD)</option>
    <option value="MMK">Myanma Kyat (MMK)</option>
    <option value="MNT">Mongolian Tugrik (MNT)</option>
    <option value="MOP">Macanese Pataca (MOP)</option>
    <option value="MUR">Mauritian Rupee (MUR)</option>
    <option value="MVR">Maldivian Rufiyaa (MVR)</option>
    <option value="MWK">Malawian Kwacha (MWK)</option>
    <option value="MXN">Mexican Peso (MXN)</option>
    <option value="MYR">Malaysian Ringgit (MYR)</option>
    <option value="MZN">Mozambican Metical (MZN)</option>
    <option value="NAD">Namibian Dollar (NAD)</option>
    <option value="NGN">Nigerian Naira (NGN)</option>
    <option value="NIO">Nicaraguan Córdoba (NIO)</option>
    <option value="NOK">Norwegian Krone (NOK)</option>
    <option value="NPR">Nepalese Rupee (NPR)</option>
    <option value="NZD">New Zealand Dollar (NZD)</option>
    <option value="PAB">Panamanian Balboa (PAB)</option>
    <option value="PEN">Peruvian Nuevo Sol (PEN)</option>
    <option value="PGK">Papua New Guinean Kina (PGK)</option>
    <option value="PHP">Philippine Peso (PHP)</option>
    <option value="PKR">Pakistani Rupee (PKR)</option>
    <option value="PLN">Polish Zloty (PLN)</option>
    <option value="PYG">Paraguayan Guarani (PYG)</option>
    <option value="QAR">Qatari Rial (QAR)</option>
    <option value="RON">Romanian Leu (RON)</option>
    <option value="RSD">Serbian Dinar (RSD)</option>
    <option value="RUB">Russian Ruble (RUB)</option>
    <option value="RWF">Rwandan Franc (RWF)</option>
    <option value="SAR">Saudi Riyal (SAR)</option>
    <option value="SBD">Solomon Islands Dollar (SBD)</option>
    <option value="SCR">Seychellois Rupee (SCR)</option>
    <option value="SEK">Swedish Krona (SEK)</option>
    <option value="SGD">Singapore Dollar (SGD)</option>
    <option value="SHP">Saint Helena Pound (SHP)</option>
    <option value="SLE">Sierra Leonean Leone (SLE)</option>
    <option value="SOS">Somali Shilling (SOS)</option>
    <option value="SRD">Surinamese Dollar (SRD)</option>
    <option value="STD">São Tomé and Príncipe Dobra (STD)</option>
    <option value="SZL">Swazi Lilangeni (SZL)</option>
    <option value="THB">Thai Baht (THB)</option>
    <option value="TJS">Tajikistani Somoni (TJS)</option>
    <option value="TOP">Tongan Paʻanga (TOP)</option>
    <option value="TRY">Turkish Lira (TRY)</option>
    <option value="TTD">Trinidad and Tobago Dollar (TTD)</option>
    <option value="TWD">New Taiwan Dollar (TWD)</option>
    <option value="TZS">Tanzanian Shilling (TZS)</option>
    <option value="UAH">Ukrainian Hryvnia (UAH)</option>
    <option value="UGX">Ugandan Shilling (UGX)</option>
    <option value="UYU">Uruguayan Peso (UYU)</option>
    <option value="UZS">Uzbekistani Som (UZS)</option>
    <option value="VND">Vietnamese Dong (VND)</option>
    <option value="VUV">Vanuatu Vatu (VUV)</option>
    <option value="WST">Samoan Tala (WST)</option>
    <option value="XAF">Central African CFA Franc (XAF)</option>
    <option value="XCD">East Caribbean Dollar (XCD)</option>
    <option value="XOF">West African CFA Franc (XOF)</option>
    <option value="XPF">CFP Franc (XPF)</option>
    <option value="YER">Yemeni Rial (YER)</option>
    <option value="ZAR">South African Rand (ZAR)</option>
    <option value="ZMW">Zambian Kwacha (ZMW)</option>
</select>

                                <input type="hidden" name="action" value="create_payment">
                                <input type="hidden" name="amount" value="2000"> <!-- Cantidad en centavos -->
                                <input type="hidden" name="currency" value="usd"> <!-- Moneda -->
                                <input type="hidden" name="payment_init" value="on">
                                <input type="hidden" name="commission_request_id" value="<?php echo esc_attr($commission_request_id); ?>">
                                <input type="hidden" name="success_url" value="<?php echo esc_url(home_url('/dashboard/company/payment/success')); ?>">
                                <input type="hidden" name="cancel_url" value="<?php echo esc_url(home_url('/dashboard/company/payment/cancel')); ?>">
                                <button class="btn btn-primary" type="submit">Pagar con Stripe</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4 order-md-2 mb-4">
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Your cart</span>
                            <span class="badge badge-secondary badge-pill"><?php echo count($items); ?></span>
                        </h4>
                        <ul class="list-group mb-3">
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                    <div>
                                        <span class="text-muted"><?php echo esc_html(Helper::format_price($item["price_paid"])); ?></span>
                                        <small class="text-muted">X <?php echo esc_html($item["quantity"]); ?></small>
                                    </div>
                                    <h6 class="my-0 text-muted"><?php echo esc_html(Helper::format_price($item["subtotal"])); ?></h6>
                                </li>
                            <?php endforeach;?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total Sales (USD)</span>
                                <strong><?php echo esc_html(Helper::format_price($total_cart)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Commission of Agent </span>
                                <strong><?php echo esc_html(Helper::format_price($total_agent)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Commission of Nexfy</span>
                                <strong><?php echo esc_html(Helper::format_price($total_platform)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Stripe service fee</span>
                                <strong><?php echo esc_html(Helper::format_price($total_tax_service)); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold"><?php echo esc_html(Helper::format_price($total_to_pay)); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
