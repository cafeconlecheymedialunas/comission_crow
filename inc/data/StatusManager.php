<?php
class StatusManager
{

    private function __construct()
    {
    }
    private static $instance = null;
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function get_statuses()
    {
        return [
            'commission_request' => $this->get_status_commission_request(),
            'dispute' => $this->get_status_dispute(),
            'payment' => $this->get_status_payment()
        ];
    }

    public function get_status_commission_request()
    {
        return [
            'pending' => 'Pending',
            'dispute_pending' => 'Dispute Pending',
            'dispute_accepted' => 'Dispute Accepted',
            'dispute_refused' => 'Dispute Refused',
            'dispute_cancelled' => 'Dispute Cancelled',
            'payment_pending' => 'Pending Payment',
            'payment_completed' => 'Payment Completed',
            'payment_failed' => 'Payment Failed',
            'payment_canceled' => 'Payment Canceled',
        ];
    }

    public function get_status_dispute()
    {
        return [
            'dispute_pending' => 'Dispute Pending',
            'dispute_accepted' => 'Dispute Accepted',
            'dispute_refused' => 'Dispute Refused',
            'dispute_cancelled' => 'Dispute Cancelled'
        ];
    }

    public function get_status_payment()
    {
        return [
            'payment_pending' => 'Payment Pending',
            'payment_completed' => 'Payment Completed',
            'payment_failed' => 'Payment Failed',
            'payment_canceled' => 'Payment Canceled'
        ];
    }

    public function get_status_deposit()
    {
        return [
            'deposit_requested' => 'Deposit Requested',
            'deposit_completed' => 'Deposit Completed',
        ];
    }


    public function update_commission_request_on_save($post_id)
    {
        $post_type = get_post_type($post_id);
        $status_manager = $this->get_statuses();

        if ($post_type === 'dispute' || $post_type === 'payment') {
            $new_status = carbon_get_post_meta($post_id, 'status');

            // Validar estado
            $valid_statuses = $status_manager[$post_type] ?? [];
            if (!array_key_exists($new_status, $valid_statuses)) {
                wp_die('Error: El estado proporcionado no es válido.');
            }

            $commission_request_id = carbon_get_post_meta($post_id, 'commission_request_id');

            if ($commission_request_id) {
                $current_commission_request_status = carbon_get_post_meta($commission_request_id, 'status');
                $this->handle_status_update($post_type, $commission_request_id, $new_status, $current_commission_request_status);
            }
        }
    }

    private function handle_status_update($post_type, $commission_request_id, $new_status, $current_status)
    {
        if ($post_type === 'dispute') {
            // Lógica específica para disputas
            if ($new_status === 'dispute_accepted') {
                carbon_set_post_meta($commission_request_id, 'status', 'dispute_accepted');
            } elseif ($new_status === 'dispute_refused') {
                carbon_set_post_meta($commission_request_id, 'status', 'dispute_refused');
            } elseif ($new_status === 'dispute_cancelled') {
                carbon_set_post_meta($commission_request_id, 'status', 'dispute_cancelled');
            } elseif ($new_status === 'dispute_pending') {
                if (!in_array($current_status, ['payment_pending', 'payment_completed'])) {
                    carbon_set_post_meta($commission_request_id, 'status', 'dispute_pending');
                }
            }
        } elseif ($post_type === 'payment') {
            // Lógica específica para pagos
            if ($new_status === 'payment_completed') {
                carbon_set_post_meta($commission_request_id, 'status', 'payment_completed');
            } elseif ($new_status === 'payment_failed') {
                carbon_set_post_meta($commission_request_id, 'status', 'payment_failed');
            } elseif ($new_status === 'payment_canceled') {
                carbon_set_post_meta($commission_request_id, 'status', 'payment_canceled');
            } elseif ($new_status === 'payment_pending') {
                if ($current_status === 'pending' || $current_status === 'dispute_pending') {
                    carbon_set_post_meta($commission_request_id, 'status', 'payment_pending');
                }
            }
        }
    }
}
