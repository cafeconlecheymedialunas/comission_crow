<?php
class Deposit
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
    public function withdraw_founds()
    {
        // Verificar nonce para seguridad
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'withdraw-founds')) {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
            return;
        }
    
        // Validar y sanitizar los datos
        $commercial_agent_id = isset($_POST['commercial_agent_id']) ? intval($_POST['commercial_agent_id']) : 0;
        $current_user_id = get_current_user_id();
    
        // Asegúrate de que el usuario esté autorizado a realizar el retiro
        if ($current_user_id <= 0 || $commercial_agent_id <= 0) {
            wp_send_json_error(['message' => 'Invalid user or agent ID.']);
            return;
        }
    
        // Obtener el saldo de la billetera del usuario
        $wallet_balance = ProfileUser::get_instance()->calculate_wallet_balance();
    
        // Aquí puedes añadir lógica para determinar la cantidad a retirar.
        // Por simplicidad, supongamos que el usuario desea retirar todo el saldo.
        $amount_to_withdraw = $wallet_balance;
    
        // Crear un nuevo post de tipo 'deposit'
        $post_data = [
            'post_title'    => 'Withdraw Found Requests for User ' . $current_user_id,
            'post_content'  => 'Amount: ' . $amount_to_withdraw,
            'post_status'   => 'publish', // O 'pending' si necesitas revisión
            'post_author'   => $current_user_id,
            'post_type'     => 'deposit',
        ];
    
        
        $post_id = wp_insert_post($post_data);
    
        if (!$post_id) {
            wp_send_json_error(['message' => 'Failed to create deposit record.']);
           
        }

        carbon_set_post_meta($post_id, 'total_withdraw_founds', $amount_to_withdraw);
        carbon_set_post_meta($post_id, 'user', $current_user_id);

        carbon_set_post_meta($post_id, 'status', 'pending');

        wp_send_json_success(['message' => 'Funds withdrawn successfully and deposit recorded.']);
    }
    
   
    
    
    
    


}
