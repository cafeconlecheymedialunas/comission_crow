<?php
use Carbon_Fields\Container\Container;
use Carbon_Fields\Field;

class ContainerCustomFields
{

    private $admin;
    private $status_manager;

    public function __construct($admin)
    {
        $this->admin = $admin;
        $this->status_manager = StatusManager::get_instance();
    }


    public function register_fields()
    {
        $this->register_opportunity_fields();
        $this->register_commercial_agent_fields();
        $this->register_company_fields();
        $this->register_contract_fields();
        $this->register_review_fields();
       
        $this->register_dispute_fields();
        $this->register_commission_request_fields();
        $this->register_payment_fields();
        $this->register_deposit_fields();
        $this->register_theme_options();
        $this->register_home_fields();
        $this->register_taxonomy_industry_fields();
    }

    public function register_company_fields()
    {

        Container::make('post_meta', __('Company Info'))
            ->add_fields([
                Field::make('select', 'user_id', __('User'))
                ->add_options([$this->admin,'get_company_users']),

                Field::make('text', 'company_name', __('Company Name')),
                Field::make('text', 'company_street', __('Company Street')),

                Field::make('text', 'company_number', __('Company Number')),
                Field::make('text', 'company_city', __('Company City')),
                Field::make('text', 'company_state', __('Company State')),
                Field::make('text', 'company_postalcode', __('Company Postal Code')),
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
                ->add_options([$this->admin,'get_agent_users']),
                
                Field::make('text', 'years_of_experience', __('Years of experience')),
                Field::make('text', 'bank_account_name_holder', __('Name of account holder')),
                Field::make('text', 'bank_account_id_holder', __('Number of ID of account holder')),
                Field::make('text', 'bank_account_number', __('Bank Account Number')),
                Field::make('text', 'bak_account_cvu_alias', __('CVU or Alias')),
                Field::make('text', 'stripe_email', __('Stripe Email')),
                Field::make('text', 'wallet_balance', __('Wallet Balance', 'your-textdomain'))
                ->set_default_value('0')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_attribute('min', '0'),
               

            ])->where('post_type', '=', 'commercial_agent');
    }

    
    public function register_opportunity_fields()
    {

        Container::make('post_meta', __('Oportunity Info'))
            ->add_tab(__('Info'), [
                Field::make('select', 'company', __('Company'))
                ->add_options([$this->admin,'get_companies']),
                Field::make('date_time', 'date', 'Date'),
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

    public function register_contract_fields()
    {
        Container::make('post_meta', __('Contract Conditions'))

            ->add_fields([
                Field::make('select', 'commercial_agent', __('Commercial Agent'))
                ->add_options([$this->admin,'get_commercial_agents']),

                Field::make('select', 'company', __('Company'))
                ->add_options([$this->admin,'get_companies']),
                Field::make('select', 'initiating_user', __('Initiating User:'))
                ->add_options([$this->admin,'get_users']),

                Field::make('select', 'opportunity', __('Opportunity'))
                ->add_options([$this->admin,'get_opportunities']),

                Field::make('text', 'sku', 'Sku'),
            
                Field::make('date_time', 'date', 'Contract Date'),

                Field::make('text', 'commission', 'Commission'),

                Field::make('text', 'minimal_price', 'Minimal Price'),

                Field::make('select', 'status', __('Status'))
                ->set_options([$this->admin,"get_contract_status"]),
                
                Field::make('date_time', 'finalization_date', __('Finalization')),


                Field::make('complex', 'status_history', 'Contract Status History')
                ->add_fields([
                    Field::make('select', 'history_status', __('Status'))
                    ->set_options([$this->admin,"get_contract_status"]),
                    Field::make('text', 'date_status', 'Date'),
                    Field::make('select', 'changed_by', __('Changed by:'))->add_options([$this->admin,'get_users']),

                   
                ])
                ->set_layout('tabbed-horizontal')

            ])->where('post_type', '=', 'contract');

          
    }

  

    
    public function register_commission_request_fields()
    {
        Container::make('post_meta', __('Commission Request Conditions'))

            ->add_fields([

                Field::make('select', 'contract_id', __('Contract'))
                ->add_options([$this->admin,'get_contracts']),

                Field::make('complex', 'items', __('Cart Items'))
                ->add_fields([
             
                    Field::make('text', 'price_paid', 'Price paid'),
                    Field::make('text', 'quantity', 'Quantity'),
                    Field::make('text', 'subtotal', 'Subtotal'),
                    Field::make('media_gallery', 'invoice', 'Invoice'),
                    Field::make('text', 'detail', 'Detail'),
                ])->set_layout('tabbed-horizontal'),
             
                Field::make('select', 'initiating_user', __('Initiating User:'))
                    ->add_options([$this->admin,'get_users']),

                Field::make('text', 'total_cart', 'Total'),
                Field::make('text', 'total_agent', 'Total Agent'),
                Field::make('text', 'total_platform', 'Total Platform'),
                Field::make('text', 'total_tax_service', 'Total Tax Serves'),
                Field::make('text', 'total_to_pay', 'Total To Pay'),
                Field::make('media_gallery', 'general_invoice', 'General Invoices'),
                Field::make('date_time', 'date', 'Commission Request Date'),
        
                Field::make('rich_text', 'comments', 'Comments'),
                Field::make('select', 'status', __('Status'))
                ->set_options([$this->status_manager,"get_status_commission_request"]),
                
                Field::make('complex', 'status_history', 'Contract Status History')
                ->add_fields([
                    Field::make('select', 'history_status', __('Status'))
                    ->set_options([$this->admin,"get_status_commission_request"]),
                    Field::make('text', 'date_status', 'Date'),
                    Field::make('select', 'changed_by', __('Changed by:'))->add_options([$this->admin,'get_users']),

                   
                ])
                ->set_layout('tabbed-horizontal')
                
            ])->where('post_type', '=', 'commission_request');

          
    }
    

    public function register_dispute_fields()
    {
        Container::make('post_meta', __('Dispute Conditions'))

            ->add_fields([
         

                Field::make('select', 'commission_request_id', __('payment'))
                ->add_options([$this->admin,'get_commission_requests']),

                Field::make('text', 'subject', __('Subject:')),
                Field::Make("media_gallery", "documents", "Documents"),


                Field::make('select', 'initiating_user', __('Initiating User:'))
                    ->add_options([$this->admin,'get_users']),
              

                Field::make('select', 'user_winnerr_dispute', __('Winner Dispute:'))
                    ->add_options([$this->admin,'get_users']),
            
                    Field::make('date_time', 'date', __('Date')),
                Field::make('rich_text', 'admin_decission_comments', __('Admin Comments:')),
                

                Field::make('select', 'status', __('Status'))
                ->set_options([$this->admin,"get_statuses_dispute"]),

                Field::make('complex', 'status_history', 'Dispute Status History')
                ->add_fields([
                    Field::make('select', 'history_status', __('Status'))
                    ->set_options([$this->status_manager,"get_status_dispute"]),
                    Field::make('date_time', 'date_status', 'Date'),
                    Field::make('select', 'changed_by', __('Changed by:'))->add_options([$this->admin,'get_users']),

                   
                ])
                ->set_layout('tabbed-horizontal')

            ])->where('post_type', '=', 'dispute');

          
    }

    public function register_payment_fields()
    {
        Container::make('post_meta', __('payment Conditions'))

            ->add_fields([

                Field::make('select', 'commission_request_id', __('Commission Request'))
                ->add_options([$this->admin,'get_commission_requests']),
                Field::make('text', 'total_paid', 'Total Paid'),
                Field::make('text', 'total_cart', 'Total Cart'),

                Field::make('text', 'total_agent', 'Total Agent'),

                Field::make('text', 'total_platform', 'Total Platform'),

                Field::make('text', 'total_tax_service', 'Total Tax Serves'),
                
                Field::make('select', 'source', 'Source')->add_options([
                    "stripe" => "Stripe"
                ]),
                Field::make('select', 'user', __('User:'))
                ->add_options([$this->admin,'get_users']),
                Field::make('media_gallery', 'invoice', 'Invoice'),


                Field::make('text', 'payment_stripe_id', 'Stripe Payment Id'),
                

            
                Field::make('date_time', 'date', 'payment Date'),

                Field::make('select', 'status', __('Status'))
                ->set_options([$this->status_manager,"get_status_deposit"]),

                
    

            



            ])->where('post_type', '=', 'payment');

          
    }
    public function register_theme_options()
    {
        Container::make('theme_options', __('Nexfy Options'))
        ->add_fields([
            Field::make('textarea', 'stripe_secret_key', __('Stripe Secret Key')),
            Field::make('textarea', 'stripe_publishable_key', __('Stripe Publishable key')),
            Field::make('text', 'billing_address_street', __('Billing Address Street')),
            Field::make('text', 'billing_address_number', __('Billing Address Number')),
            Field::make('text', 'billing_address_city', __('Billing Address City')),
            Field::make('text', 'billing_address_state', __('Billing Address State')),
            Field::make('text', 'billing_address_location', __('Billing Address Location')),
            Field::make('text', 'billing_address_postalcode', __('Billing Address Postal Code')),
            Field::make('text', 'billing_company_holder', __('Billing Company Holder')),
            Field::make('text', 'billing_company_name', __('Billing Company Name')),
        ]);
    }



    public function register_deposit_fields()
    {
        Container::make('post_meta', __('Deposit Conditions'))

            ->add_fields([

           
                Field::make('text', 'total_withdraw_funds', 'Total Withdraw'),
                
                Field::make('select', 'user', __('User:'))
                ->add_options([$this->admin,'get_users'])->set_required(true),
                
                Field::make('media_gallery', 'invoice', 'Invoice'),


                Field::make('text', 'payment_stripe_id', 'Stripe Payment Id'),
                

            
                Field::make('date_time', 'date', 'payment Date'),

                
                Field::make('select', 'status', __('Status'))
                ->set_options([$this->status_manager,"get_status_payment"]),

            



            ])->where('post_type', '=', 'deposit');

          
    }

    
    public function register_review_fields()
    {
        Container::make('post_meta', __('Review'))

            ->add_fields([
                Field::make('select', 'commercial_agent', __('Commercial Agent'))
                ->add_options([$this->admin,'get_commercial_agents']),
                Field::make('select', 'company', __('Company'))
                ->add_options([$this->admin,'get_companies']),
                Field::make('rich_text', 'content', __('Content')),
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

    public function register_taxonomy_industry_fields(){
        
    
        // Registra los campos personalizados solo si estamos en la página de inicio
        Container::make('term_meta', __('Review'))
          
            ->where('term_taxonomy', '=', 'industry') // Solo para el tipo de post 'page'
            ->add_fields([
                Field::make('file', 'cover_image', 'Cover Image'),
            ]); 
     

    }
    public function register_home_fields(){
        $front_page_id = get_option('page_on_front');
    
        // Registra los campos personalizados solo si estamos en la página de inicio
        Container::make('post_meta', __('Review'))
          
            ->where('post_type', '=', 'page') // Solo para el tipo de post 'page'
            ->where('post_id', '=', $front_page_id)
            ->add_tab( __( 'Hero Section' ), array(
                Field::make( 'text', 'hero_title', __( 'Title' ) ),
                Field::make( 'rich_text', 'hero_description', __( 'Description' ) ),
                Field::make( 'file', 'hero_image', __( 'Image' ) )
            ) )->add_tab( __( 'Features' ), array(
                Field::make( 'complex', 'features', __( 'Features' ) )
                ->add_fields( array(
                    Field::make( 'text', 'feature_title', __( 'Title' ) ),
                    Field::make( 'rich_text', 'feature_description', __( 'Description' ) ),
                    Field::make( 'file', 'feature_image', __( 'Image' ) )
                ) ) ->set_layout('tabbed-horizontal')
                
            ) )->add_tab( __( 'Selected Opportunities' ), array(
                Field::make( 'text', 'opportunities_title', __( 'Title' ) ),
                Field::make( 'rich_text', 'opportunities_description', __( 'Description' ) ),
                Field::make( 'association', 'opportunities_select', __( 'Select Opportunities' ) )
                ->set_types( array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'opportunity',
                    )
                ) )
            ) )
            ->add_tab( __( 'Selected Taxonomy' ), array(
                Field::make( 'text', 'industry_title', __( 'Title' ) ),
                Field::make( 'rich_text', 'industry_description', __( 'Description' ) ),
                Field::make( 'association', 'industry_select', __( 'Select Opportunities' ) )
                ->set_types( array(
                    array(
                        'type'      => 'term',
                        'taxonomy' => 'industry',
                    )
                ) )
            ) )->add_tab( __( 'Selected agents' ), array(
                Field::make( 'text', 'selected_agents_title', __( 'Title' ) ),
                Field::make( 'rich_text', 'selected_agents_description', __( 'Description' ) ),
                Field::make( 'association', 'selected_agents', __( 'Select Agents' ) )
                ->set_types( array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'commercial_agent',
                    )
                ) )
            ) )
            ->add_tab( __( 'Counters' ), array(
                Field::make( 'complex', 'counters', __( 'Counters' ) )
                ->add_fields( array(
                    Field::make( 'text', 'title', __( 'Title' ) ),
                    Field::make( 'text', 'counter', __( 'Counter' ) ),
                    Field::make( 'text', 'counter_unit', __( 'Counter Unit' ) ),
                    Field::make( 'image', 'icon', __( 'Icon' ) ),
                ) ) ->set_layout('tabbed-horizontal')
            ) )
            ->add_tab( __( 'Blog' ), array(
                Field::make( 'text', 'blog_title', __( 'Title' ) ),
                Field::make( 'rich_text', 'blog_description', __( 'Description' ) ),
                Field::make( 'text', 'blog_quantity', __( 'Quantity' ) ),
            ) )
            ->add_tab( __( 'Hero Section with button' ), array(
                Field::make( 'text', 'hero_button__title', __( 'Title' ) ),
                Field::make( 'rich_text', 'hero_button_description', __( 'Description' ) ),
                Field::make( 'file', 'hero_button_image', __( 'Image' ) ),
                Field::make( 'text', 'hero_button_button_text', __( 'Button Text' ) ),
                Field::make( 'text', 'hero_button_button_link', __( 'Button Link' ) )
            ) )
            ->add_tab( __( 'Brands' ), array(
                Field::make( 'complex', 'brands', __( 'Brands' ) )
                ->add_fields( array(
                    Field::make( 'text', 'brand_title', __( 'Title' ) ),
                    Field::make( 'image', 'brand_image', __( 'Image' ) )
                ) )->set_layout('tabbed-horizontal')
            ) ); 
     

    }
}
