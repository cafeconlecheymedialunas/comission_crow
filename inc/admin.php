<?php
use Carbon_Fields\Container\Container;
use Carbon_Fields\Field;

class Admin
{


    public function __construct()
    {
        
        add_action('admin_head', [$this, 'custom_admin_css_for_post_types']);
        $this->create_post_types();

        add_action('init', [$this, 'create_role_company']);
        add_action('init', [$this, 'create_role_agent']);

        add_action('carbon_fields_register_fields', [$this, 'register_opportunity_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_commercial_agent_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_company_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_deal_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_review_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_payments_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_dispute_fields']);
        add_filter('use_block_editor_for_post_type', [$this,'disable_block_editor_for_post_type'], 10, 2);
        
       
    }

   
    public function create_post_types()
    {
        $custom_post_types = Custom_Post_Type::get_instance();
        $custom_taxonomy = Custom_Taxonomy::get_instance();
       
        //Cpt
        $custom_post_types->register('opportunity', 'Opportunity', 'Opportunities', ['menu_icon' => 'dashicons-search']);
        $custom_post_types->register('review', 'Review', 'Reviews', ['menu_icon' => 'dashicons-star-empty']);
        $custom_post_types->register('company', 'Company', 'Companies', ['menu_icon' => 'dashicons-store']);
        $custom_post_types->register('commercial_agent', 'Commercial Agent', 'Comercial Agents', ['menu_icon' => 'dashicons-businessperson']);
        $custom_post_types->register('deal', 'Deal', 'Deals', ['menu_icon' => 'dashicons-heart']);
        $custom_post_types->register('payment', 'Payment', 'Payments', ['menu_icon' => 'dashicons-yes-alt']);
        $custom_post_types->register('dispute', 'Dispute', 'Disputes', ['menu_icon' => 'dashicons-yes-alt']);

        //Taxonomies
        $custom_taxonomy->register('skill', ['commercial_agent'], 'Skill', 'Skills');
        $custom_taxonomy->register('selling_method', ['commercial_agent'], 'Selling Method', 'Selling Methods');
        $custom_taxonomy->register('industry', ['commercial_agent','company',"opportunity"], 'Industry', 'Industries');
        $custom_taxonomy->register('seller_type', ['commercial_agent'], 'Seller Type', 'Seller Types');

        $custom_taxonomy->register('activity', ['company'], 'Activity', 'Activities');
        $custom_taxonomy->register('type_of_company', ["opportunity"], 'Company Type', 'Company Types');
        $custom_taxonomy->register('country', ['company',"commercial_agent","opportunity"], 'Country', 'Countries');
        $custom_taxonomy->register('language', ["commercial_agent","opportunity"], 'Language', 'Languages');
        $custom_taxonomy->register('currency', ["opportunity"], 'Currency', 'Currencies');
    }

    public function create_role_company()
    {
        // Obtener el objeto de roles de WordPress
        global $wp_roles;

        // Si el objeto de roles no está disponible, intentar cargarlo
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Comprobar si el rol ya está registrado
        if (!$wp_roles->get_role('company')) {
            // Obtener las capacidades del rol "Subscriber"
            $subscriber_caps = $wp_roles->get_role('subscriber')->capabilities;

            // Registrar el nuevo rol "Business" con las mismas capacidades que "Subscriber"
            $wp_roles->add_role('company', __('Company'), $subscriber_caps);
        }
        $role = get_role('company');
        if ($role) {
            $role->remove_cap('read');
            $role->remove_cap('edit_posts');
            $role->remove_cap('delete_posts');
            // Puedes retirar más capacidades según tus necesidades
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

    public function register_opportunity_fields()
    {

        Container::make('post_meta', __('Oportunity Info'))
            ->add_tab(__('Info'), [
                Field::make('select', 'company', __('Company'))
                ->add_options([$this,'get_companies']),
                
                Field::make('radio', 'target_audience', __('Target Audience'))->set_options([
                    'companies' => "Companies",
                    'individuals' => "Individuals",
                ]),
               
        
                Field::make('select', 'age', __('Age'))
                ->set_options([
                    'over_18' => 'Over 18',
                    'over_30' => 'Over 30',
                    'over_60' => 'Over 60',
                    'any_age' => 'Any age',
                ]),
                Field::make('select', 'gender', __('Gender'))
                ->set_options([
                    'male' => 'Male',
                    'female' => 'Female',
                    'any_gender' => 'Any gender',
                ]),

            ])
            ->add_tab(__('Pricing'), [
                Field::make('text', 'price', __('Price')),
                Field::make('text', 'commission', __('Commission')),
                Field::make('checkbox', 'deliver_leads', 'Deliver Leads?')
                    ->set_option_value('yes'),
                Field::make('text', 'sales_cycle_estimation', __('Sales cycle estimation')),
            ])->add_tab(__('Materials'), [
            Field::make('media_gallery', 'images', __('Images')),//->set_attribute( 'readOnly', true),
            Field::make('media_gallery', 'supporting_materials', __('Supporting materials')),
            Field::make('complex', 'videos', __('Videos urls'))
                ->add_fields([
                    Field::make('oembed', 'video', __('Url Video')),
                ]),
            Field::make('textarea', 'tips', __('Tips')),

        ])->add_tab(__('Questions'), [

            Field::make('textarea', 'question_1', __('1) What is your company’s elevator pitch?')),
            Field::make('textarea', 'question_2', __('2) Please complete the below value statement: Example: "We help (XXX) in the (XXX) industry (XXX) WITHOUT (XXX) & WITHOUT (XXX).')),
            Field::make('textarea', 'question_3', __('3) How do you currently pitch your company to a prospect?')),
            Field::make('textarea', 'question_4', __('4) What are the most common objections you face within your current sales cycle?')),
            Field::make('textarea', 'question_5', __('5) What strategies do you employ to overcome the objections specified?')),
            Field::make('textarea', 'question_6', __('6) Please give an overview of what company challenges you help your clients overcome?')),

        ])->where('post_type', '=', 'opportunity');

    }



    public function register_company_fields()
    {

        Container::make('post_meta', __('Company Info'))
            ->add_fields([
                Field::make('select', 'user_id', __('User'))
                ->add_options([$this,'get_company_users']),

                Field::make('text', 'company_name', __('Company Name')),
          
                Field::make('text', 'employees_number', __('Number of Employees')),
                Field::make('text', 'website_url', __('website Profile')),
                Field::make('text', 'facebook_url', __('Facebook Profile')),
                Field::make('text', 'instagram_url', __('Instagram Profile')),
                Field::make('text', 'twitter_url', __('Twitter Profile')),
                Field::make('text', 'linkedin_url', __('Linkedin Profile')),
                Field::make('text', 'tiktok_url', __('TikTok Profile')),
                Field::make('text', 'youtube_url', __('Youtube Profile')),
                Field::make('text', 'bank_name', __('Bank Name')),
               

            ])->where('post_type', '=', 'company');
    }

    public function register_commercial_agent_fields()
    {

        Container::make('post_meta', __('Agent Info'))

            ->add_fields([
                Field::make('select', 'user_id', __('User'))
                ->add_options([$this,'get_agent_users']),
                
                Field::make('text', 'years_of_experience', __('Years of experience')),
                Field::make('text', 'bank_account_name_holder', __('Name of account holder')),
                Field::make('text', 'bank_account_id_holder', __('Number of ID of account holder')),
                Field::make('text', 'bank_account_number', __('Bank Account Number')),
                Field::make('text', 'bak_account_cvu_alias', __('CVU or Alias')),

            ])->where('post_type', '=', 'commercial_agent');
    }

    public function register_deal_fields()
    {
        Container::make('post_meta', __('Deal Conditions'))

            ->add_fields([
                Field::make('select', 'commercial_agent', __('Comercial Agent'))
                ->add_options([$this,'get_commercial_agents']),

                Field::make('select', 'company', __('Company'))
                ->add_options([$this,'get_companies']),


                Field::make('select', 'opportunity', __('Opportunity'))
                ->add_options([$this,'get_opportunities']),

               
            
                Field::make('date_time', 'date', 'Deal Date'),

                Field::make('text', 'commission', 'Commission'),

                Field::make('text', 'minimal_price', 'Minimal Price'),

                Field::make('select', 'status', __('Status'))
                ->set_options([$this,"get_deal_status"]),

            ])->where('post_type', '=', 'deal');

          
    }

    public function register_payments_fields()
    {
        Container::make('post_meta', __('Payment Conditions'))

            ->add_fields([

                Field::make('select', 'deal_id', __('Deal'))
                ->add_options([$this,'get_deals']),

                Field::make('complex', 'items', __('Cart Items'))
                ->add_fields([
             
                    Field::make('text', 'price_paid', 'Price paid'),
                    Field::make('text', 'quantity', 'Quantity'),
                    Field::make('text', 'subtotal', 'Subtotal'),
                ]),

                Field::make('text', 'total', 'Total'),
            
                Field::make('date_time', 'date', 'Payment Date'),
                Field::make('text', 'commission', 'Commission'),

                Field::make('complex', 'invoices', __('Invoices'))
                ->add_fields([
                    Field::make('text', 'title', 'Title'),
                    Field::make('media_gallery', 'invoice', 'Invoice'),
                    Field::make('text', 'description', 'Description'),
                ]),

                Field::make('select', 'status', __('Status'))
                ->set_options([$this,"get_statuses_payments"]),

            ])->where('post_type', '=', 'payment');

          
    }

    public function register_dispute_fields()
    {
        Container::make('post_meta', __('Dispute Conditions'))

            ->add_fields([
         

                Field::make('select', 'payment_id', __('Payment'))
                ->add_options([$this,'get_payments']),



                Field::make('complex', 'messages', __('Messages'))
                ->add_fields([
                    Field::make('select', 'from', __('From:'))
                    ->add_options([$this,'get_users']),
    
                    Field::make('select', 'to', __('To:'))
                    ->add_options([$this,'get_users']),
                
                    Field::make('text', 'title', 'title'),
                    Field::make('text', 'message', 'rich_text'),
                ]),

              

                Field::make('select', 'user_winnerr_dispute', __('Wiiner Dispute:'))
                    ->add_options([$this,'get_users']),
            
                
                Field::make('rich_text', 'admin_decission_comments', __('Admin Comments:')),
                

                Field::make('select', 'status', __('Status'))
                ->set_options([$this,"get_statuses_dispute"]),

            ])->where('post_type', '=', 'dispute');

          
    }

    public function get_deal_status()
    {
        return [
            'requested' => 'Requested',
            "accepted" => "Accepted",
            "refused" => "Refused",
            'finished' => 'Finished',
        ];
    }

    public function get_statuses_payments()
    {
        return [
            'request' => 'Request',
            "refused" => "Refused",
            "accepted" => "Accepted",
            "pending_payment" => "Pending",
            "payed" => "Payed"
        ];
    }

    public function get_statuses_dispute()
    {
        return [
            'pending' => 'Pending',
            "resolve" => "Resolve"
        ];
    }
    public function get_target_audiences()
    {
        return [
            'companies' => 'Companies',
            'individual' => 'Individual',
        ];
    }
    public function get_ages()
    {
        return [
            'over_18' => 'Over 18',
            'over_30' => 'Over 30',
            'over_60' => 'Over 60',
            'any_age' => 'Any Age',
        ];
    }
    public function get_genders()
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'any_gender' => 'Any Gender',
        ];
    }
    public function get_agent_users()
    {
        $users = get_users(['role__in' => ['agent', 'administrator'],]);
    
        $options = [""=>"Select an User"];
    
        if (!empty($users)) {
            
            foreach ($users as $user) {
                $options[$user->ID] = $user->display_name;
            }
        }
    
        return $options;
    }


    public function get_users()
    {
        $users = get_users(['role__in' => ['agent', 'company',"administrator"],]);
    
        $options = [""=>"Select an User"];
    
        if (!empty($users)) {
            
            foreach ($users as $user) {
                $options[$user->ID] = $user->display_name;
            }
        }
    
        return $options;
    }

    
    public function get_company_users()
    {
        $users = get_users(['role__in' => ['company', 'administrator'],]);
    
        $options = [""=>"Select an User"];
    
        if (!empty($users)) {
            
            foreach ($users as $user) {
                $options[$user->ID] = $user->display_name;
            }
        }
    
        return $options;
    }

  
   

    
  
 
   

  
    public function get_opportunities()
    {
        $opportunities = get_posts([
            'post_type' => 'opportunity',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($opportunities)) {
            $options[""]="Select a Opportunity";
            foreach ($opportunities as $opportunity) {
                $options[$opportunity->ID] = $opportunity->post_title;
            }
        }
    
        return $options;
    }

    
    public function get_companies()
    {
        $companies = get_posts([
            'post_type' => 'company',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($companies)) {
            $options[""]="Select a Company";
            foreach ($companies as $company) {
                $options[$company->ID] = $company->post_title;
            }
        }
    
        return $options;
    }

    public function get_payments()
    {
        $payments = get_posts([
            'post_type' => 'payment',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($payments)) {
            $options[""]="Select a Payment";
            foreach ($payments as $payment) {
                $options[$payment->ID] = $payment->post_title;
            }
        }
    
        return $options;
    }

    public function get_deals()
    {
        $deals = get_posts([
            'post_type' => 'deal',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($deals)) {
            $options[""]="Select a deal";
            foreach ($deals as $deal) {
                $options[$deal->ID] = $deal->post_title;
            }
        }
    
        return $options;
    }

    public function get_commercial_agents()
    {
        $agents = get_posts([
            'post_type' => 'commercial_agent',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($agents)) {
            $options[""]="Select a Commercial Agent";
            foreach ($agents as $agent) {
                $options[$agent->ID] = $agent->post_title;
            }
        }
    
        return $options;
    }

    public function register_review_fields()
    {
        Container::make('post_meta', __('Review'))

            ->add_fields([
                Field::make('select', 'commercial_agent', __('Comercial Agent'))
                ->add_options([$this,'get_commercial_agents']),
                Field::make('select', 'score', __('Score'))
                    ->set_options([
                        "1" => 1,
                        "2" => 2,
                        "3" => 3,
                        "4" => 4,
                        "5" => 5,
                    ]),
            ])->where('post_type', '=', 'review');
    }

    public function custom_admin_css_for_post_types()
    {
        global $typenow;

        // Lista de tipos de publicaciones personalizadas
        $custom_post_types = ['opportunity', 'review', 'deal', "company", "commercial_agent", "payment","dispute"];

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
    public function disable_block_editor_for_post_type($use_block_editor, $post_type)
    {
        // Define los post types donde quieres deshabilitar el editor de bloques
        $post_types = ['commercial_agent', 'company',"deal","opportunity","review","dispute","payment"]; // Reemplaza con tus post types
    
        // Comprueba si el post type actual está en la lista definida
        if (in_array($post_type, $post_types)) {
            return false; // Usa el editor clásico (TinyMCE) para estos post types
        }
    
        return $use_block_editor; // Usa el editor de bloques para todos los demás post types
    }
    

}
