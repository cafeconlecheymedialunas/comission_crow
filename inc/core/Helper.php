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
    public static function convert_price_to_selected_currency($amount, $user_id = null, $decimals = 2)
    {
        $post = ProfileUser::get_instance()->get_user_associated_post_type($user_id);
        
        $post_id = $post ? $post->ID : null;
    
        $currency_symbol = '$';
        $currency_code = 'USD';
        $exchange_rate = 1.0;
    
        if ($post_id) {
            $post_currency_terms = wp_get_post_terms($post_id, 'currency');
            if (!empty($post_currency_terms)) {
                $post_currency = $post_currency_terms[0];
                $currency_symbol = carbon_get_term_meta($post_currency->term_id, 'currency_symbol') ?: $currency_symbol;
                $currency_code = carbon_get_term_meta($post_currency->term_id, 'currency_code') ?: $currency_code;
                $exchange_rate = floatval(carbon_get_term_meta($post_currency->term_id, 'currency_exhange_rate')) ?: $exchange_rate;
            }
        }
    
        $amount = floatval($amount);
        if ($amount <= 0) {
            return false;
        }
    
        if ($exchange_rate <= 0) {
            return false; 
        }
    
        $converted_amount = $amount * $exchange_rate;
        $formatted_amount = number_format($converted_amount, $decimals, '.', ',');
    
        return $currency_symbol . $formatted_amount . " ($currency_code)";
    }

    public static function convert_and_format_price($amount, $user_id = null)
    {
        // Convertimos el precio utilizando la función existente
        $converted_price = self::convert_price_to_selected_currency($amount, $user_id, 0);
        
        // Extraemos el valor numérico del precio convertido
        preg_match('/[\d,]+(?:\.\d+)?/', $converted_price, $matches);
        $numeric_value = isset($matches[0]) ? floatval(str_replace(',', '', $matches[0])) : 0;
    
        // Formateamos el número con notaciones como K, M, etc., y redondeamos
        if ($numeric_value >= 1000000) {
            $formatted_value = round($numeric_value / 1000000) . 'M';
        } elseif ($numeric_value >= 1000) {
            $formatted_value = round($numeric_value / 1000) . 'K';
        } else {
            $formatted_value = round($numeric_value);
        }
    
        // Extraemos el símbolo de la moneda de la cadena original
        preg_match('/^\D+/', $converted_price, $currency_symbol);
        $currency_symbol = isset($currency_symbol[0]) ? $currency_symbol[0] : '';
    
        // Retornamos el precio formateado con el símbolo de moneda
        return  $formatted_value;
    }
    


    public static function convert_to_usd($amount, $user_id = null, $decimals = 2)
    {
        $post = ProfileUser::get_instance()->get_user_associated_post_type($user_id);
        $post_id = $post ? $post->ID : null;
    
        $currency_code = 'USD';
        $exchange_rate = 1.0;
    
        if ($post_id) {
            $post_currency_terms = wp_get_post_terms($post_id, 'currency');
            if (!empty($post_currency_terms)) {
                $post_currency = $post_currency_terms[0];
                $currency_code = carbon_get_term_meta($post_currency->term_id, 'currency_code') ?: $currency_code;
                $exchange_rate = floatval(carbon_get_term_meta($post_currency->term_id, 'currency_exhange_rate')) ?: $exchange_rate;
            }
        }
    
        $amount = floatval($amount);
        if ($amount <= 0) {
            return new WP_Error('invalid_amount', 'Invalid amount for conversion.');
        }
    
        if ($exchange_rate <= 0) {
            return new WP_Error('invalid_exchange_rate', 'Invalid exchange rate.');
        }
    
        $converted_amount = $amount / $exchange_rate;

        return $converted_amount;
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

    public static function validate_files($files, $allowed_types = ['application/pdf', 'text/plain'], $max_size = 10485760) // 10MB
    {
        $errors = [];
    
        foreach ($files['name'] as $key => $value) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $file_type = $files['type'][$key];
                $file_size = $files['size'][$key];
    
                if (!in_array($file_type, $allowed_types)) {
                    $errors[] = sprintf('File type %s is not allowed. Allowed types are: %s.', $file_type, implode(', ', $allowed_types));
                }
    
                if ($file_size > $max_size) {
                    $errors[] = sprintf('File size of %s exceeds the maximum limit of %sMB. Your file size is %sMB.', 
                                        $files['name'][$key], 
                                        $max_size / 1048576, 
                                        $file_size / 1048576);
                }
            } else {
                $errors[] = sprintf('Error uploading file %s: %s', $files['name'][$key], self::get_upload_error_message($files['error'][$key]));
            }
        }
    
        if (!empty($errors)) {
            return [
                "success" => false,
                "errors" => $errors,
            ];
        }
    
        return [
            "success" => true,
        ];
    }
    
    private static function get_upload_error_message($error_code)
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        ];
    
        return isset($messages[$error_code]) ? $messages[$error_code] : 'Unknown upload error.';
    }
    
    public static function handle_multiple_file_upload($files)
    {
        $uploads = [];
        foreach ($files['name'] as $key => $value) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key],
                ];

                $upload = wp_handle_upload($file, ['test_form' => false]);
                if ($upload && !isset($upload['error'])) {
                    $attachment_id = wp_insert_attachment([
                        'guid' => $upload['url'],
                        'post_mime_type' => $upload['type'],
                        'post_title' => sanitize_file_name($upload['file']),
                        'post_content' => '',
                        'post_status' => 'inherit',
                    ], $upload['file']);

                    require_once ABSPATH . 'wp-admin/includes/image.php';
                    $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                    wp_update_attachment_metadata($attachment_id, $attach_data);

                    $uploads[] = $attachment_id;
                } else {
                    return ['error' => 'File upload error: ' . $upload['error']];
                }
            }
        }
        return $uploads;
    }

}
