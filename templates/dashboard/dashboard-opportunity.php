<?php
$languages_terms = get_terms([
    "taxonomy" => "language",
    "hide_empty" => false,
]);
$sectors_terms = get_terms([
    "taxonomy" => "sector",
    "hide_empty" => false,
]);
$language_terms = get_terms([
    "taxonomy" => "language",
    "hide_empty" => false,
]);
$country_terms = get_terms([
    "taxonomy" => "country",
    "hide_empty" => false,
]);
$currency_terms = get_terms([
    "taxonomy" => "currency",
    "hide_empty" => false,
]);

?>

<div class="row">
						                            <div class="col-md-12">
														<div class="card">
<form id="opportunity-form" class="row g-3" method="post">
    <div class="col-md-6">
        <label for="title" class="form-label">Title:</label>
        <input type="text" id="title" name="title" class="form-control">
        <span id="title-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="content" class="form-label">Content:</label>
        <textarea id="content" name="content" class="form-control" rows="4"></textarea>
        <span id="content-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="sector" class="form-label">Sector:</label>
        <select name="sector" class="form-select custom-select">
            <option value="">Select an option</option>
            <?php foreach ($sectors_terms as $term): ?>
                <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
        </select>
        <span id="sector-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="target_audience" class="form-label">Target Audience:</label>
        <select name="target_audience" class="form-select">
            <option value="">Select an option</option>
            <option value="companies">Companies</option>
            <option value="individuals">Individuals</option>
        </select>
        <span id="target_audience-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="company_type" class="form-label">Company Type:</label>
        <select name="company_type" class="form-select custom-select">
            <option value="">Select an option</option>
            <?php foreach ($company_types_terms as $term): ?>
                <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
        </select>
        <span id="company_type-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="languages" class="form-label">Languages:</label>
        <select name="languages[]" multiple class="form-select custom-select-multiple">
            <?php foreach ($languages_terms as $term): ?>
                <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
        </select>
        <span id="languages-error" class="invalid-feeedback d-block"></span>
    </div>


    <div class="col-md-6">
        <label for="location" class="form-label">Location:</label>
        <select name="location" class="form-select custom-select">
            <option value="">Select an option</option>
            <?php foreach ($country_terms as $term): ?>
                <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
        </select>
        <span id="location-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="age" class="form-label">Age:</label>
        <select name="age" class="form-select">
            <option value="">Select an option</option>
            <option value="over_18">Over 18</option>
            <option value="over_30">Over 30</option>
            <option value="over_60">Over 60</option>
            <option value="any_age">Any Age</option>
        </select>
        <span id="age-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="gender" class="form-label">Gender:</label>
        <select name="gender" class="form-select">
            <option value="">Select an option</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="any_gender">Any Gender</option>
        </select>
        <span id="gender-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="currency" class="form-label">Currency:</label>
        <select name="currency" class="form-select custom-select">
            <option value="">Select an option</option>
            <?php foreach ($currency_terms as $term): ?>
                <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
        </select>
        <span id="currency-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="price" class="form-label">Price:</label>
        <input type="text" id="price" name="price" class="form-control">
        <span id="price-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="commission" class="form-label">Commission:</label>
        <input type="text" id="commission" name="commission" class="form-control">
        <span id="comission-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label class="form-check-label">
            <input type="checkbox" name="deliver_leads" class="form-check-input" value="yes">
            Deliver Leads?
        </label>
        <span id="deliver_leads-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="sales_cycle_estimation" class="form-label">Sales Cycle Estimation:</label>
        <input type="text" id="sales_cycle_estimation" name="sales_cycle_estimation" class="form-control">
        <span id="sales_cycle_estimation-error" class="invalid-feeedback d-block"></span>
    </div>
    <div class="col-md-6">
        <label for="image-ids" class="form-label">Images:</label>
        <input type="text" id="image-ids" name="images" class="regular-text media-ids" readonly>
        <button type="button" id="select-image-button" class="button select-media-button" data-media-type="image" data-multiple="true">Select Images</button>
        <div class="preview" style="display: none;">
         
        </div>
        <span id="images-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-6">
        <label for="text-ids" class="form-label">Text File:</label>
        <input type="text" id="text-ids" name="supporting_materials" class="regular-text media-ids" readonly>
        <button type="button" id="select-text-button" class="button select-media-button" data-media-type="text" data-multiple="true">Select Text File</button>
        <div class="preview" style="display: none;">
           
        </div>
        <span id="supporting_materials-error" class="invalid-feeedback d-block"></span>
    </div>
    
    <div class="col-md-12">
                <div class="d-flex justify-content-between mb-5">
                    <h4>Videos</h4>
                    <a href="#" class="add-new-url prolancer-btn" data-nonce="6ef301fdf4"><i class="fal fa-plus"></i> Add New URL</a>
                </div>
                <div class="url-videos">
                    <div class="row mb-3">
                        <div class="col-sm-8 my-auto">
                            <input type="url" name="videos[]" class="form-control" placeholder="Video URL">
                            <small class="text-danger error-message" style="display: none;">Please fill out the URL field before adding another.</small>
                        </div>
                        <div class="col-sm-2">
                            <i class="fas fa-trash remove-url"></i>
                        </div>
                    </div>
                </div>
                <span id="videos-error" class="invalid-feeedback d-block"></span>
            </div>
    <div class="col-md-12">
        <label for="tips" class="form-label">Tips:</label>
        <textarea id="tips" name="tips" class="form-control"></textarea>
        <span id="tips-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-12">
        <label for="question_1" class="form-label">What is your company’s elevator pitch?:</label>
        <textarea id="question_1" name="question_1" class="form-control"></textarea>
        <span id="question_1-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-12">
        <label for="question_2" class="form-label">Please complete the below value statement:</label>
        <textarea id="question_2" name="question_2" class="form-control"></textarea>
        <span id="question_2-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-12">
        <label for="question_3" class="form-label">How do you currently pitch your business to a prospect?</label>
        <textarea id="question_3" name="question_3" class="form-control"></textarea>
        <span id="question_3-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-12">
        <label for="question_4" class="form-label">What are the most common objections you face within your current sales cycle?</label>
        <textarea id="question_4" name="question_4" class="form-control"></textarea>
        <span id="question_4-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-12">
        <label for="question_5" class="form-label">What strategies do you employ to overcome the objections specified?</label>
        <textarea id="question_5" name="question_5" class="form-control"></textarea>
        <span id="question_5-error" class="invalid-feeedback d-block"></span>
    </div>

    <div class="col-md-12">
        <label for="question_6" class="form-label">Please give an overview of what business challenges you help your clients overcome?</label>
        <textarea id="question_6" name="question_6" class="form-control"></textarea>
        <span id="question_6-error" class="invalid-feeedback d-block"></span>
    </div>
    <div class="errors"></div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
</div>
</div>
</div>


