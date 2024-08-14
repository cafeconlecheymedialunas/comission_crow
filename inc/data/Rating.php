<?php

class Rating
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

    public function create_rating() {
        if (!isset($_POST['commercial_agent'], $_POST['score'], $_POST['company'], $_POST['content'])) {
            wp_send_json_error(["general" => 'Missing data']); 
            wp_die();
        }
    
        $commercial_agent_id = intval($_POST['commercial_agent']);
        $score = intval($_POST['score']);
        $company_id = intval($_POST['company']);
        $content = sanitize_textarea_field($_POST['content']);
    
        if ($commercial_agent_id <= 0) {
            wp_send_json_error(["general" => 'Invalid commercial agent ID']);
            wp_die();
        }
    
        if ($score < 1 || $score > 5) {
            wp_send_json_error(["general" => 'Invalid score. Must be between 1 and 5']);
            wp_die(); 
        }
    
        if ($company_id <= 0) {
            wp_send_json_error(["general" =>'Invalid company ID']);
            wp_die();
        }
    
        if (empty($content)) {
            wp_send_json_error(["general" =>'Content cannot be empty']);
            wp_die();
        }
    
        // Verificar si ya existe una reseña de esta empresa para este agente
        $existing_review = new WP_Query([
            'post_type'  => 'review',
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
            'fields' => 'ids' // Solo necesitamos los IDs de los posts
        ]);
    
        if ($existing_review->have_posts()) {
            wp_send_json_error(["general" => 'You have already submitted a review for this agent']);
            wp_die();
        }
    
        // Inserta el nuevo post de tipo 'review'
        $review_id = wp_insert_post([
            'post_type'   => 'review',
            'post_title'  => 'Rating for Agent ID ' . $commercial_agent_id,
            'post_status' => 'publish',
            'post_content' => $content
        ]);
    
        if (is_wp_error($review_id)) {
            wp_send_json_error(["general"=> 'Failed to create review']);
            wp_die();
        }
    
        carbon_set_post_meta($review_id, "score", $score);
        carbon_set_post_meta($review_id, "commercial_agent", $commercial_agent_id);
        carbon_set_post_meta($review_id, "company", $company_id);

        $this->send_create_agent_review_email($review_id);
    
        wp_send_json_success('Review submitted successfully');
    
        wp_die(); // Termina la ejecución después de procesar la solicitud
    }
    public function send_create_agent_review_email($review_id)
    {
        // Obtener detalles de la reseña
        $commercial_agent_id = carbon_get_post_meta($review_id, 'commercial_agent');
        $company_id = carbon_get_post_meta($review_id, 'company');
        $score = carbon_get_post_meta($review_id, 'score');
        $content = get_post_field('post_content', $review_id);
    
        // Obtener detalles del agente y de la empresa
        $user_commercial_agent_id = carbon_get_post_meta($commercial_agent_id, 'user');
        $user_commercial_agent = get_user_by('ID', $user_commercial_agent_id);
        $company_title = get_the_title($company_id);
    
        // Crear una instancia de la clase EmailSender
        $email_sender = new EmailSender();
    
        // Definir los parámetros del correo electrónico
        $to = $user_commercial_agent->user_email;
        $subject = 'New Review Received for Your Services';
        $message = "<p>Hello, {$user_commercial_agent->first_name},</p>
            <p>You have received a new review for your services from <strong>{$company_title}</strong>.</p>
            <p><strong>Rating:</strong> {$score} out of 5</p>
            <p><strong>Review:</strong></p>
            <p>{$content}</p>
            <p>Thank you for your continued dedication and hard work.</p>";
    
        // Enviar el correo electrónico
        $sent = $email_sender->send_email($to, $subject, $message);
    
        if (!$sent) {
            $errors = $email_sender->get_error();
            foreach ($errors->get_error_messages() as $error_message) {
                error_log('Error sending email: ' . $error_message);
            }
        }
    
        return $sent;
    }


    public function calculate_average_rating($commercial_agent_id){
        // Consultar todas las reseñas para el agente comercial dado
        $reviews_query = new WP_Query([
            'post_type'  => 'review',
            'meta_query' => [
                [
                    'key'   => 'commercial_agent',
                    'value' => $commercial_agent_id,
                    'compare' => '='
                ]
            ]
        ]);
    
        // Inicializar variables para la suma de los puntajes y el número de reseñas
        $total_score = 0;
        $review_count = 0;
    
        // Recorrer las reseñas y sumar los puntajes
        if ($reviews_query->have_posts()) {
            while ($reviews_query->have_posts()) {
                $reviews_query->the_post();
                $score = carbon_get_post_meta(get_the_ID(), "score");
    
                // Asegurarse de que $score es un número antes de sumarlo
                if (is_numeric($score)) {
                    $total_score += $score;
                    $review_count++;
                }
            }
            wp_reset_postdata();
        }
    
        // Calcular el promedio
        if ($review_count > 0) {
            $average_score = $total_score / $review_count;
        } else {
            $average_score = 0; // Si no hay reseñas, el promedio es 0 o un valor que decidas
        }
    
        return $average_score;
    }
    

}
