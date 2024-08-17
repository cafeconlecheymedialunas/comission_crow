<?php
$current_user = wp_get_current_user();

$associated_post = ProfileUser::get_instance()->get_user_associated_post_type();

$currency = wp_get_post_terms($associated_post->ID,"currency");
$currency_code = !empty($currency) ? carbon_get_term_meta($currency[0]->term_id, 'currency_code') : "USD";
$currency_symbol = !empty($currency) ? carbon_get_term_meta($currency[0]->term_id, 'currency_symbol') : "$";

$args_agents = [
    'post_type' => 'commercial_agent',
    'posts_per_page' => -1,
];
$commercial_agents_query = new WP_Query($args_agents);
$commercial_agents = $commercial_agents_query->posts;

$args_companies = [
    'post_type' => 'company',
    'posts_per_page' => -1,
];
$companies_query = new WP_Query($args_companies);
$companies = $companies_query->posts;

$args_opportunities = [
    'post_type' => 'opportunity',
    'posts_per_page' => -1,
];
$opportunities_query = new WP_Query($args_opportunities);
$opportunities = $opportunities_query->posts;
?>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Create a Contract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="save-contract" enctype="multipart/form-data">
          <div class="row">

              <div class="col-md-6">
                <label for="company_id" class="form-label">Company:</label>
                <select name="company_id" id="company_id" class="form-select"
                  <?php echo ($associated_post && $associated_post->post_type === 'company') ? 'disabled' : ''; ?>>
                  <option value="">Select a Company</option>
                  <?php foreach ($companies as $company): ?>
                    <option value="<?php echo esc_attr($company->ID); ?>"
                      <?php echo ($associated_post && $associated_post->post_type === 'company' && $associated_post->ID === $company->ID) ? 'selected' : ''; ?>>
                      <?php echo esc_html($company->post_title); ?>
                    </option>
                  <?php endforeach;?>
                </select>

                <div class="error-message"></div>
                <?php if ($associated_post->post_type === 'company' && $associated_post->ID): ?>
                <input type="hidden" name="company_id" id="company_id_hidden" value="<?php echo esc_attr($associated_post->ID); ?>">
                <?php endif;?>
              </div>



              <div class="col-md-6">
                <label for="commercial_agent_id" class="form-label">Commercial Agent:</label>
                <select name="commercial_agent_id" id="commercial_agent_id" class="form-select"
                  <?php echo ($associated_post && $associated_post->post_type === 'commercial_agent') ? 'disabled' : ''; ?>>
                  <option value="">Select a Commercial Agent</option>
                  <?php foreach ($commercial_agents as $agent): ?>
                    <option value="<?php echo esc_attr($agent->ID); ?>"
                      <?php echo ($associated_post && $associated_post->post_type === 'commercial_agent' && $associated_post->ID === $agent->ID) ? 'selected' : ''; ?>>
                      <?php echo esc_html($agent->post_title); ?>
                    </option>
                  <?php endforeach;?>
                </select>
                <?php if ($associated_post->post_type === 'commercial_agent' && $associated_post->ID): ?>
                <input type="hidden" name="commercial_agent_id" id="commercial_agent_id_hidden" value="<?php echo esc_attr($associated_post->ID); ?>">
                <?php endif;?>
                <div class="error-message"></div>
              </div>



              <div class="col-md-6">
                <label for="opportunity_id" class="form-label">Opportunity:</label>
                <select name="opportunity_id" id="opportunity_id" class="form-select">
                  <option value="">Select an Opportunity</option>
                  <?php foreach ($opportunities as $opportunity): ?>
                    <option value="<?php echo esc_attr($opportunity->ID); ?>">
                      <?php echo esc_html($opportunity->post_title); ?>
                    </option>
                  <?php endforeach;?>
                </select>
                <div class="error-message"></div>
              </div>


            <div class="col-md-12">
              <label for="content" class="form-label">Explains the details of the proposal</label>
              <div class="editor-container" id="content" data-target="content"></div>
              <input type="hidden" id="content" name="content">
              <div class="error-message"></div>
            </div>


            <div class="col-md-6">
            <label for="minimal_price" class="form-label">Minimal Price:</label>
              <div class="input-group mb-3">
                <span class="input-group-text"><?php echo "$currency_symbol ($currency_code)"; ?></span>
                <input type="text" name="minimal_price" id="minimal_price" class="form-control" placeholder="Minimal Price">
                <div class="error-message"></div>
              </div>
            </div>

            <div class="col-md-6">
              <label for="commission" class="form-label">Commission:</label>
              <input type="text" name="commission" id="commission" class="form-control" placeholder="Commission">
              <div class="error-message"></div>
            </div>
          </div>

          <input type="hidden" name="security" value="<?php echo wp_create_nonce('create_contract_nonce'); ?>">
          <input type="hidden" name="entity_type" value="<?php echo $associated_post->post_type; ?>">

          <div class="alert alert-warning general-errors" role="alert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="save-contract" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
