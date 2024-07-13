<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Custom_Taxonomy')) {
    class Custom_Taxonomy
    {
        private static $instance = null;

        // Constructor privado para prevenir instanciación directa
        private function __construct()
        {
        }

        // Método para obtener la instancia única
        public static function get_instance()
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        // Método para registrar una taxonomía personalizada
        public function register($taxonomy, $object_type, $singular, $plural, $custom_args = [])
        {
            $labels = $this->get_labels($singular, $plural);

            $default_args = [
                'labels'            => $labels,
                'hierarchical'      => true,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'show_tagcloud'     => true,
                'show_in_quick_edit'=> true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
            ];

            $args = array_merge($default_args, $custom_args);

            add_action('init', function () use ($taxonomy, $object_type, $args) {
                $this->register_taxonomy($taxonomy, $object_type, $args);
            });
        }

        // Método para registrar la taxonomía
        private function register_taxonomy($taxonomy, $object_type, $args)
        {
            register_taxonomy($taxonomy, $object_type, $args);
        }

        // Método para crear etiquetas
        private function get_labels($singular, $plural)
        {
            $labels = [
                'name'                       => _x($plural, 'Taxonomy General Name', 'text_domain'),
                'singular_name'              => _x($singular, 'Taxonomy Singular Name', 'text_domain'),
                'menu_name'                  => __($plural, 'text_domain'),
                'all_items'                  => __('All ' . $plural, 'text_domain'),
                'parent_item'                => __('Parent ' . $singular, 'text_domain'),
                'parent_item_colon'          => __('Parent ' . $singular . ':', 'text_domain'),
                'new_item_name'              => __('New ' . $singular . ' Name', 'text_domain'),
                'add_new_item'               => __('Add New ' . $singular, 'text_domain'),
                'edit_item'                  => __('Edit ' . $singular, 'text_domain'),
                'update_item'                => __('Update ' . $singular, 'text_domain'),
                'view_item'                  => __('View ' . $singular, 'text_domain'),
                'separate_items_with_commas' => __('Separate ' . $plural . ' with commas', 'text_domain'),
                'add_or_remove_items'        => __('Add or remove ' . $plural, 'text_domain'),
                'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
                'popular_items'              => __('Popular ' . $plural, 'text_domain'),
                'search_items'               => __('Search ' . $plural, 'text_domain'),
                'not_found'                  => __('Not Found', 'text_domain'),
                'no_terms'                   => __('No ' . $plural, 'text_domain'),
                'items_list'                 => __($plural . ' list', 'text_domain'),
                'items_list_navigation'      => __($plural . ' list navigation', 'text_domain'),
            ];

            return $labels;
        }
    }
}

// Ejemplo de uso
