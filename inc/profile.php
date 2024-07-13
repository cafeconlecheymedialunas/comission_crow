<?php
/*add_action('wp_ajax_nopriv_update_agent_profile', 'update_agent_profile');
add_action('wp_ajax_update_agent_profile', 'update_agent_profile');

function update_agent_profile() {
    check_ajax_referer('update-profile-agent-nonce', 'security');

    if (!current_user_can('edit_user', $_POST['user_id'])) {
        wp_send_json_error(__('Permission denied.', 'textdomain'));
    }

    $user_id = intval($_POST['user_id']);
    $errors = array();

    // Validaciones
    if (empty($_POST['first_name'])) {
        $errors['first_name'] = __('First name is required.', 'textdomain');
    }

    if (empty($_POST['last_name'])) {
        $errors['last_name'] = __('Last name is required.', 'textdomain');
    }

    if (empty($_POST['user_email']) || !is_email($_POST['user_email'])) {
        $errors['user_email'] = __('Valid email address is required.', 'textdomain');
    }

    if (!empty($errors)) {
        wp_send_json_error($errors);
    }

    // Actualización si no hay errores
    $args = array(
        'post_type'   => 'commercial_agent',
        'meta_query'  => array(
            array(
                'key'     => 'agent',
                'value'   => $user_id,
                'compare' => '=',
            ),
        ),
    );

    $agent_query = new WP_Query($args);

    if (!$agent_query->have_posts()) {
        wp_send_json_error(__('Commercial agent not found for this user.', 'textdomain'));
    }

    while ($agent_query->have_posts()) {
        $agent_query->the_post();
        $agent_id = get_the_ID();

        // Actualizar los campos de Carbon Fields
        carbon_set_post_meta($agent_id, 'description', sanitize_textarea_field($_POST['description']));
        carbon_set_post_meta($agent_id, 'languages', $_POST['language']);
        carbon_set_post_meta($agent_id, 'location', sanitize_text_field($_POST['location']));
        carbon_set_post_meta($agent_id, 'seller_type', sanitize_text_field($_POST['seller_type']));
        carbon_set_post_meta($agent_id, 'years_of_experience', sanitize_text_field($_POST['years_of_experience']));

        // Actualizar campos complejos
        // Industries
        $industries = array();
        if (!empty($_POST['industry'])) {
            foreach ($_POST['industry'] as $industry_id) {
                $industries[] = array(
                    'industry_id' => sanitize_text_field($industry_id),
                );
            }
        }
        carbon_set_post_meta($agent_id, 'industries', $industries);

        // Selling Methods
        $selling_methods = array();
        if (!empty($_POST['selling_methods'])) {
            foreach ($_POST['selling_methods'] as $selling_method_id) {
                $selling_methods[] = array(
                    'selling_method_id' => sanitize_text_field($selling_method_id),
                );
            }
        }
        carbon_set_post_meta($agent_id, 'selling_methods', $selling_methods);

        // Skills
        $skills = array();
        if (!empty($_POST['skills'])) {
            foreach ($_POST['skills'] as $skill_id) {
                $skills[] = array(
                    'skill_id' => sanitize_text_field($skill_id),
                );
            }
        }
        carbon_set_post_meta($agent_id, 'skills', $skills);

        // Actualizar los campos de usuario
        $update_user_data = array(
            'ID'           => $user_id,
            'first_name'   => sanitize_text_field($_POST['first_name']),
            'last_name'    => sanitize_text_field($_POST['last_name']),
            'user_email'   => sanitize_email($_POST['user_email']),
            'display_name' => sanitize_text_field($_POST['first_name'] . ' ' . $_POST['last_name']), // Actualiza display_name
            'user_login'   => sanitize_user($_POST['first_name'] . $_POST['last_name']), // Actualiza username
        );

        wp_update_user($update_user_data);

        wp_send_json_success(__('Profile updated successfully.', 'textdomain'));
    }

    wp_reset_postdata();
    wp_die();
}

*/
