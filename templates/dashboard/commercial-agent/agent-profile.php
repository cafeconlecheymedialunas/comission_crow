<div class="seller-profile mb-4">
    <div class="card">
        <div class="row">
            <?php 
            $thumbnail = get_the_post_thumbnail($commercial_agent->ID, [250, 250], [
                'class' => 'attachment-250x250 size-250x250 agent-profile-picture',
                "decoding" => "async",
            ]);
            if ($thumbnail): ?>
                <div class="col-md-3 col-lg-2 my-auto">
                    <?php echo $thumbnail; ?>
                </div>
            <?php endif; ?>
            
            <div class="my-auto <?php echo ($thumbnail) ? "col-md-9 col-lg-10" : ""; ?>">
                <div class="d-flex justify-content-between">
                    <div class="my-auto">
                        <h3 class="mb-0"><?php echo esc_html("$user_commercial_agent->first_name $user_commercial_agent->last_name"); ?></h3>
                        
                        <?php if ($user_commercial_agent->user_registered): ?>
                            <p>Member since <?php echo esc_html($user_commercial_agent->user_registered); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php
                    $currency_code = 'USD';
                    if ($commercial_agent) {
                        $post_currency_terms = wp_get_post_terms($commercial_agent->ID, 'currency');
                        if (!empty($post_currency_terms)) {
                            $post_currency = $post_currency_terms[0];
                            $currency_code = carbon_get_term_meta($post_currency->term_id, 'currency_code') ?: $currency_code;
                        }
                    }
                    ?>
                    
                    <ul class="list-unstyled mid-meta">
                        <li class="list-inline-item">
                            <button class="btn btn-sm btn-info mb-3" data-bs-toggle="modal" data-bs-target="#chat-modal" data-user-id="<?php echo esc_attr($user_commercial_agent->ID); ?>">
                          Send Message    <i class="chat fa-solid fa-comments"></i> 
                            </button>
                        </li>

                        <div class="modal fade" id="chat-modal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo do_shortcode('[better_messages_user_conversation user_id="' . esc_attr($user_commercial_agent->ID) . '"]'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // Obtener la compañía asociada al usuario actual
                        $args = [
                            'post_type' => 'company',
                            'meta_query' => [
                                [
                                    'key' => 'user_id',
                                    'value' => $current_user->ID,
                                    'compare' => '=',
                                ],
                            ],
                            'posts_per_page' => -1,
                        ];

                        $company_query = new WP_Query($args);
                        $company_id = $company_query->have_posts() ? $company_query->posts[0]->ID : null;

                        // Obtener todos los contratos asociados al agente comercial y la compañía
                        $contract_args = [
                            'post_type'  => 'contract',
                            'meta_query' => [
                                'relation' => 'AND',
                                [
                                    'key'   => 'commercial_agent',
                                    'value' => $commercial_agent_id,
                                    'compare' => '='
                                ],
                                [
                                    'key'   => 'company',
                                    'value' => $company_id,
                                    'compare' => '='
                                ]
                            ],
                            'fields' => 'ids'
                        ];

                        $contracts = new WP_Query($contract_args);
                        $has_completed_commission_request = false;

                        if (!empty($contracts->posts)) {
                            foreach ($contracts->posts as $contract_id) {
                                // Verificar solicitudes de comisión con el estado 'payment_completed' para cada contrato
                                $commission_request_args = [
                                    'post_type'  => 'commission_request',
                                    'meta_query' => [
                                        'relation' => 'AND',
                                        [
                                            'key'   => 'contract_id',
                                            'value' => $contract_id,
                                            'compare' => '='
                                        ],
                                        [
                                            'key'   => 'status',
                                            'value' => 'payment_completed',
                                            'compare' => '='
                                        ]
                                    ],
                                    'fields' => 'ids'
                                ];

                                $commission_requests = new WP_Query($commission_request_args);

                                if ($commission_requests->have_posts()) {
                                    $has_completed_commission_request = true;
                                    break; // No se necesita más comprobación si ya se encontró una solicitud completada
                                }
                            }
                        }

                        if ($company_id && in_array('company', $current_user->roles) && $has_completed_commission_request): ?>
                            <li class="list-inline-item">
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#rating" data-user-id="<?php echo esc_attr($user_commercial_agent->ID); ?>">
                                    Send a Rating
                                </button>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div> <!-- Cierre del div 'my-auto' -->
        </div> <!-- Cierre del div 'row' -->
    </div> <!-- Cierre del div 'card' -->
</div> <!-- Cierre del div 'seller-profile' -->
