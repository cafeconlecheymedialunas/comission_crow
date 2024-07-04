<?php
// Definición del array de configuración para campos personalizados
return array(
    'user_meta' => array(
        array(
            'container_name' => 'user_custom_fields',
            'fields' => array(
                array(
                    'type' => 'text',
                    'id' => 'user_phone_number',
                    'label' => 'Phone Number',
                ),
                array(
                    'type' => 'textarea',
                    'id' => 'user_bio',
                    'label' => 'Biography',
                ),
                // Puedes añadir más campos de usuario según sea necesario
            ),
        ),
    ),
    'theme_options' => array(
        array(
            'container_name' => 'theme_custom_fields',
            'fields' => array(
                array(
                    'type' => 'text',
                    'id' => 'theme_company_name',
                    'label' => 'Company Name',
                ),
                array(
                    'type' => 'textarea',
                    'id' => 'theme_footer_text',
                    'label' => 'Footer Text',
                ),
                // Puedes añadir más opciones del tema según sea necesario
            ),
        ),
    ),
    'post_meta' => array(
        array(
            'container_name' => 'post_custom_fields_1',
            'post_type' => 'post', // Ejemplo de tipo de entrada (post type)
            'fields' => array(
                array(
                    'type' => 'text',
                    'id' => 'post_author_name',
                    'label' => 'Author Name',
                ),
                array(
                    'type' => 'textarea',
                    'id' => 'post_excerpt',
                    'label' => 'Post Excerpt',
                ),
                // Puedes añadir más campos de metadatos de entradas según sea necesario
            ),
        ),
        array(
            'container_name' => 'post_custom_fields_2',
            'post_type' => 'page', // Ejemplo de tipo de entrada (post type)
            'fields' => array(
                array(
                    'type' => 'text',
                    'id' => 'post_published_date',
                    'label' => 'Published Date',
                ),
                array(
                    'type' => 'text',
                    'id' => 'post_views_count',
                    'label' => 'Views Count',
                ),
                // Puedes añadir más configuraciones de metadatos de entradas aquí
            ),
        ),
    ),
    // Puedes añadir más tipos de configuraciones aquí
);
