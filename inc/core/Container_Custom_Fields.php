<?php
use Carbon_Fields\Container\Container;
// Clase para manejar la creación y configuración de campos personalizados con Carbon Fields
class Container_Custom_Fields {

    // Instancia única de la clase
    private static $instance = null;

    // Método estático para obtener la instancia única (singleton)
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Constructor privado para prevenir la instanciación directa
    private function __construct() {}

    // Método para registrar campos personalizados desde un array de configuraciones
    public function register_container_fields($fields_config) {
        foreach ($fields_config as $container_type => $configs) {
            foreach ($configs as $config) {
                $container_name = $config['container_name'];
                $post_type = $config['post_type'];
                $fields = $config['fields'];

                // Registrar campos personalizados utilizando Carbon Fields
                add_action('carbon_fields_register_fields', function() use ($container_type, $container_name, $post_type, $fields) {
                    Container::make($container_type, $container_name)
                        ->where('post_type', '=', $post_type)
                        ->add_fields($fields);
                });
            }
        }
    }

    // Método para bootear Carbon Fields
    public function boot_carbon_fields() {
        require_once get_template_directory().'vendor/autoload.php';
        \Carbon_Fields\Carbon_Fields::boot();
    }
}
