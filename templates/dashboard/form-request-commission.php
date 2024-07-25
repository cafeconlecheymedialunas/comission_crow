<?php
$current_user = wp_get_current_user();
$user_info = get_userdata($current_user->ID);



?>
  <div class="modal fade" id="modal-commission" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatModalLabel">Commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

  <h2>Add Commission Request Items</h2>
  <form id="commission-form">
    <button type="button" class="btn btn-link add-new-item">Add New Item</button>
    <div class="commission-request-items">
      <!-- Initial Row -->
      <div class="item row mb-3 relative">
        <div class="col-md-3">
          <label for="price" class="form-label">Price:</label>
          <input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price">
          <small class="text-danger error-message">Please fill out the price field.</small>
        </div>
        <div class="col-md-3">
          <label for="quantity" class="form-label">Quantity:</label>
          <input type="number" name="quantity[]" class="form-control" placeholder="Quantity">
          <small class="text-danger error-message">Please fill out the quantity field.</small>
        </div>
        <div class="col-md-3">
          <label for="invoice" class="form-label">Invoice:</label>
          <input type="file" name="invoice[]" class="form-control">
          <small class="text-danger error-message">Please upload the invoice file.</small>
        </div>
        <div class="col-md-3">
          <label for="subtotal" class="form-label">Subtotal:</label>
          <span class="subtotal">0.00</span>
        </div>
        <i class="fa fa-trash-o remove-item"></i>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-2">
        <label for="total" class="form-label">Total:</label>
        <span id="total" class="total">0.00</span>
      </div>
    </div>
    <input type="hidden" id="hidden_agreement_id" name="agreement_id">
    <input type="hidden" name="security" value="<?php echo wp_create_nonce("create_commission_request_nonce"); ?>"/>
    <button type="submit" class="btn btn-success">Submit</button>
  </form>



