<?php
class EmailMetaBox
{
    private $post_types = ['dispute', 'deposit'];

    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_post_meta']);
        add_action('admin_footer', [$this, 'enqueue_admin_scripts']);
        add_action('wp_ajax_send_custom_email', [$this, 'handle_ajax_email']);
    }

    public function add_meta_boxes()
    {
        foreach ($this->post_types as $post_type) {
            add_meta_box(
                'custom_actions_meta_box',
                __('Actions'),
                [$this, 'render_meta_box'],
                $post_type,
                'side',
                'high'
            );
        }
    }

    public function render_meta_box($post)
    {
        wp_nonce_field('custom_actions_meta_box_nonce', 'custom_actions_meta_box_nonce');

        $selected_action = get_post_meta($post->ID, '_selected_action', true);

        $email_options = [];
        if ($post->post_type === 'dispute') {
            $email_options = [
                'dispute_approval_email_agent' => 'Send Dispute Approval Email to Commercial Agent',
                'dispute_approval_email_company' => 'Send Dispute Approval Email to Company',
                'dispute_rejected_email_agent' => 'Send Dispute Rejected Email to Commercial Agent',
                'dispute_rejected_email_company' => 'Send Dispute Rejected Email to Company',
            ];
        } elseif ($post->post_type === 'deposit') {
            $email_options = [
                'deposit_approval_email_agent' => 'Send Deposit Approval Email to Agent',
                'deposit_approval_email_company' => 'Send Deposit Approval Email to Company',
                'deposit_rejected_email_agent' => 'Send Deposit Rejected Email to Agent',
                'deposit_rejected_email_company' => 'Send Deposit Rejected Email to Company',
            ];
        }

        echo '<p>';
        echo '<label for="action_select">' . __('Select Email Action:') . '</label>';
        echo '<select id="action_select" name="action_select">';
        foreach ($email_options as $key => $label) {
            $selected = $key === $selected_action ? 'selected' : '';
            echo "<option value=\"$key\" $selected>$label</option>";
        }
        echo '</select>';
        echo '</p>';
        echo '<p>';
        echo '<input type="button" class="button button-primary" id="send_email" value="' . __('Send Email') . '">';
        echo '</p>';
    }

    public function save_post_meta($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['custom_actions_meta_box_nonce']) || !wp_verify_nonce($_POST['custom_actions_meta_box_nonce'], 'custom_actions_meta_box_nonce')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['action_select'])) {
            update_post_meta($post_id, '_selected_action', sanitize_text_field($_POST['action_select']));
        }
    }

    public function enqueue_admin_scripts()
    {
        global $post;
        if (!in_array($post->post_type, $this->post_types)) {
            return;
        }

        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#send_email').on('click', function() {
                var post_id = <?php echo $post->ID; ?>;
                var action = $('#action_select').val();
                var data = {
                    action: 'send_custom_email',
                    post_id: post_id,
                    selected_action: action,
                    security: '<?php echo wp_create_nonce('send_custom_email_nonce'); ?>'
                };

                $.post(ajaxurl, data, function(response) {
                    if (response.success) {
                        alert('Email sent successfully.');
                    } else {
                        alert('Failed to send email: ' + response.data.message);
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function handle_ajax_email()
    {
        check_ajax_referer('send_custom_email_nonce', 'security');

        $post_id = intval($_POST['post_id']);
        $selected_action = sanitize_text_field($_POST['selected_action']);

        if (!$post_id || !$selected_action) {
            wp_send_json_error(['message' => 'Invalid post ID or action.']);
        }

        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error(['message' => 'Post not found.']);
        }

        $success = false;
        $message = '';

        if ($post->post_type === 'dispute') {
            $dispute = Dispute::get_instance();

            switch ($selected_action) {
                case 'dispute_approval_email_agent':
                    $success = $dispute->send_dispute_approval_email_to_agent($post_id);
                    $message = 'Dispute Approval Email sent to Agent.';
                    break;
                case 'dispute_approval_email_company':
                    $success = $dispute->send_dispute_approval_email_to_company($post_id);
                    $message = 'Dispute Approval Email sent to Company.';
                    break;
                case 'dispute_rejected_email_agent':
                    $success = $dispute->send_dispute_rejection_email_to_agent($post_id);
                    $message = 'Dispute Rejected Email sent to Agent.';
                    break;
                case 'dispute_rejected_email_company':
                    $success = $dispute->send_dispute_rejection_email_to_company($post_id);
                    $message = 'Dispute Rejected Email sent to Company.';
                    break;
                default:
                    wp_send_json_error(['message' => 'Invalid email type selected.']);
                    return;
            }
        } elseif ($post->post_type === 'deposit') {
            $deposit = Deposit::get_instance();

            switch ($selected_action) {
                case 'deposit_approval_email_agent':
                    $success = $deposit->send_deposit_approval_email_to_agent($post_id);
                    $message = 'Deposit Approval Email sent to Agent.';
                    break;
                case 'deposit_approval_email_company':
                    $success = $deposit->send_deposit_approval_email_to_company($post_id);
                    $message = 'Deposit Approval Email sent to Company.';
                    break;
                case 'deposit_rejected_email_agent':
                    $success = $deposit->send_deposit_rejection_email_to_agent($post_id);
                    $message = 'Deposit Rejected Email sent to Agent.';
                    break;
                case 'deposit_rejected_email_company':
                    $success = $deposit->send_deposit_rejection_email_to_company($post_id);
                    $message = 'Deposit Rejected Email sent to Company.';
                    break;
                default:
                    wp_send_json_error(['message' => 'Invalid email type selected.']);
                    return;
            }
        }

        if ($success) {
            wp_send_json_success(['message' => $message]);
        } else {
            wp_send_json_error(['message' => 'Failed to send email.']);
        }
    }
}

new EmailMetaBox();
