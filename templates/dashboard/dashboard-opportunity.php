<?php
// Obtener todos los términos de la taxonomía 'language'
$languages_terms = get_terms([
    'taxonomy' => 'language',
    'hide_empty' => false,
]);
$sectors_terms = get_terms([
    'taxonomy' => 'sector',
    'hide_empty' => false,
]);
$language_terms = get_terms([
    'taxonomy' => 'language',
    'hide_empty' => false,
]);
$country_terms = get_terms([
    'taxonomy' => 'country',
    'hide_empty' => false,
]);
$currency_terms = get_terms([
    'taxonomy' => 'currency',
    'hide_empty' => false,
]);
/*
Field::make('radio', 'target_audience', __('Target Audience'))->set_options(array(
'companies' => "Companies",
'individuals' => "Individuals",
)),

Field::make('multiselect', 'languages', __('Languages'))
->set_options(array($this,"get_languages")),
Field::make('select', 'location', __('Location'))
->set_options(array($this,"get_countries")),
Field::make('select', 'age', __('Age'))
->set_options(array(
'over_18' => 'Over 18',
'over_30' => 'Over 30',
'over_60' => 'Over 60',
'any_age' => 'Any age',
)),
Field::make('select', 'gender', __('Gender'))
->set_options(array(
'male' => 'Male',
'female' => 'Female',
'any_gender' => 'Any gender',
)),
Field::make('select', 'currency', __('Currency'))
->set_options(array($this,"get_currencies"))

Field::make('text', 'price', __('Price')),
Field::make('text', 'commission', __('Commission')),
Field::make('checkbox', 'deliver_leads', 'Deliver Leads?')
->set_option_value('yes'),
Field::make('text', 'sales_cycle_estimation', __('Sales cycle estimation')),

Field::make('media_gallery', 'images', __('Images'))->set_type('image'),
Field::make('media_gallery', 'supporting_materials', __('Supporting materials'))->set_type('text'),
Field::make('complex', 'videos', __('Videos urls'))
->add_fields(array(
Field::make('oembed', 'video', __('Url Video')),
)),
Field::make('textarea', 'tips', __('Tips')),
  Field::make('textarea', 'question_1', __('1) What is your company’s elevator pitch?')),
            Field::make('textarea', 'question_2', __('2) Please complete the below value statement: Example: "We help (XXX) in the (XXX) industry (XXX) WITHOUT (XXX) & WITHOUT (XXX).')),
            Field::make('textarea', 'question_3', __('3) How do you currently pitch your business to a prospect?')),
            Field::make('textarea', 'question_4', __('4) What are the most common objections you face within your current sales cycle?')),
            Field::make('textarea', 'question_5', __('5) What strategies do you employ to overcome the objections specified?')),
            Field::make('textarea', 'question_6', __('6) Please give an overview of what business challenges you help your clients overcome?')),
 */
?>
<form id="opportunity-form" class="row g-3">
    <div class="col-md-6">
        <label for="title" class="form-label">Title:</label>
        <input type="text" id="title" name="title" class="form-control" required>
    </div>
    <?php if ($sectors_terms): ?>
    <div class="col-md-6">

            <label for="sectors" class="form-label">Sector:</label>
            <select name="sectors[]" multiple class="form-select custom-select">
                <?php foreach ($sectors_terms as $term): ?>
                    <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                <?php endforeach;?>
            </select>


    </div>
    <?php endif;?>
    <div class="col-md-6">

            <label for="target_audience" class="form-label">Target Audience:</label>
            <select name="target_audience" class="form-select">

                    <option value="companies">Companies</option>
                    <option value="individuals">Individuals</option>
            </select>

    </div>
    <?php if ($language_terms): ?>
    <div class="col-md-6">

            <label for="languages" class="form-label">Languages:</label>
            <select name="languages[]" multiple class="form-select custom-select">
                <?php foreach ($language_terms as $term): ?>
                    <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                <?php endforeach;?>
            </select>



    </div>
    <?php endif;?>
    <?php if ($country_terms): ?>
    <div class="col-md-6">

            <label for="location[]" class="form-label">Locations:</label>
            <select name="location[]" multiple class="form-select custom-select">
                <?php foreach ($country_terms as $term): ?>
                    <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                <?php endforeach;?>
            </select>



    </div>
    <?php endif;?>
    <div class="col-md-6">

       <label for="target_audience" class="form-label">Age Range:</label>
       <select name="target_audience" class="form-select">
            <option value="">Select a choice</option>
            <option value="over_18">Over 18</option>
            <option value="over_30">Over 30</option>
            <option value="over_60">Over 60</option>
            <option value="any_age">Any Age</option>
       </select>

</div>
<div class="col-md-6">

       <label for="gender" class="form-label">Gender:</label>
       <select name="gender" class="form-select">
            <option value="">Select a choice</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="any_gender">Any Gender</option>
       </select>

</div>
<?php if ($currency_terms): ?>
    <div class="col-md-6">

            <label for="currency" class="form-label">Currency:</label>
            <select name="currency" class="form-select custom-select">
                <?php foreach ($currency_terms as $term): ?>
                    <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                <?php endforeach;?>
            </select>



    </div>
    <?php endif;?>
    <div class="col-md-6">
        <label for="price" class="form-label">Price:</label>
        <input type="text" id="price" name="price" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label for="comission" class="form-label">Comission:</label>
        <input type="text" id="comission" name="comission" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="switch">
            <input type="checkbox" name="deliver_leads" value="yes">
            <span class="slider"></span>
        </label>
        <label for="deliver_leads">Deliver Leads?</label>
    </div>
    <div class="col-md-6">
        <label for="sales_cycle_estimation" class="form-label">Sales Cycle Estimation:</label>
        <input type="text" id="sales_cycle_estimation" name="sales_cycle_estimation" class="form-control" required>
    </div>
   
    <div class="col-md-6">
                <div class="media-gallery-field">
                    <input type="hidden" name="image_ids" id="image_ids" value="">
                    <img class="image-preview img-fluid" style="display:none;" src="" alt="Preview Image">
                    <div>
                        <button class="upload-image-button button">Upload/Add Image</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="media-gallery-field">
                <label for="supporting_materials" class="form-label">Supporting materials:</label>
                    <input type="file" class="form-control" name="supporting_materials" id="supporting_materials" value="" multiple accept=".txt,.csv,.doc,.docx,.pdf,.rtf,.odt,.tex,.wps,.wpd">
                  
                </div>
            </div>
            <div class="col-md-6">
            <div id="video-urls-container">
                <div class="video-url-field">
                    <input type="text" name="video_urls[]" class="form-control" placeholder="Enter video URL">
                </div>
            </div>
            <button type="button" id="add-video-url-button" class="button">Add Another Video URL</button>
            </div>
            <div class="col-md-12">
                <label for="tips" class="form-label">Tips:</label>
                <textarea  id="tips" name="tips" class="form-control"></textarea>
            </div>
            <div class="col-md-12">
                <label for="question_1" class="form-label">What is your company’s elevator pitch?:</label>
                <textarea  id="question_1" name="question_1" class="form-control"></textarea>
            </div>
            <div class="col-md-12">
                <label for="question_2" class="form-label">Please complete the below value statement: Example: "We help (XXX) in the (XXX) industry (XXX) WITHOUT (XXX) & WITHOUT (XXX).</label>
                <textarea  id="question_2" name="question_2" class="form-control"></textarea>
            </div>
            <div class="col-md-12">
                <label for="question_3" class="form-label"> How do you currently pitch your business to a prospect?</label>
                <textarea  id="question_3" name="question_3" class="form-control"></textarea>
            </div>
            <div class="col-md-12">
                <label for="question_4" class="form-label">What are the most common objections you face within your current sales cycle?</label>
                <textarea  id="question_4" name="question_4" class="form-control"></textarea>
            </div>
            <div class="col-md-12">
                <label for="question_5" class="form-label">What strategies do you employ to overcome the objections specified?</label>
                <textarea  id="question_5" name="question_5" class="form-control"></textarea>
            </div>
            <div class="col-md-12">
                <label for="question_6" class="form-label"> Please give an overview of what business challenges you help your clients overcome?</label>
                <textarea  id="question_6" name="question_6" class="form-control"></textarea>
            </div>
           
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

