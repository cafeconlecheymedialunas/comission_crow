<div class="modal fade" id="modal-commission" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chatModalLabel">Add new Commission Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="commission-form">
          <div class="row mb-3">
            <?php if(!empty($contracts)): ?>
              <div class="col-md-6">
                <label for="contract" class="form-label">Contracts:</label>
                <select name="contract_id" class="form-select rounded-2">
                  <option value="">Select an option</option>
                  <?php foreach ($contracts as $contract): ?>
                    <option value="<?php echo esc_attr($contract->ID); ?>">
                      <?php echo esc_html($contract->post_title); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="error-message"></div>
              </div>
            <?php endif; ?>
            <div class="col-md-6">
              <label for="text-ids" class="form-label">General Invoice:</label>
              <input type="file" id="text-ids" name="general_invoice" class="form-control rounded-2" multiple accept=".pdf, .txt">
              <div class="error-message"></div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <div class="border border-1 rounded-2 p-3">
                <div class="">
                  <h5 class="mb-0">Detail sItems</h5>
                </div>
                <div class="py-4">
                  <div class="table-container">
                
                    <table id="data-table" class="table rounded-2 table-bordered mb-0">
                      <thead class="thead-light">
                        <tr>
                          <th>Price</th>
                          <th>Quantity</th>
                          <th>Invoice</th>
                          <th>Detail</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="row_to_clone">
                          <td>
                            <input type="number" name="price[]" min="" class="txt price form-control rounded-2" placeholder="Price" step="0.01" autocomplete='off' />
                          </td>
                          <td>
                            <input type="number" name="quantity[]" class="txt quantity form-control rounded-2" placeholder="Quantity" autocomplete='off' />
                          </td>
                          <td>
                            <input type="file" name="invoice[]" class="invoice form-control rounded-2"  multiple accept=".pdf, .txt"/>
                          </td>
                          <td>
                            <textarea rows="2" name="detail[]" class="detail form-control rounded-2" style="height:100px;" placeholder="Detail"></textarea>
                          </td>
                          <td class="text-center">
                            <a href="#" class="removeRow btn btn-danger btn-sm rounded-2">&times;</a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="error-message"></div>
                </div>
                <div class="text-end pt-2">
                  <a class="addRow btn btn-secondary d-inline rounded-2" href="#">Add Another Row</a>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <div class="py-2 border border-1 rounded-2 p-3">
                <div class="mb-2">
                  <h5>Total Sales</h5>
                </div>
                <div>
                  <p class="mb-0">Total: $<span id="sum">0.00</span></p>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <label for="comments" class="form-label">Comments</label>
              <div class="editor-container rounded-2" data-target="comments"></div>
              <input type="hidden" id="comments" name="comments" value="<?php echo isset($opportunity_post) ? esc_attr(carbon_get_post_meta($opportunity_post->ID, "question_6")) : ""; ?>">
            </div>
          </div>

          <div class="alert alert-danger general-errors rounded-2" role="alert" style="display:none"></div>

          <input type="hidden" name="security" value="<?php echo wp_create_nonce('create_commission_request_nonce'); ?>" />
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary rounded-2" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="commission-form" class="btn btn-primary commission-request-button rounded-2">Save Commission Request</button>
      </div>
    </div>
  </div>
</div>
