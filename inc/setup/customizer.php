<?php

defined('ABSPATH') || exit;

/**
 * Implement Theme Customizer additions and adjustments.
 * https://codex.wordpress.org/Theme_Customization_API
 *
 * How do I "output" custom theme modification settings? https://developer.wordpress.org/reference/functions/get_theme_mod
 * echo get_theme_mod( 'copyright_info' );
 * or: echo get_theme_mod( 'copyright_info', 'Default (c) Copyright Info if nothing provided' );
 *
 * "sanitize_callback": https://codex.wordpress.org/Data_Validation
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 *
 * @return void
 */
function comission_crow_customize($wp_customize)
{
    /**
     * Initialize sections
     */
    $wp_customize->add_section(
        'theme_header_section',
        [
            'title'    => __('Header', 'comission_crow'),
            'priority' => 1000,
        ]
    );

    /**
     * Section: Page Layout
     */
    // Header Logo.
    $wp_customize->add_setting(
        'header_logo',
        [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'header_logo',
            [
                'label'       => __('Upload Header Logo', 'comission_crow'),
                'description' => __('Height: &gt;80px', 'comission_crow'),
                'section'     => 'theme_header_section',
            ]
        )
    );

    // Predefined Navbar scheme.
    $wp_customize->add_setting(
        'navbar_scheme',
        [
            'default'           => 'default',
            'sanitize_callback' => 'sanitize_text_field',
        ]
    );
    $wp_customize->add_control(
        'navbar_scheme',
        [
            'type'    => 'radio',
            'label'   => __('Navbar Scheme', 'comission_crow'),
            'section' => 'theme_header_section',
            'choices' => [
                'navbar-light bg-light'  => __('Default', 'comission_crow'),
                'navbar-dark bg-dark'    => __('Dark', 'comission_crow'),
                'navbar-dark bg-primary' => __('Primary', 'comission_crow'),
            ],
        ]
    );

    // Fixed Header?
    $wp_customize->add_setting(
        'navbar_position',
        [
            'default'           => 'static',
            'sanitize_callback' => 'sanitize_text_field',
        ]
    );
    $wp_customize->add_control(
        'navbar_position',
        [
            'type'    => 'radio',
            'label'   => __('Navbar', 'comission_crow'),
            'section' => 'theme_header_section',
            'choices' => [
                'static'       => __('Static', 'comission_crow'),
                'fixed_top'    => __('Fixed to top', 'comission_crow'),
                'fixed_bottom' => __('Fixed to bottom', 'comission_crow'),
            ],
        ]
    );

    // Search?
    $wp_customize->add_setting(
        'search_enabled',
        [
            'default'           => '1',
            'sanitize_callback' => 'sanitize_text_field',
        ]
    );
    $wp_customize->add_control(
        'search_enabled',
        [
            'type'    => 'checkbox',
            'label'   => __('Show Searchfield?', 'comission_crow'),
            'section' => 'theme_header_section',
        ]
    );
}
add_action('customize_register', 'comission_crow_customize');


