<?php
use Carbon_Fields\Container\Container;
use Carbon_Fields\Field;

class Admin
{

    private $languages = array(
        'af' => 'Afrikaans',
        'sq' => 'Albanian',
        'am' => 'Amharic',
        'ar' => 'Arabic',
        'hy' => 'Armenian',
        'az' => 'Azerbaijani',
        'eu' => 'Basque',
        'be' => 'Belarusian',
        'bn' => 'Bengali',
        'bs' => 'Bosnian',
        'bg' => 'Bulgarian',
        'ca' => 'Catalan',
        'ceb' => 'Cebuano',
        'ny' => 'Chichewa',
        'zh' => 'Chinese',
        'co' => 'Corsican',
        'hr' => 'Croatian',
        'cs' => 'Czech',
        'da' => 'Danish',
        'nl' => 'Dutch',
        'en' => 'English',
        'eo' => 'Esperanto',
        'et' => 'Estonian',
        'tl' => 'Filipino',
        'fi' => 'Finnish',
        'fr' => 'French',
        'fy' => 'Frisian',
        'gl' => 'Galician',
        'ka' => 'Georgian',
        'de' => 'German',
        'el' => 'Greek',
        'gu' => 'Gujarati',
        'ht' => 'Haitian Creole',
        'ha' => 'Hausa',
        'haw' => 'Hawaiian',
        'iw' => 'Hebrew',
        'hi' => 'Hindi',
        'hmn' => 'Hmong',
        'hu' => 'Hungarian',
        'is' => 'Icelandic',
        'ig' => 'Igbo',
        'id' => 'Indonesian',
        'ga' => 'Irish',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'jv' => 'Javanese',
        'kn' => 'Kannada',
        'kk' => 'Kazakh',
        'km' => 'Khmer',
        'rw' => 'Kinyarwanda',
        'ko' => 'Korean',
        'ku' => 'Kurdish',
        'ky' => 'Kyrgyz',
        'lo' => 'Lao',
        'la' => 'Latin',
        'lv' => 'Latvian',
        'lt' => 'Lithuanian',
        'lb' => 'Luxembourgish',
        'mk' => 'Macedonian',
        'mg' => 'Malagasy',
        'ms' => 'Malay',
        'ml' => 'Malayalam',
        'mt' => 'Maltese',
        'mi' => 'Maori',
        'mr' => 'Marathi',
        'mn' => 'Mongolian',
        'my' => 'Myanmar (Burmese)',
        'ne' => 'Nepali',
        'no' => 'Norwegian',
        'or' => 'Odia (Oriya)',
        'ps' => 'Pashto',
        'fa' => 'Persian',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'pa' => 'Punjabi',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sm' => 'Samoan',
        'gd' => 'Scots Gaelic',
        'sr' => 'Serbian',
        'st' => 'Sesotho',
        'sn' => 'Shona',
        'sd' => 'Sindhi',
        'si' => 'Sinhala',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'so' => 'Somali',
        'es' => 'Spanish',
        'su' => 'Sundanese',
        'sw' => 'Swahili',
        'sv' => 'Swedish',
        'tg' => 'Tajik',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'th' => 'Thai',
        'tr' => 'Turkish',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'ug' => 'Uyghur',
        'uz' => 'Uzbek',
        'vi' => 'Vietnamese',
        'cy' => 'Welsh',
        'xh' => 'Xhosa',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'zu' => 'Zulu',
    );
    public function __construct()
    {
        add_action('admin_head', array($this,'custom_admin_css_for_post_types' ) );
        $this->create_post_types();
        
        add_action('init', array($this, 'create_role_business'));
        add_action('init', array($this, 'create_role_agent'));

        add_action('carbon_fields_register_fields', array($this, 'register_opportunity_fields'));
        add_action('carbon_fields_register_fields', array($this, 'register_commercial_agent_fields'));
        add_action('carbon_fields_register_fields', array($this, 'register_company_fields'));
        add_action('carbon_fields_register_fields', array($this, 'register_deal_fields'));
    }
    public function create_post_types()
    {
        $custom_post_types = Custom_Post_Type::get_instance();

        $custom_post_types->register('opportunity', 'Opportunity', 'Opportunities', ['menu_icon' => 'dashicons-search']);
        $custom_post_types->register('review', 'Review', 'Reviews', ['menu_icon' => 'dashicons-star-empty']);
        $custom_post_types->register('company', 'Company', 'Companies', ['menu_icon' => 'dashicons-store']);
        $custom_post_types->register('commercial_agent', 'Commercial Agent', 'Comercial Agents', ['menu_icon' => 'dashicons-businessperson']);

        $custom_post_types->register('deal', 'Deal', 'Deals', ['menu_icon' => 'dashicons-heart']);
        $custom_post_types->register('payment', 'Payment', 'Payments', ['menu_icon' => 'dashicons-yes-alt']);
    }

    public function create_role_business()
    {
        // Obtener el objeto de roles de WordPress
        global $wp_roles;

        // Si el objeto de roles no está disponible, intentar cargarlo
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Comprobar si el rol ya está registrado
        if (!$wp_roles->get_role('business')) {
            // Obtener las capacidades del rol "Subscriber"
            $subscriber_caps = $wp_roles->get_role('subscriber')->capabilities;

            // Registrar el nuevo rol "Business" con las mismas capacidades que "Subscriber"
            $wp_roles->add_role('business', __('Business'), $subscriber_caps);
        }
    }

    public function create_role_agent()
    {
        // Obtener el objeto de roles de WordPress
        global $wp_roles;

        // Si el objeto de roles no está disponible, intentar cargarlo
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Comprobar si el rol ya está registrado
        if (!$wp_roles->get_role('agent')) {
            // Obtener las capacidades del rol "Subscriber"
            $subscriber_caps = $wp_roles->get_role('subscriber')->capabilities;

            // Registrar el nuevo rol "Business" con las mismas capacidades que "Subscriber"
            $wp_roles->add_role('agent', __('Agent'), $subscriber_caps);
        }
    }

    public function register_opportunity_fields(){
       
        Container::make('post_meta', __('Oportunity Info'))
                ->add_tab(__('Info'), array(
                    Field::make('text', 'sector', __('Sector')),
                    Field::make('radio', 'target_audience', __('Target Audience'))->set_options(array(
                        'empresas' => "Companies",
                        'individuals' => "Individuals",
                    )),
                    Field::make('text', 'company_type', __('Company Type')),
                    Field::make('text', 'language', __('Language')),
                    Field::make('text', 'location', __('Location')),
                    Field::make('text', 'age', __('Age')),
                    Field::make('text', 'currency', __('Currency')),
    
                ))
                ->add_tab(__('Pricing'), array(
                    Field::make('text', 'price', __('Price')),
                    Field::make('text', 'commission', __('Commission')),
                    Field::make('checkbox', 'deliver_leads', 'Deliver Leads?')
                        ->set_option_value('yes'),
                    Field::make('text', 'sales_cycle_estimation', __('Sales cycle estimation')),
                ))->add_tab(__('Advanced'), array(
    
                Field::make('media_gallery', 'supporting_materials', __('Supporting materials'))->set_type('text'),
                Field::make('complex', 'videos', __('Videos urls'))
                    ->add_fields(array(
                        Field::make('oembed', 'video', __('Url Video')),
                    )),
                Field::make('rich_text', 'tips', __('Tips')),
    
            ))->where('post_type', '=', 'opportunity');
    
        
      
    
    }

    public function register_company_fields(){

        Container::make('post_meta', __('Company Info'))
        ->add_fields( array(
            
            Field::make('media_gallery', 'company_logo', __('Company Logo'))->set_type('image'),
           
            Field::make( 'text', 'business_name', __( 'Company Name' ) ),
            Field::make( 'text', 'sector', __( 'Sector' ) ),
            Field::make( 'text', 'activity', __( 'Activity' ) ),
            Field::make('rich_text', 'description', __('Description')),
            Field::make( 'text', 'location', __( 'Location' ) ),
            Field::make( 'text', 'employees_number', __( 'Number of Employees' ) ),
            Field::make( 'text', 'instagram_url', __( 'Instagram Url' ) ),

        )) ->where( 'post_type', '=', 'company' );
    }

    public function register_commercial_agent_fields(){
        
        Container::make('post_meta', __('Agent Info'))
           
        ->add_fields( array(
            Field::make( 'association', 'agent', __( 'Commercial Agent' ) )
            ->set_types( array(
                array(
                    'type'      => 'user',
                )
            ) ),
            Field::make('media_gallery', 'avatar', __('Profile Image'))->set_type('image'),
            Field::make( 'rich_text', 'description', __( 'Description' ) ),
            Field::make('multiselect', 'language', __('Languages'))
            ->set_options($this->languages),
            Field::make('text', 'location', __('Location')),
            Field::make('select', 'seller_type', __('Seller Type'))
            ->set_options(array("agency" => "Agency","freelance"=>"Freelance")),
            Field::make('select', 'selling_methods', __('Selling Methods'))
            ->set_options(array(
                'cold_calling' => 'Cold Calling',
                'email' => 'Email',
                'face_to_face' => 'Face to Face',
                'online_demos' => 'Online Demos',
                'appointment_setting' => 'Appointment Setting'
            )),
            Field::make( 'date_time', 'date', 'Deal Date' ),
            

        ))->where( 'post_type', '=', 'commercial_agent' );
    }

    public function register_deal_fields(){
        Container::make('post_meta', __('Deal Conditions'))
           
        ->add_fields( array(
            Field::make( 'association', 'agent', __( 'Commercial Agent' ) )
            ->set_types( array(
                array(
                    'type'      => 'post',
                    'post_type' => 'agent',
                )
            ) ),
            Field::make( 'association', 'company', __( 'Company' ) )
            ->set_types( array(
                array(
                    'type'      => 'post',
                    'post_type' => 'company',
                )
            ) ),
            Field::make( 'association', 'opportunity', __( 'Opportunity' ) )
            ->set_types( array(
                array(
                    'type'      => 'post',
                    'post_type' => 'opportunity',
                )
            ) ),
            Field::make( 'date_time', 'date', 'Deal Date' ),
            Field::make( 'text', 'commission', 'Commission' ),

        ))->where( 'post_type', '=', 'deal' );
    }

    public function register_review_fields(){
        Container::make('post_meta', __('Review'))
           
        ->add_fields( array(
            Field::make( 'association', 'agent', __( 'Commercial Agent' ) )
            ->set_types( array(
                array(
                    'type'      => 'post',
                    'post_type' => 'agent',
                )
            ) ),
            Field::make('select', 'seller_type', __('Seller Type'))
            ->set_options(array(
                "1" => 1,
                "2"=>2,
                "3" =>3,
                "4"=>4,
                "5"=>5
            )),
        ))->where( 'post_type', '=', 'deal' );
    }

    public function custom_admin_css_for_post_types() {
        global $typenow;
    
        // Lista de tipos de publicaciones personalizadas
        $custom_post_types = array('opportunity', 'review', 'deal',"company","commercial_agent","payment");
    
        // Verificar si el tipo de publicación actual está en la lista
        if (in_array($typenow, $custom_post_types)) {
            echo '
            <style>
                /* Ejemplo de CSS personalizado */
                .edit-post-meta-boxes-area__container{
                    max-width: 840px;
                    margin:0 auto;
                }
                .postbox-header{
                    border: solid #e2e4e7;
                    border-width: 0 1px 0 1px;
                }
            </style>
            ';
        }
    }
  

}


