<?php
$language_terms = get_terms([
    "taxonomy" => "language",
    "hide_empty" => false,
]);
$industry_terms = get_terms([
    "taxonomy" => "industry",
    "hide_empty" => false,
]);
;
$country_terms = get_terms([
    "taxonomy" => "country",
    "hide_empty" => false,
]);
$currency_terms = get_terms([
    "taxonomy" => "currency",
    "hide_empty" => false,
]);

$type_of_company_terms = get_terms([
    "taxonomy" => "type_of_company",
    "hide_empty" => false,
]);

$age_options = $admin->get_ages();
$target_audience_options = $admin->get_target_audiences();
$gender_options = $admin->get_genders();



$company_post = get_user_associated_post_type();






$target_audience = isset($opportunity_post) ? carbon_get_post_meta($opportunity_post->ID, 'target_audience') : '';
$age = isset($opportunity_post) ? carbon_get_post_meta($opportunity_post->ID, 'age') : '';
$gender = isset($opportunity_post) ? carbon_get_post_meta($opportunity_post->ID, 'gender') : '';
$images = isset($opportunity_post) ? carbon_get_post_meta($opportunity_post->ID, 'images') : [];
$supporting_materials = isset($opportunity_post) ? carbon_get_post_meta($opportunity_post->ID, 'supporting_materials') : [];
$videos = isset($opportunity_post) ? carbon_get_post_meta($opportunity_post->ID, 'videos') : '';
$tips = isset($opportunity_post) ? carbon_get_post_meta($opportunity_post->ID, 'tips') : '';

$industry = isset($opportunity_post) ?  wp_get_post_terms($opportunity_post->ID, 'industry', ['fields' => 'ids']): [];
$language = isset($opportunity_post) ? wp_get_post_terms($opportunity_post->ID, 'language', ['fields' => 'ids']):[];
$country =isset($opportunity_post) ?  wp_get_post_terms($opportunity_post->ID, 'country', ['fields' => 'ids']):[];
$type_of_company = isset($opportunity_post) ? wp_get_post_terms($opportunity_post->ID, 'type_of_company', ['fields' => 'ids']):[];
$currency = isset($opportunity_post) ? wp_get_post_terms($opportunity_post->ID, 'currency', ['fields' => 'ids']):[];

?>
<form id="opportunity-form" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
        <label for="title" class="form-label">Title:</label>
        <input type="text" id="title" name="title" class="form-control" value="<?php echo isset($opportunity_post) ? esc_attr($opportunity_post->post_title):"";?>">
       
    </div>


    <?php if($industry_terms):?>
    <div class="col-md-6">
        <label for="industry" class="form-label">Industry:</label>
        <select name="industry[]" class="form-select">
            <option value="">Select an option</option>
            <?php foreach ($industry_terms as $term): ?>
                <option 
                value="<?php echo esc_attr($term->term_id); ?>" 
                <?php echo in_array($term->term_id, $industry) ? 'selected' : ''; ?>
                >
                    <?php echo esc_html($term->name); ?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <?php endif;?>
    <?php if($language_terms):?>
    <div class="col-md-6">
        <label for="language" class="form-label">Languages:</label>
        <select name="language[]" class="form-select" multiple aria-label="multiple select example">
            <?php foreach ($language_terms as $term): ?>
                <option 
                    value="<?php echo esc_attr($term->term_id); ?>" 
                    <?php echo in_array($term->term_id, $language) ? 'selected' : ''; ?>
                >
                    <?php echo esc_html($term->name); ?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <?php endif;?>
    <?php if($country_terms):?>
    <div class="col-md-6">
        <label for="country" class="form-label">Country:</label>
        <select name="country[]" class="form-select">
            <option value="">Select an option</option>
            <?php foreach ($country_terms as $term): ?>
                <option 
                    value="<?php echo esc_attr($term->term_id); ?>" 
                    <?php echo in_array($term->term_id, $country) ? 'selected' : ''; ?>
                >
                    <?php echo esc_html($term->name); ?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <?php endif;?>
    <?php if($currency_terms):?>
    <div class="col-md-6">
        <label for="currency" class="form-label">Currency:</label>
        <select name="currency[]" class="form-select">
            <option value="">Select an option</option>
            <?php foreach ($currency_terms as $term): ?>
                <option 
                    value="<?php echo esc_attr($term->term_id); ?>" 
                    <?php echo in_array($term->term_id, $currency) ? 'selected' : ''; ?>
                >
                    <?php echo esc_html($term->name); ?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <?php endif;?>
    <div class="col-md-6">
        <label for="target_audience" class="form-label">Target Audience:</label>
        <select name="target_audience" class="form-select">
            <option value="">Select an option</option>
            <?php foreach ($target_audience_options as $key => $value): ?>
                <option 
                    value="<?php echo esc_attr($key); ?>" 
                    <?php echo selected($target_audience, $key); ?>
                >
                
                    <?php echo esc_html($value); ?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <?php if($type_of_company_terms):?>
    <div class="col-md-6">
        <label for="type_of_company" class="form-label">Company Type:</label>
        <select name="type_of_company[]" class="form-select">
            <option value="">Select an option</option>
            <?php foreach ($type_of_company_terms as $term): ?>
                <option value="<?php echo esc_attr($term->term_id); ?>" 
                <?php echo in_array($term->term_id, $type_of_company) ? 'selected' : ''; ?>
            >
            <?php echo esc_html($term->name); ?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <?php endif;?>
    <div class="col-md-12">
            <label for="content" class="form-label">Content:</label>
            <div class="editor-container"></div>
            <input type="hidden" id="content" name="content" value="<?php echo isset($opportunity_post) ? esc_attr($opportunity_post->post_content):"";?>">
    </div>

   
  
   

    <div class="col-md-6">
        <label for="age" class="form-label">Age:</label>
        <select name="age" class="form-select">
            <option value="">Select an option</option>
            <?php foreach ($age_options as $key => $value): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($age, $key); ?>><?php echo esc_html($value); ?></option>
            <?php endforeach;?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="gender" class="form-label">Gender:</label>
        <select name="gender" class="form-select">
            <option value="">Select an option</option>
            <?php foreach ($gender_options as $key => $value): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($gender, $key); ?>><?php echo esc_html($value); ?></option>
            <?php endforeach;?>
        </select>
    </div>


    <div class="col-md-6">
        <label for="price" class="form-label">Price:</label>
        <input type="text"   id="price" name="price" class="form-control" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "price")):"";?>">
    </div>

    <div class="col-md-6">
        <label for="commission" class="form-label">Commission:</label>
        <input type="text" id="commission" name="commission" class="form-control" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "commission")) : ""; ?>" />
    </div>


    <div class="col-md-6">
        <label class="form-check-label">
            <input type="checkbox" name="deliver_leads" class="form-check-input" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "deliver_leads")):"";?>">
            Deliver Leads?
        </label>
    </div>

    <div class="col-md-6">
        <label for="sales_cycle_estimation" class="form-label">Sales Cycle Estimation:</label>
        <input type="text" id="sales_cycle_estimation" name="sales_cycle_estimation" class="form-control" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "sales_cycle_estimation")):"";?>">
    </div>
    <?php if(isset($_GET["opportunity_id"])) {?>
    <div class="col-md-6">
        <label for="images" class="form-label">Images</label>
        <input class="form-control" type="file" name="images[]" id="images" multiple accept="image/*">

    </div>
    <?php } else {
        $template_path = 'templates/dashboard/dropzone.php';
        if (locate_template($template_path)) {
            include locate_template($template_path);
        }
  
    } ?> 
   

    <div class="col-md-6">
        <label for="supporting_materials" class="form-label">Supporting Materials</label>
        <input class="form-control" type="file" name="supporting_materials[]" id="supporting_materials" multiple accept=".pdf, .txt">

    </div>
  

    <div class="col-md-12">
                <div class="d-flex justify-content-between mb-5">
                    <h4>Videos</h4>
                    <a href="#" class="add-new-url prolancer-btn" data-nonce="6ef301fdf4"><i class="fal fa-plus"></i> Add New Video</a>
                </div>
                <div class="url-videos">
                    <?php if($videos):?>
                    <?php foreach($videos as $video):?>
                    <div class="row mb-3">
                        <div class="col-sm-8 my-auto">
                            <input type="url" name="videos[]" value="<?php echo $video["video"];?>" class="form-control" placeholder="Video URL">
                            <small class="text-danger error-message" style="display: none;">Please fill out the URL field before adding another.</small>
                        </div>
                        <div class="col-sm-2">
                            <i class="fas fa-trash remove-url"></i>
                        </div>
                    </div>
                    <?php endforeach;?>
                    <?php endif;?>
                </div>
                
            </div>
    
            <div class="col-md-12">
                <label for="tips" class="form-label">Tips</label>
                <div class="editor-container" data-target="tips"></div>
                <input type="hidden" id="tips" name="tips" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "tips")) : ""; ?>">
            </div>

<div class="col-md-12">
    <label for="question_1" class="form-label">What is your company’s elevator pitch?</label>
    <div class="editor-container" data-target="question_1"></div>
    <input type="hidden" id="question_1" name="question_1" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "question_1")) : ""; ?>">
</div>

<div class="col-md-12">
    <label for="question_2" class="form-label">Please complete the below value statement:</label>
    <div class="editor-container" data-target="question_2"></div>
    <input type="hidden" id="question_2" name="question_2" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "question_2")) : ""; ?>">
</div>

<div class="col-md-12">
    <label for="question_3" class="form-label">How do you currently pitch your business to a prospect?</label>
    <div class="editor-container" data-target="question_3"></div>
    <input type="hidden" id="question_3" name="question_3" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "question_3")) : ""; ?>">
</div>

<div class="col-md-12">
    <label for="question_4" class="form-label">What are the most common objections you face within your current sales cycle?</label>
    <div class="editor-container" data-target="question_4"></div>
    <input type="hidden" id="question_4" name="question_4" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "question_4")) : ""; ?>">
</div>

<div class="col-md-12">
    <label for="question_5" class="form-label">What strategies do you employ to overcome the objections specified?</label>
    <div class="editor-container" data-target="question_5"></div>
    <input type="hidden" id="question_5" name="question_5" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "question_5")) : ""; ?>">
</div>

<div class="col-md-12">
    <label for="question_6" class="form-label">Please give an overview of what business challenges you help your clients overcome?</label>
    <div class="editor-container" data-target="question_6"></div>
    <input type="hidden" id="question_6" name="question_6" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "question_6")) : ""; ?>">
</div>

    <input type="hidden" name="security" value="<?php echo wp_create_nonce(
        "create-opportunity-nonce"
    ); ?>"/>

     
    <input type="hidden" name="company_id" value="<?php echo $company_post->ID;?>">
  
    <?php if(isset($_GET["opportunity_id"]) && !empty($_GET["opportunity_id"])):?>
     <input type="hidden" name="opportunity_id" value="<?php echo  $_GET["opportunity_id"];?>"/>
     <?php endif;?>
     <div class="alert alert-danger general-errors" role="alert" style="display:none;">
     </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>

</form>

