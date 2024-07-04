<?php
use Carbon_Fields\Container\Container;

if (class_exists('Custom_Post_Type')) {
    $custom_post_types = Custom_Post_Type::get_instance();
    
    $custom_post_types->register('opportunity', 'Opportunity', 'Opportunities', ['menu_icon' => 'dashicons-search']);
    $custom_post_types->register('review', 'Review', 'Reviews', ['menu_icon' => 'dashicons-star-empty']);
    $custom_post_types->register('company', 'Company', 'Companys', ['menu_icon' => 'dashicons-store']);
    $custom_post_types->register('agent', 'Agent', 'Agents', ['menu_icon' => 'dashicons-businessperson']);
    $custom_post_types->register('deal', 'Deal', 'Deals', ['menu_icon' => 'dashicons-heart']);
    
    // Puedes registrar más Custom Post Types aquí de la misma manera
}

$config_file = require_once get_template_directory() . "/inc/data/containers.php";

// Verifica que $config_file sea un array válido antes de usarlo
if (is_array($config_file)) {
    $fields_config = $config_file;

    // Obtén una instancia del manejador de campos personalizados
    $fields_handler = Container_Custom_Fields::get_instance();

    // Registra los campos personalizados utilizando la configuración cargada
    $fields_handler->register_container_fields($fields_config);

    // Inicializa Carbon Fields
    $fields_handler->boot_carbon_fields();
} else {
    // Maneja el caso donde $config_file no es un array válido
    // Puedes agregar un registro de error o manejo según sea necesario
}
?>
