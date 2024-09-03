<?php
$current_user = wp_get_current_user();

$associated_post = ProfileUser::get_instance()->get_user_associated_post_type();

$currency = wp_get_post_terms($associated_post->ID, "currency");
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

// Check if the current opportunity ID is valid
if ($current_opportunity) {
    $valid_opportunity_ids = wp_list_pluck($opportunities, 'ID');
    $is_current_opportunity_valid = in_array($current_opportunity, $valid_opportunity_ids);
}

$price = carbon_get_post_meta($current_opportunity, "price");
$commission = carbon_get_post_meta($current_opportunity, "commission");
$current_opportunity_company = carbon_get_post_meta($current_opportunity, "company");
?>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Send a Contract Proposal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="save-contract" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-6">
              <label for="company_id" class="form-label">Company:</label>
              <?php
              // Obtener el rol del usuario
              $user_role = wp_get_current_user()->roles[0]; // Obtén el rol actual del usuario

              // Determinar si el campo debe estar deshabilitado
              $readonly = ($user_role === 'company' || ($associated_post && $associated_post->post_type === 'company')) ? 'readonly' : '';
              $readonly = ($user_role === 'commercial_agent' || ($current_opportunity_company != '')) ? 'readonly' : '';

              // Determinar el valor seleccionado basado en el rol del usuario
              $selected_value = '';

              if ($user_role === 'company' && $associated_post) {
                  // Si el rol es company, usa el ID de associated_post
                  $selected_value = $associated_post->ID;
              } elseif ($user_role === 'commercial_agent') {
                  // Si el rol es commercial_agent, usa current_opportunity_company
                  $selected_value = $current_opportunity_company;
              }
              ?>

              <select name="company_id" id="company_id" class="form-select" <?php echo $readonly ? 'data-readonly="true"' : ''; ?>>
                <option value="">Select a Company</option>
                <?php foreach ($companies as $company): ?>
                  <option value="<?php echo esc_attr($company->ID); ?>"
                    <?php echo $selected_value == $company->ID ? 'selected' : ''; ?>>
                    <?php echo esc_html($company->post_title); ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <!-- Campo oculto para enviar el valor de company_id -->
              <?php if ($user_role === 'company' || $user_role === 'commercial_agent' || $selected_value): ?>
                <input type="hidden" name="company_id_hidden" value="<?php echo esc_attr($selected_value); ?>">
              <?php endif; ?>
            </div>

            <div class="col-md-6">
              <label for="commercial_agent_id" class="form-label">Commercial Agent:</label>
              <select name="commercial_agent_id" id="commercial_agent_id" class="form-select" <?php echo ($associated_post && $associated_post->post_type === 'commercial_agent') ? 'data-readonly="true"' : ''; ?>>
                <option value="">Select a Commercial Agent</option>
                <?php foreach ($commercial_agents as $agent): ?>
                  <option value="<?php echo esc_attr($agent->ID); ?>"
                    <?php echo ($associated_post && $associated_post->post_type === 'commercial_agent' && $associated_post->ID === $agent->ID) ? 'selected' : ''; ?>>
                    <?php echo esc_html($agent->post_title); ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <!-- Campo oculto para enviar el valor de commercial_agent_id -->
              <?php if ($associated_post->post_type === 'commercial_agent' && $associated_post->ID): ?>
                <input type="hidden" name="commercial_agent_id_hidden" value="<?php echo esc_attr($associated_post->ID); ?>">
              <?php endif; ?>
            </div>

            <div class="col-md-6">
              <label for="opportunity_id" class="form-label">Opportunity:</label>
              <select name="opportunity_id" id="opportunity_id" class="form-select" <?php echo $current_opportunity && $is_current_opportunity_valid ? 'data-readonly="true"' : ''; ?>>
                <option value="">Select an Opportunity</option>
                <?php foreach ($opportunities as $opportunity): ?>
                  <option value="<?php echo esc_attr($opportunity->ID); ?>"
                    <?php echo ($current_opportunity && $current_opportunity == $opportunity->ID) ? 'selected' : ''; ?>>
                    <?php echo esc_html($opportunity->post_title); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-12">
              <label for="content" class="form-label">Add a message for the company</label>
              <div class="editor-container" id="content" data-target="content"></div>
              <input type="hidden" id="content" name="content">
            </div>

            <div class="col-md-6">
              <label for="minimal_price" class="form-label">Minimal Price:</label>
              <div class="input-group mb-3">
                <span class="input-group-text"><?php echo "$currency_symbol ($currency_code)"; ?></span>
                <input type="text" name="minimal_price" value="<?php echo $price; ?>" <?php echo $price ? "readonly data-readonly='true'" : ""; ?> id="minimal_price" class="form-control" placeholder="Minimal Price">
              </div>
            </div>

            <div class="col-md-6">
              <label for="commission" class="form-label">Commission:</label>
              <div class="input-group commission mb-3">
                <input type="text" name="commission" id="commission" class="form-control" value="<?php echo $commission; ?>" <?php echo $commission ? "readonly data-readonly='true'" : ""; ?> placeholder="Commission">
                <span class="input-group-text" id="basic-addon2">%</span>
              </div>
            </div>
          </div>

          <input type="hidden" name="security" value="<?php echo wp_create_nonce('create_contract_nonce'); ?>">
          <input type="hidden" name="entity_type" value="<?php echo esc_attr($associated_post->post_type); ?>">

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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-readonly="true"]').forEach(function (select) {
      select.addEventListener('mousedown', function (event) {
        event.preventDefault();
      });
    });
  });
</script>

<style>
  [data-readonly="true"] {
    background-color: rgb(233, 236, 239) !important;
    pointer-events: none; /* Opcional: Desactiva los eventos del mouse en estos elementos para que no se pueda interactuar con ellos */
 
  }
</style>
