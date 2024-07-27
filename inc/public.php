<?php

class PublicFront
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this,'handle_scripts' ]);
        
    }
    public function handle_scripts()
    {
        $theme_version = wp_get_theme()->get('Version');

        // 1. Styles.
       
    
        wp_enqueue_script('jquery');
    
        
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
        wp_enqueue_media();

        wp_enqueue_script('media-upload');
        wp_enqueue_style('media-views');
        wp_enqueue_script('tinymce');


        wp_enqueue_script('popperjs', "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js", ["jquery"], "1.14.3", true);
        
        wp_enqueue_script('bootstrapjs', "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js", ["jquery"], "5.3.3", true);
        wp_enqueue_style('bootstrap', "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css", [], "", 'all');

        wp_enqueue_style('sweetalertcss', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css', [], '11.2.2', 'all');
        wp_enqueue_script('sweetalertjs', "https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js", ["jquery"], "4.1.3", true);
       
        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', [], "4.0.13", "all");
        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);
       
        wp_enqueue_style('fontawesomcss', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css", [], "6.5.2", "all");
        wp_enqueue_style('quill-editorcss', "https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css", [], "7.3.0", "all");
        wp_enqueue_script('quill-editorjs', "https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js", ["jquery"], "7.3.0", true);
        
        wp_enqueue_style('select2bootstracss', "https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css", [], $theme_version, "all");
        ;
  

        wp_enqueue_script('jqueryvalidatejs', "https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js", ["jquery"], "1.19.3", true);
        
        wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], '1.0', true);

        wp_enqueue_script('authjs', get_template_directory_uri()."/assets/js/auth.js", ["jquery"], "1.0.0", true);
        
        wp_localize_script('authjs', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php'), 'kamerpower_login_nonce' => wp_create_nonce('kamerpower-login-nonce')]);

        wp_enqueue_script('opportunity', get_template_directory_uri() . '/assets/js/opportunity.js', ['jquery'], '1.0', true);

        wp_enqueue_script('profile', get_template_directory_uri() . '/assets/js/profile.js', ['jquery'], '1.0', true);

        wp_enqueue_script('contract', get_template_directory_uri() . '/assets/js/contract.js', ['jquery'], '1.0', true);
       
        wp_enqueue_script('commission', get_template_directory_uri() . '/assets/js/commission.js', ['jquery'], '1.0', true);

        wp_enqueue_script('dispute', get_template_directory_uri() . '/assets/js/dispute.js', ['jquery'], '1.0', true);

        
        
        wp_enqueue_style('style', get_theme_file_uri('style.css'), [], $theme_version, 'all');

        wp_enqueue_style('maincss', get_template_directory_uri() . '/assets/css/main.css', [], $theme_version, "all");

        wp_enqueue_style('headercss', get_template_directory_uri() . '/assets/css/header.css', [], $theme_version, "all");

        wp_enqueue_style('authcss', get_template_directory_uri() . '/assets/css/auth.css', [], $theme_version, "all");

        wp_enqueue_style('dashboardcss', get_template_directory_uri() . '/assets/css/dashboard.css', [], $theme_version, "all");

        

        $ajax_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
        ];
        // Localizar el script con la URL de AJAX
        wp_localize_script('opportunity', 'ajax_object', $ajax_data);
        wp_localize_script('profile', 'ajax_object', $ajax_data);
        wp_localize_script('contract', 'ajax_object', $ajax_data);
       
    }

}
