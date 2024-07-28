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
        add_action('init', [$this, 'create_role_commercial_agent']);

        add_action('carbon_fields_register_fields', [$this, 'register_opportunity_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_commercial_agent_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_company_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_contract_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_review_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_transaction_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_dispute_fields']);
        add_action('carbon_fields_register_fields', [$this, 'register_commission_request_fields']);
        add_filter('use_block_editor_for_post_type', [$this,'disable_block_editor_for_post_type'], 10, 2);
    
    }

   
    public function create_post_types()
    {
        $CustomPostTypes = CustomPostType::get_instance();
        $CustomTaxonomy = CustomTaxonomy::get_instance();
       
        //Cpt
        $CustomPostTypes->register('opportunity', 'Opportunity', 'Opportunities', ['menu_icon' => 'dashicons-search']);
        $CustomPostTypes->register('review', 'Review', 'Reviews', ['menu_icon' => 'dashicons-star-empty']);
        $CustomPostTypes->register('company', 'Company', 'Companies', ['menu_icon' => 'dashicons-store']);
        $CustomPostTypes->register('commercial_agent', 'Commercial Agent', 'Commercial Agents', ['menu_icon' => 'dashicons-businessperson']);
        $CustomPostTypes->register('contract', 'Contract', 'Contracts', ['menu_icon' => 'dashicons-heart']);
        $CustomPostTypes->register('transaction', 'Transaction', 'Transactions', ['menu_icon' => 'dashicons-bank']);
        $CustomPostTypes->register('commission_request', 'Commission Request', 'Commission Requests', ['menu_icon' => 'dashicons-bank']);
        $CustomPostTypes->register('dispute', 'Dispute', 'Disputes', ['menu_icon' => 'dashicons-warning']);

        //Taxonomies
        $CustomTaxonomy->register('skill', ['commercial_agent'], 'Skill', 'Skills');
        $CustomTaxonomy->register('selling_method', ['commercial_agent'], 'Selling Method', 'Selling Methods');
        $CustomTaxonomy->register('industry', ['commercial_agent','company',"opportunity"], 'Industry', 'Industries');
        $CustomTaxonomy->register('seller_type', ['commercial_agent'], 'Seller Type', 'Seller Types');

        $CustomTaxonomy->register('activity', ['company'], 'Activity', 'Activities');
        $CustomTaxonomy->register('type_of_company', ["opportunity"], 'Company Type', 'Company Types');
        $CustomTaxonomy->register('country', ['company',"commercial_agent","opportunity"], 'Country', 'Countries');
        $CustomTaxonomy->register('language', ["commercial_agent","opportunity"], 'Language', 'Languages');
        $CustomTaxonomy->register('currency', ["opportunity"], 'Currency', 'Currencies');
    }

    public function create_role_company()
    {
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Comprobar si el rol ya está registrado
        if (!$wp_roles->get_role('company')) {
           
            $subscriber_caps = $wp_roles->get_role('subscriber')->capabilities;

            $wp_roles->add_role('company', __('Company'), $subscriber_caps);
        }
        $role = get_role('company');
        if ($role) {
            $role->remove_cap('read');
            $role->remove_cap('edit_posts');
            $role->remove_cap('delete_posts');
        }
    }

    public function create_role_commercial_agent()
    {
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
       
        if (!$wp_roles->get_role('commercial_agent')) {
            
            $subscriber_caps = $wp_roles->get_role('subscriber')->capabilities;

            $wp_roles->add_role('commercial_agent', __('Commercial Agent'), $subscriber_caps);
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

        Container::make('post_meta', __('Commercial Agent Info'))

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

    public function register_contract_fields()
    {
        Container::make('post_meta', __('Contract Conditions'))

            ->add_fields([
                Field::make('select', 'commercial_agent', __('Commercial Agent'))
                ->add_options([$this,'get_commercial_agents']),

                Field::make('select', 'company', __('Company'))
                ->add_options([$this,'get_companies']),


                Field::make('select', 'opportunity', __('Opportunity'))
                ->add_options([$this,'get_opportunities']),

               
            
                Field::make('date_time', 'date', 'Contract Date'),

                Field::make('text', 'commission', 'Commission'),

                Field::make('text', 'minimal_price', 'Minimal Price'),

                Field::make('select', 'status', __('Status'))
                ->set_options([$this,"get_contract_status"]),
                
                Field::make('date_time', 'finalization_date', __('Finalization')),


                Field::make('complex', 'status_history', 'Contract Status History')
                ->add_fields([
                    Field::make('select', 'history_status', __('Status'))
                    ->set_options([$this,"get_contract_status"]),
                    Field::make('text', 'date_status', 'Date'),
                    Field::make('select', 'changed_by', __('Changed by:'))->add_options([$this,'get_users']),

                   
                ])
                ->set_layout('tabbed-horizontal')

            ])->where('post_type', '=', 'contract');

          
    }

  

    
    public function register_commission_request_fields()
    {
        Container::make('post_meta', __('Commission Request Conditions'))

            ->add_fields([

                Field::make('select', 'contract_id', __('Contract'))
                ->add_options([$this,'get_contracts']),

                Field::make('complex', 'items', __('Cart Items'))
                ->add_fields([
             
                    Field::make('text', 'price_paid', 'Price paid'),
                    Field::make('text', 'quantity', 'Quantity'),
                    Field::make('text', 'subtotal', 'Subtotal'),
                    Field::make('media_gallery', 'invoice', 'Invoice'),
                    Field::make('text', 'detail', 'Detail'),
                ]),

                Field::make('text', 'total_cart', 'Total'),

                Field::make('text', 'total_agent', 'Total Agent'),
            
                Field::make('date_time', 'date', 'Commission Request Date'),
        
                Field::make('rich_text', 'comments', 'Comments'),
                Field::make('select', 'status', __('Status'))
                ->set_options([$this,"get_status_commission_request"]),
                
                Field::make('complex', 'status_history', 'Contract Status History')
                ->add_fields([
                    Field::make('select', 'history_status', __('Status'))
                    ->set_options([$this,"get_status_commission_request"]),
                    Field::make('text', 'date_status', 'Date'),
                    Field::make('select', 'changed_by', __('Changed by:'))->add_options([$this,'get_users']),

                   
                ])
                ->set_layout('tabbed-horizontal')
                
            ])->where('post_type', '=', 'commission_request');

          
    }

    public function register_transaction_fields()
    {
        Container::make('post_meta', __('Transaction Conditions'))

            ->add_fields([

                Field::make('select', 'commission_request_id', __('Contract'))
                ->add_options([$this,'get_commission_requests']),

    
                Field::make('select', 'source', 'Source')->add_options([
                    "deposit" => "Deposit",
                    "stripe" => "Stripe"
                ]),

                Field::make('complex', 'deposits', __('Invoices'))
                ->add_fields([
                    Field::make('text', 'title', 'Title'),
                    Field::make('file', 'invoice', 'Invoice'),
                    Field::make('text', 'description', 'Description'),
                    Field::make('text', 'bank_account_name_holder', __('Name of account holder')),
                    Field::make('text', 'bank_account_id_holder', __('Number of ID of account holder')),
                    Field::make('text', 'bank_account_number', __('Bank Account Number')),
                    Field::make('text', 'bak_account_cvu_alias', __('CVU or Alias')),

                ])->set_layout("tabbed-horizontal"),


                Field::make('text', 'payment_stripe_id', 'Stripe Payment Id'),
                

            
                Field::make('date_time', 'date', 'Transaction Date'),

                Field::make('select', 'status', __('Status'))
                ->set_options([$this,"get_status_commission_request"]),
                
                Field::make('complex', 'status_history', 'Contract Status History')
                ->add_fields([
                    Field::make('select', 'history_status', __('Status'))
                    ->set_options([$this,"get_status_commission_request"]),
                    Field::make('text', 'date_status', 'Date'),
                    Field::make('select', 'changed_by', __('Changed by:'))->add_options([$this,'get_users']),

                   
                ])
                ->set_layout('tabbed-horizontal')

                
    

            



            ])->where('post_type', '=', 'deposit');

          
    }

    public function register_dispute_fields()
    {
        Container::make('post_meta', __('Dispute Conditions'))

            ->add_fields([
         

                Field::make('select', 'transaction_id', __('Transaction'))
                ->add_options([$this,'get_transactions']),



                Field::make('complex', 'messages', __('Messages'))
                ->add_fields([
                    Field::make('select', 'from', __('From:'))
                    ->add_options([$this,'get_users']),
    
                    Field::make('select', 'to', __('To:'))
                    ->add_options([$this,'get_users']),
            
                    Field::make('rich_text', 'message', 'Mesage'),
                ]),

              

                Field::make('select', 'user_winnerr_dispute', __('Wiiner Dispute:'))
                    ->add_options([$this,'get_users']),
            
                
                Field::make('rich_text', 'admin_decission_comments', __('Admin Comments:')),
                

                Field::make('select', 'status', __('Status'))
                ->set_options([$this,"get_statuses_dispute"]),

            ])->where('post_type', '=', 'dispute');

          
    }

    public function get_contract_status()
    {
        return [
            'pending' => 'Pending',
            "accepted" => "Accepted",
            "refused" => "Refused",
            "finishing" => "Finishing",
            'finished' => 'Finished',
        ];
    }

    public function get_status_commission_request()
    {
        return [
            'pending' => 'Pending',
            "dispute" => "In Dispute",
            "accepted" => "Accepted"
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
        $users = get_users(['role__in' => ['commercial_agent'],]);
    
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
        $users = get_users(['role__in' => ['commercial_agent', 'company',"administrator"],]);
    
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
        $users = get_users(['role__in' => ['company'],]);
    
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

    public function get_transactions()
    {
        $transactions = get_posts([
            'post_type' => 'transaction',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($transactions)) {
            $options[""]="Select a Transaction";
            foreach ($transactions as $transaction) {
                $options[$transaction->ID] = $transaction->post_title;
            }
        }
    
        return $options;
    }

    public function get_deposits()
    {
        $deposits = get_posts([
            'post_type' => 'deposit',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($deposits)) {
            $options[""]="Select a Deposit";
            foreach ($deposits as $deposit) {
                $options[$deposit->ID] = $deposit->post_title;
            }
        }
    
        return $options;
    }

    public function get_contracts()
    {
        $contracts = get_posts([
            'post_type' => 'contract',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);
    
        $options = [];
        if (!empty($contracts)) {
            $options[""]="Select a contract";
            foreach ($contracts as $contract) {
                $options[$contract->ID] = $contract->post_title;
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
                Field::make('select', 'commercial_agent', __('Commercial Agent'))
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
        $CustomPostTypes = ['opportunity', 'review', 'contract', "company", "commercial_agent", "transaction","dispute","commission_request"];

        // Verificar si el tipo de publicación actual está en la lista
        if (in_array($typenow, $CustomPostTypes)) {
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
        $post_types = ['commercial_agent', 'company',"contract","opportunity","review","dispute","transaction","deposit"]; // Reemplaza con tus post types
    
        // Comprueba si el post type actual está en la lista definida
        if (in_array($post_type, $post_types)) {
            return false; // Usa el editor clásico (TinyMCE) para estos post types
        }
    
        return $use_block_editor; // Usa el editor de bloques para todos los demás post types
    }
    

}
