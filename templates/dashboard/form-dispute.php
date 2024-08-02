<?php
$current_user = wp_get_current_user();
$commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();

?>
<div class="modal fade" id="modal-dispute" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create a Dispute</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form id="create-dispute" method="post" action="" enctype="multipart/form-data">
   
    <div class="row">
        <div class="col-md-12">
        <label for="commission_request_select form-label">Commission Request:</label>
        <select name="commission_request_select" class="form-select" id="commission_request_select">
            <?php foreach ($admin->get_commission_requests() as $value => $label): ?>
                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
            <?php endforeach;?>
        </select>
        <div class="error-message"></div>
        </div>
        <div class="col-md-12">
        <label for="documents" class="form-label">Documents:</label>
        <input type="file" name="documents" id="documents" class="form-control">
        <div class="error-message"></div>
        </div>
        <div class="col-md-12">
            <label for="subject" class="form-label">Subject:</label>
            <input type="text" name="subject" id="subject" class="form-control">
            <div class="error-message"></div>
        </div>
        <div class="col-md-12">
                <label for="description" class="form-label">Description</label>
                <div class="editor-container" data-target="description"></div>
                <input type="hidden" id="description" name="description">
                <div class="error-message"></div>
            </div>


    </div>
    <input type="hidden" name="security" value="<?php echo wp_create_nonce(
    "create_dispute_nonce"
); ?>"/>
    <input type="hidden" id="commission_request_id" name="commission_request_id">
     <div class="alert alert-danger general-errors mt-4" role="alert" style="display:none;">
     </div>
</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="create-dispute" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

