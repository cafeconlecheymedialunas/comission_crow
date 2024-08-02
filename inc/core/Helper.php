<?php 
class Helper
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

    public static function get_human_time_diff($date)
    {
        // Check if the date is valid
        if (!$date || !strtotime($date)) {
            return 'Invalid date';
        }

        // Convert the stored date to a timestamp
        $timestamp_commission_request = strtotime($date);

        // Get the current timestamp
        $current_timestamp = current_time('timestamp');

        // Calculate the human-readable time difference
        $time_diff = human_time_diff($timestamp_commission_request, $current_timestamp);

        // Display the time elapsed in a readable format
        $human_date = $time_diff;

        return $human_date;
    }

    public static function get_last_update_by_and_date($post_id)
    {
        $history_status = carbon_get_post_meta($post_id, "status_history");
        $history_end = end($history_status);
    
        // Verificar que existan datos en el historial
        if ($history_end && isset($history_end["date_status"], $history_end["changed_by"])) {
            $last_update = $history_end["date_status"];
            $last_sender_history = $history_end["changed_by"];
    
            // Obtener información del usuario
            $current_user_id = get_current_user_id();
            $user_display_name = ($last_sender_history == $current_user_id) ? "You" : null;
    
            if (!$user_display_name) {
                $user = get_user_by("ID", $last_sender_history);
                $user_display_name = $user ? $user->first_name . " " . $user->last_name : null;
            }
    
            $human_readable_date = $last_update ? self::get_human_time_diff($last_update) . " ago" : null;
    
            // Construir el texto de salida
            $last_update_text = trim(($user_display_name ? $user_display_name : '') . ($user_display_name && $human_readable_date ? ' - ' : '') . ($human_readable_date ? $human_readable_date : ''));
    
            return $last_update_text;
        }
    
        return ''; // En caso de que no haya datos en el historial, devolver cadena vacía
    }
    

    public static function add_item_to_status_history($post_id, $status = "pending")
    {
        // Obtener el historial de estado existente
        $status_history = carbon_get_post_meta($post_id, 'status_history');

        // Si el campo no existe en este tipo de post, no hacemos nada
        if ($status_history === null) {
            return;
        }

        // Asegurarse de que $status_history sea un array
        if (!is_array($status_history)) {
            $status_history = [];
        }

        // Añadir el nuevo estado al historial
        $status_history[] = [
            'history_status' => $status,
            'date_status' => current_time('mysql'),
            'changed_by' => get_current_user_id(),
        ];

        // Guardar el historial actualizado
        return $status_history;
    }

}