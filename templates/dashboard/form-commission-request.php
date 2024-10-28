<div class="modal fade" id="modal-commission" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chatModalLabel">Have you made your sale? Ask for your commission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="commission-form">
          <div class="row mb-4">
            <?php if (!empty($contracts)): ?>
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
              <label for="text-ids" class="form-label">Submit your service invoice:</label>
              <input type="file" id="text-ids" name="general_invoice" class="form-control rounded-2" multiple accept=".pdf, .txt">
              <div class="error-message"></div>
            </div>
          </div>

          <?php
          // Verificamos si $associated_post está definido y si tiene un valor válido
          if (isset($associated_post) && $associated_post) {
            // Obtenemos la moneda asociada al post
            $currency = wp_get_post_terms($associated_post->ID, "currency");

            // Verificamos si $currency no está vacío y es un array
            if (!empty($currency) && is_array($currency) && isset($currency[0])) {
              $currency_id = $currency[0]->term_id;
              $currency_symbol = carbon_get_term_meta($currency_id, "currency_symbol");
              $currency_code = carbon_get_term_meta($currency_id, "currency_code");
            }
          }

          // Valores predeterminados en caso de que no haya moneda asociada
          $currency_symbol = isset($currency_symbol) ? $currency_symbol : "$"; // O algún otro valor predeterminado
          $currency_code = isset($currency_code) ? $currency_code : "USD"; // Predeterminado a USD si no se encuentra
          ?>

          <div class="row mb-4">
            <div class="col-12">
              <div class="border border-1 rounded-2 p-3">
                <div class="">
                  <h5 class="mb-0">Sale details</h5>
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
                            <div class="input-group mb-3">
                              <span class="input-group-text"><?php echo "$currency_symbol ($currency_code)"; ?></span>
                              <input type="number" name="price[]" min="0" class="txt price form-control rounded-2" min="1" placeholder="Price" step="0.01" autocomplete='off' />
                              <div class="error-message"></div>
                            </div>
                          </td>
                          <td>
                            <input type="number" name="quantity[]" min="1" class="txt quantity form-control rounded-2" placeholder="Quantity" autocomplete='off' />
                          </td>
                          <td>
                            <input type="file" name="invoice[]" class="invoice form-control rounded-2" multiple accept=".pdf, .txt" />
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

          <div class="row mb-4">
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
          <div class="row mb-4">
            <div class="col-12">
              <div class="border border-1 rounded-2 p-3">
                <div class="">
                  <h5 class="mb-3">Customer’s information</h5>
                  <p class="mb-0" style="word-break: break-word;overflow-wrap: break-word;">
                    Provide the customer's information to the company for invoicing. Once the payment is processed and the sale is confirmed, your commission will be paid.
                  </p>
                 
                </div>
                <div class="py-4">
                  <div class="row">
                    <!-- Name Field -->
                    <div class="col-md-6 mb-3">
                      <label for="customer_name" class="form-label">Name:</label>
                      <input type="text" name="customer_name" value="<?php echo esc_attr($customer_name); ?>" id="customer_name" class="form-control" placeholder="Customer Name">
                    </div>

                    <!-- Company Field -->
                    <div class="col-md-6 mb-3">
                      <label for="customer_company" class="form-label">Company:</label>
                      <input type="text" name="customer_company" value="<?php echo esc_attr($customer_company); ?>" id="customer_company" class="form-control" placeholder="Company Name">
                    </div>

                    <!-- Email Field -->
                    <div class="col-md-6 mb-3">
                      <label for="customer_email" class="form-label">Email:</label>
                      <input type="email" name="customer_email" value="<?php echo esc_attr($customer_email); ?>" id="customer_email" class="form-control" placeholder="Customer Email">
                    </div>

                    <!-- Address Field -->
                    <div class="col-md-6 mb-3">
                      <label for="customer_address" class="form-label">Address:</label>
                      <input type="text" name="customer_address" value="<?php echo esc_attr($customer_address); ?>" id="customer_address" class="form-control" placeholder="Customer Address">
                    </div>

                    <!-- Phone Number Field -->
                    <div class="col-md-6 mb-3">
                      <label for="customer_phone" class="form-label">Phone number:</label>
                      <input type="text" name="customer_phone" value="<?php echo esc_attr($customer_phone); ?>" id="customer_phone" class="form-control" placeholder="Phone number">
                    </div>
                  </div>
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