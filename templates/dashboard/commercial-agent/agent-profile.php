<div class="seller-profile mb-4">
    <div class="card">
        <div class="row">
            <div class="col-md-3 col-lg-2 my-auto">
                <?php 
                $thumbnail = get_the_post_thumbnail($commercial_agent->ID, [250, 250], [
                    'class' => 'attachment-250x250 size-250x250 agent-profile-picture',
                    "decoding" => "async",
                ]);
                if ($thumbnail): ?>
                    <?php echo $thumbnail; ?>
                <?php endif; ?>
            </div>
            <div class="col-md-9 col-lg-10 my-auto">
                <div class="row">
                    <div class="col-lg-6 my-auto">
                        <h3 class="mb-0"><?php echo esc_html("$user_commercial_agent->first_name $user_commercial_agent->last_name"); ?></h3>

                        <?php if ($user_commercial_agent->user_registered): ?>
                            <p>Member since <?php echo esc_html($user_commercial_agent->user_registered); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="stats-list">
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
                                    <?php if (isset($pending_commission_request)): ?>
                                        <div class="stats earnings">
                                            <span><?php echo count($pending_commission_request); ?></span> Pending Commission
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($on_going_contracts)): ?>
                                        <div class="stats contracts">
                                            <span><?php echo count($on_going_contracts); ?></span> On Going Contracts
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($completed_commission_request)): ?>
                                        <div class="stats orders">
                                            <span><?php echo count($completed_commission_request); ?></span> Commissions paid
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <ul class="list-unstyled mid-meta">
                                    <li class="list-inline-item">
                                        <button class="btn btn-sm btn-info mb-3" data-bs-toggle="modal" data-bs-target="#chat-modal" data-user-id="<?php echo esc_attr($user_commercial_agent->ID); ?>">
                                            Send Message
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
                                    // Get the company associated with the current user
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

                                    // Get all contracts associated with the commercial agent and the company
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
                                        'fields' => 'ids' // Only need post IDs
                                    ];

                                    $contracts = new WP_Query($contract_args);

                                    $has_completed_commission_request = false;

                                    if (!empty($contracts->posts)) {
                                        foreach ($contracts->posts as $contract_id) {
                                            // Check for commission requests with 'payment_completed' status for each contract
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
                                                'fields' => 'ids' // Only need post IDs
                                            ];

                                            $commission_requests = new WP_Query($commission_request_args);

                                            if ($commission_requests->have_posts()) {
                                                $has_completed_commission_request = true;
                                                break; // No need to check further if we found a completed commission request
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
