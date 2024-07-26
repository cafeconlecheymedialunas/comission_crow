<?php
$current_user = wp_get_current_user();



if(in_array("commercial_agent", $current_user->roles)) {
    $commercial_agent = CommercialAgent::get_instance();
    $post = $commercial_agent->get_commercial_agent();
    $key = "commercial_agent";
} else {
    $company = Company::get_instance();
    $post = $company->get_company();
    $key = "company";
}






$query = new WP_Query([
  'post_type' => 'contract',
  'meta_query' => [
      [
          'key' => $key,
          'value' => $post->ID,
          'compare' => '='
      ]
  ],
  'posts_per_page' => -1,
]);

$contracts = $query->posts;




?>
  <div class="modal fade" id="modal-dispute" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chatModalLabel">Commission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="commission-form">
          <div class="row">
            
            <?php if($commission_requests):?>
              <div class="col-md-6" id="contract-select" style="display:none">
                  <label for="contract" class="form-label">Contracts:</label>
                  <select  name="contract_id" class="form-select">
                      <option value="">Select an option</option>
                      <?php foreach ($commission_request as $request): ?>
                          <option value="<?php echo esc_attr($request->ID); ?>">
                              <?php echo esc_html($request->post_title); ?>
                          </option>
                      <?php endforeach;?>
                  </select>
              </div>
            <?php endif;?>
            
            <div class="col-md-6">
                <label for="text-ids" class="form-label">General Invoice:</label>
                <input type="hidden" id="text-ids" value="" name="general_invoice" class="regular-text media-ids">
                <button type="button" id="select-text-button" class="button select-media-button btn btn-secondary" data-media-type="text" data-multiple="true">Select Text File</button>
                <div class="text-preview row" style="display:none;">
              
                </div>
            </div>
            
            <div class="col-12">
              <div class="request-detail py-3 px-2 border">

              <div class="d-flex justify-content-end mb-2">
                <button type="button" class="btn btn-info btn-sm add-new-item">Add New Item</button>
              </div>
              
              <ul class="commission-request-items list-group mb-2">
                <li class="item list-group-item position-relative">
                <div class="row">
                  <div class="col-6 col-md-2">
                    <label for="price" class="form-label">Price:</label>
                    <input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price">
                    <small class="text-danger error-message">Please fill out the price field.</small>
                  </div>
                  <div class="col-6 col-md-2">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" name="quantity[]" class="form-control" placeholder="Quantity">
                    <small class="text-danger error-message">Please fill out the quantity field.</small>
                  </div>
                  <div class="col-md-2">
                      <label for="text-ids" class="form-label">Invoice:</label>
                      <input type="hidden" id="text-ids" value="" name="invoice" class="regular-text media-ids">
                      <button type="button" id="select-text-button" class="button d-block select-media-button btn btn-secondary" data-media-type="text" data-multiple="true">
                        <i class="fa-solid fa-file-invoice"></i>
                      </button>
                      <div class="text-preview row" style="display:none;"></div>
                  </div>
                  <div class="col-6 col-md-3">
                      <label for="detail" class="form-label">Detail:</label>
                      <textarea id="detail" name="detail[]"></textarea>
                  </div>
                  <div class="col-6 col-md-2">
                    <label for="subtotal" class="form-label">Subtotal:</label>
                    <span class="subtotal">0.00</span>
                  </div>
                 
                  
                  
                  
                </div>
                <span class="position-absolute remove-item"><i class="fa fa-trash" aria-hidden="true"></i></span>
                </li>
                
                
              </ul>
              <div class="detail-error text-danger"></div>
              
             
                <div class="total d-flex justify-content-end align-items-center">
                  <label for="total" class="">Total:</label>
                  <span id="total" class="total">0.00</span>
                </div>
              
            </div>
            </div>
            <div class="col-md-12">
                <label for="comments" class="form-label">Comments:</label>
                <div class="editor-container" data-target="comments"></div>
                <input type="hidden" id="comments" name="comments">
            </div>
          </div>
          <input type="hidden" id="hidden_contract_id" name="contract_id">
          <input type="hidden" name="security" value="<?php echo wp_create_nonce("create_commission_request_nonce"); ?>"/>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="commission-form" class="btn btn-primary">Save changes</button>
      </div>
      </div>
      </div>
      </div>
      




