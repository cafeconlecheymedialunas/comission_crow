<?php
class Crud {
    protected $post_type;

    public function __construct($post_type) {
        $this->post_type = $post_type;
    }

    // Método privado para actualizar campos personalizados con validaciones
    protected function update_custom_fields($post_id, $data, $field_mappings) {
        $errors = array();

        // Iterar sobre los campos definidos y actualizar cada uno
        foreach ($field_mappings as $meta_key => $field_label) {
            if (isset($data[$meta_key])) {
                $updated = update_post_meta($post_id, $meta_key, $data[$meta_key]);
                if (!$updated) {
                    $error_message = sprintf(__('Failed to update %s'), $field_label);
                    $errors[] = new WP_Error('update_failed', $error_message);
                }
            }
        }

        // Verificar si hubo errores y retornar apropiadamente
        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    // Método privado para formatear los datos de una publicación
    protected function format_post_data($post) {
        // Implementación genérica para formatear datos de una publicación
        return array(
            'ID' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            // Puedes agregar más campos generales aquí si es necesario
        );
    }

    // Método para crear una nueva publicación
    public function create($data, $field_mappings) {
        $post_id = wp_insert_post(array(
            'post_type' => $this->post_type,
            'post_title' => $data['title'],
            'post_content' => $data['content'],
            'post_status' => 'publish',
        ));

        if (is_wp_error($post_id)) {
            return $post_id; // Retornar WP_Error
        }

        $update_result = $this->update_custom_fields($post_id, $data, $field_mappings);
        if (is_wp_error($update_result)) {
            return $update_result; // Retornar WP_Error
        }

        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error($this->post_type . '_creation_failed', __('Creation failed for ') . $this->post_type);
        }

        return $this->format_post_data($post);
    }

    // Método para leer una publicación por su ID
    public function read($post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== $this->post_type) {
            return new WP_Error($this->post_type . '_not_found', __('Post not found for ') . $this->post_type);
        }

        return $this->format_post_data($post);
    }

    // Método para actualizar una publicación por su ID
    public function update($post_id, $data, $field_mappings) {
        $post = array(
            'ID' => $post_id,
            'post_title' => $data['title'],
            'post_content' => $data['content'],
        );

        $updated = wp_update_post($post);

        if (is_wp_error($updated)) {
            return $updated; // Retornar WP_Error
        }

        $update_result = $this->update_custom_fields($post_id, $data, $field_mappings);
        if (is_wp_error($update_result)) {
            return $update_result; // Retornar WP_Error
        }

        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error($this->post_type . '_update_failed', __('Update failed for ') . $this->post_type);
        }

        return $this->format_post_data($post);
    }

    // Método para eliminar una publicación por su ID
    public function delete($post_id) {
        $deleted = wp_delete_post($post_id, true);
        if (!$deleted) {
            return new WP_Error($this->post_type . '_delete_failed', __('Delete failed for ') . $this->post_type);
        }

        return true;
    }
}
