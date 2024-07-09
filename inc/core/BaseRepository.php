<?php
class BaseRepository {
    protected $post_type;

    public function __construct($post_type) {
        $this->post_type = $post_type;
    }

    public function create(array $data, array $field_mappings) {
        $post_id = wp_insert_post(array(
            'post_type' => $this->post_type,
            'post_title' => $data['title'],
            'post_content' => $data['content'],
            'post_status' => 'publish',
        ));

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        $update_result = $this->update_custom_fields($post_id, $data, $field_mappings);
        if (is_wp_error($update_result)) {
            return $update_result;
        }

        return $post_id;
    }

    protected function update_custom_fields($post_id, $data, $field_mappings) {
        $errors = array();
        foreach ($field_mappings as $meta_key => $field_label) {
            if (isset($data[$meta_key])) {
                $updated = update_post_meta($post_id, $meta_key, $data[$meta_key]);
                if (!$updated) {
                    $errors[] = new WP_Error('update_failed', sprintf(__('Failed to update %s'), $field_label));
                }
            }
        }
        return empty($errors) ? true : $errors;
    }

    public function get($post_id, $return_fields = []) {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== $this->post_type) {
            return new WP_Error($this->post_type . '_not_found', __('Post not found for ') . $this->post_type);
        }

        $post_data = $this->format_post_data($post);
        if (empty($return_fields) || in_array('all', $return_fields)) {
            $meta = get_post_meta($post_id);
            $post_data = array_merge($post_data, $meta);
        } else {
            foreach ($return_fields as $field) {
                $post_data[$field] = get_post_meta($post_id, $field, true);
            }
        }

        return $post_data;
    }

    protected function format_post_data($post) {
        return array(
            'ID' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
        );
    }
}
