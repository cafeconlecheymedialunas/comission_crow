<?php
class Crud
{
    protected $post_type;
    protected $general_errors;

    public function __construct($post_type)
    {
        $this->post_type = $post_type;
        $this->general_errors = [];
    }

    protected function update_custom_fields($post_id, $data, $field_mappings)
    {
        foreach ($field_mappings as $meta_key => $field_label) {
            if (isset($data[$meta_key])) {
                carbon_set_post_meta($post_id, $meta_key, $data[$meta_key]);
            }
        }
    }

    public function create($data, $field_mappings)
    {
        $post_id = wp_insert_post([
            'post_type' => $this->post_type,
            'post_title' => $data['post_title'],
            'post_content' => $data['post_content'],
            'post_status' => 'publish',
            'post_author' => $data['post_author'],
        ]);

        if (is_wp_error($post_id)) {
            $this->general_errors[] = "Could not create the post.";
            return null;
        }

        $this->update_custom_fields($post_id, $data, $field_mappings);

        return $post_id;
    }

    public function update($post_id, $data, $field_mappings)
    {
        $post = [
            'ID' => $post_id,
            'post_title' => $data['post_title'],
            'post_content' => $data['post_content']
        ];
        $updated = wp_update_post($post);

        if (is_wp_error($updated)) {
            $this->general_errors[] = "Could not update the post.";
            return null;
        }

        $this->update_custom_fields($post_id, $data, $field_mappings);

        return $post_id;
    }

    public function delete($post_id)
    {
        $deleted = wp_delete_post($post_id, true);
        if (!$deleted) {
            $this->general_errors[] = "Delete failed for " . $this->post_type;
            return false;
        }
        return $deleted;
    }

    public function has_errors()
    {
        return !empty($this->general_errors);
    }

    public function get_errors()
    {
        return $this->general_errors;
    }
}
