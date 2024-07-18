<?php

class PublicFront
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this,'handle_scripts' ]);
        
    }
    public function handle_scripts()
    {
        wp_enqueue_media();
        wp_enqueue_script('media-upload');
        wp_enqueue_style('media-views');
        wp_enqueue_script('tinymce');

        wp_enqueue_script('popper', "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js", ["jquery"], "1.14.3", true);
        wp_enqueue_script('bootstrap', "https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js", ["jquery"], "4.1.3", true);

        wp_enqueue_style('sweetalertcss', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css', [], '11.2.2', 'all');
        wp_enqueue_script('sweetalertjs', "https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js", ["jquery"], "4.1.3", true);
       
        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', [], "4.0.13", "all");
        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);
       
        wp_enqueue_style('fontawesomcss', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css", [], "6.5.2", "all");

        wp_enqueue_script('quill-editorjs', "https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js", ["jquery"], "7.3.0", true);
        wp_enqueue_style('quill-editorcss', "https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css", [], "7.3.0", "all");
        

        wp_enqueue_script('jqueryvalidatejs', "https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js", ["jquery"], "1.19.3", true);
        
        wp_enqueue_script('main', get_template_directory_uri() . '/assets/main.js', ['jquery'], '1.0', true);

        wp_enqueue_script('authjs', get_template_directory_uri()."/assets/auth.js", ["jquery"], "1.0.0", true);
        
        wp_localize_script('authjs', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php'), 'kamerpower_login_nonce' => wp_create_nonce('kamerpower-login-nonce')]);

        wp_enqueue_script('opportunity', get_template_directory_uri() . '/assets/opportunity.js', ['jquery'], '1.0', true);

        wp_enqueue_script('profile', get_template_directory_uri() . '/assets/profile.js', ['jquery'], '1.0', true);

       

        $ajax_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
        ];
        // Localizar el script con la URL de AJAX
        wp_localize_script('opportunity', 'ajax_object',$ajax_data );
        wp_localize_script('profile', 'ajax_object',$ajax_data );

       
    }

}
