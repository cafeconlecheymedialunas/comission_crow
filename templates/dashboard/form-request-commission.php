<?php
$current_user = wp_get_current_user();



if(in_array("commercial_agent", $current_user->roles)) {
    $post_type = CommercialAgent::get_instance();
    $post = $post_type->get_commercial_agent();
    $key = "commercial_agent";
} else {
    $post_type = Company::get_instance();
    $post = $post_type->get_company();
    $key = "company";
}



$contracts = $post_type->get_contracts()





?>
  <div class="modal fade" id="modal-commission" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chatModalLabel">Commission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="commission-form">
          <div class="row">
            
            <?php if($contracts):?>
              <div class="col-md-6" id="contract-select" style="display:none">
                  <label for="contract" class="form-label">Contracts:</label>
                  <select  name="contract_id" class="form-select">
                      <option value="">Select an option</option>
                      <?php foreach ($contracts as $contract): ?>
                          <option value="<?php echo esc_attr($contract->ID); ?>">
                              <?php echo esc_html($contract->post_title); ?>
                          </option>
                      <?php endforeach;?>
                  </select>
              </div>
            <?php endif;?>
            
            <div class="col-md-6">
                <label for="text-ids" class="form-label">General Invoice:</label>
                <input type="file" id="text-ids" value="" name="general_invoice" class="form-control">
               
            </div>
            
            <div class="col-12">
              <div class="request-detail py-3 px-2 border">

              <div class="d-flex justify-content-end mb-2">
                <button type="button" class="btn btn-link btn-sm add-new-item">Add New Item</button>
              </div>
              
              <ul class="commission-request-items mb-2">
                <li class="item position-relative">
                <div class="row border">
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
                  <div class="col-md-3">
                      <label for="text-ids" class="form-label">Invoice:</label>
                      <input type="file" id="invoice" name="invoice" class="form-control">
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
      




