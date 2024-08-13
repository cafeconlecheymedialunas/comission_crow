<?php

class PublicFront
{
    private $theme_version;
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'handle_scripts']);
        $this->theme_version = wp_get_theme()->get('Version');
    }

    public function handle_scripts()
    {
        
        wp_enqueue_script('jquery');

      
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

      
        wp_enqueue_script('popperjs', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', ['jquery'], '1.14.3', true);
        wp_enqueue_script('bootstrapjs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', ['jquery'], '5.3.3', true);
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '', 'all');
       
        wp_enqueue_style('fontawesomcss', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], '6.5.2', 'all');
      
        
    
        
        if (is_page_template('page-templates/page-dashboard.php') || is_page_template('page-templates/page-auth.php')) {
            wp_enqueue_media();
            wp_enqueue_script('media-upload');
            wp_enqueue_style('media-views');
            wp_enqueue_script('tinymce');
         
            wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', ['jquery'], '1.19.3', true);
            wp_enqueue_style('quill-editorcss', 'https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css', [], '2.0.2', 'all');
            wp_enqueue_script('quill-editorjs', 'https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js', ['jquery'], '2.0.2', true);
            wp_enqueue_style('select2bootstracss', 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css', [], "1.3.0", 'all');
            wp_enqueue_script('jqueryvalidatejs', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', ['jquery'], '1.19.5', true);
            wp_enqueue_style('sweetalertcss', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css', [], '11.2.2', 'all');
            wp_enqueue_script('sweetalertjs', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js', ['jquery'], '11.2.2', true);
            wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', [], '4.0.13', 'all');
            wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], '4.0.13', true);
            $this->enqueue_script_with_assets('main');
            $this->enqueue_script_with_assets('auth');
            
            $this->enqueue_script_with_assets('opportunity');
            $this->enqueue_script_with_assets('profile');
            $this->enqueue_script_with_assets('contract');
            $this->enqueue_script_with_assets('commission');
            $this->enqueue_script_with_assets('dispute');
            $this->enqueue_script_with_assets('payment');
            $this->enqueue_script_with_assets('deposit');
          
        }
        if (
            is_page_template('page-templates/page-dashboard.php') || 
            is_page_template('page-templates/page-auth.php') || 
            is_page_template('page-templates/find-commercial-agents.php') || 
            is_page_template('page-templates/find-opportunities.php') 
        ) {
            $this->enqueue_style_with_assets('main');
            $this->enqueue_style_with_assets('header');
            $this->enqueue_style_with_assets('auth');
            $this->enqueue_style_with_assets('admin-dashboard');
        }
        if(is_front_page()){
            
            wp_enqueue_script('slickjs', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', ['jquery'], '1.9.0', true);
            wp_enqueue_style('slickcss', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', [], '1.9.0', 'all');
            wp_enqueue_style('slickthemecss', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', [], '1.9.0', 'all');
            $this->enqueue_script_with_assets("frontend");
            $this->enqueue_style_with_assets('frontend');
            $this->enqueue_script_with_assets("home");
            $this->enqueue_style_with_assets('home');
           
        }

        if (is_page_template('page-templates/find-opportunities.php') ) {
            $this->enqueue_script_with_assets('find-opportunities');
        }

        if (is_page_template('page-templates/find-commercial-agents.php')) {
            $this->enqueue_script_with_assets('find-commercial-agents');
        }

        // Encolar los estilos usando archivos .asset.php.
        wp_enqueue_style('style', get_theme_file_uri('style.css'), [], $this->theme_version, 'all');
       

        // Localizar scripts con datos AJAX.
        $ajax_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
            "home_url" => home_url()
        ];
        $this->localize_script('opportunity', $ajax_data);
        $this->localize_script('profile', $ajax_data);
        $this->localize_script('contract', $ajax_data);
        $this->localize_script('payment', $ajax_data);
        $this->localize_script('deposit', $ajax_data);
        $this->localize_script('find-opportunities', $ajax_data);
        $this->localize_script('find-commercial-agents', $ajax_data);
        $this->localize_script('home', $ajax_data);
    }

    private function enqueue_script_with_assets($handle)
    {
      

   
        
            wp_enqueue_script(
                $handle,
                get_template_directory_uri() . '/dist/js/' . $handle . '.js',
                ["jquery"],
                $this->theme_version ,
                true
            );
        
    }

    private function enqueue_style_with_assets($handle)
    {
       
            wp_enqueue_style(
                $handle,
                get_template_directory_uri() . '/dist/css/' . $handle . '.css',
                [],
                $this->theme_version
            );
        
    }

    private function localize_script($handle, $data)
    {
        wp_localize_script($handle, 'ajax_object', $data);
    }
}
