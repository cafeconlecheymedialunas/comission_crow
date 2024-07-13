<?php

class PublicFront{
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this,'handle_scripts' ) );
    }
    function handle_scripts() {
        wp_enqueue_media();
        wp_enqueue_script('media-upload');
        wp_enqueue_style('media-views');
        wp_enqueue_script( 'popper',"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js", array("jquery"),"1.14.3", true );
        wp_enqueue_script( 'bootstrap',"https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js", array("jquery"),"4.1.3", true );
        wp_enqueue_script( 'sweetalertjs',"https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js", array("jquery"),"4.1.3", true );
      

        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'), null, true);
        wp_enqueue_script( 'authjs', get_template_directory_uri()."/assets/auth.js", array("jquery"),"1.0.0", true );
        wp_localize_script('authjs', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'kamerpower_login_nonce' => wp_create_nonce('kamerpower-login-nonce')));




        wp_enqueue_script('profile-script', get_template_directory_uri() . '/assets/profile.js', array('jquery'), '1.0', true);

        // Localizar el script con la URL de AJAX
        wp_localize_script('profile-script', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));

        wp_enqueue_style('sweetalertcss','https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css', array(), '11.2.2', 'all');
        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',array(),"4.0.13","all");
    }
   
}