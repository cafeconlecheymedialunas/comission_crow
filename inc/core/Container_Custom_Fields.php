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
                $conditions = $config['conditions'];
                $fields = $config['fields'];

                // Registrar campos personalizados utilizando Carbon Fields
                add_action('carbon_fields_register_fields', function() use ($container_type, $container_name, $conditions, $fields) {
                    $container = Container::make($container_type, $container_name);

                    // Añadir condiciones si las hay
                    if (!empty($conditions)) {
                        foreach ($conditions as $condition) {
                            $container->where($condition[0], $condition[1], $condition[2]);
                        }
                    }

                    // Añadir campos
                    $container->add_fields($fields);
                });
            }
        }
    }

}

// Bootear Carbon Fields

