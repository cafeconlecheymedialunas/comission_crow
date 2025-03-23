<div class="py-5 benefits-section">
  <div class="container p-5">
  <div class="row">
    <!-- Columna para Companies -->
    <div class="col-md-6 mb-4 mb-md-0">
      <h3>Companies</h3>
      <ul class="list-unstyled mt-3 benefits ">
        <?php if (!empty($company_benefits)): ?>
          <?php foreach ($company_benefits as $benefit): ?>
            
            <li class="mb-3 benefit">
            <i class="fa-solid fa-check"></i>
              <?php echo wp_kses_post($benefit['benefit_html']); ?>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Columna para Sales Agents -->
    <div class="col-md-6">
      <h3>Sales Agents</h3>
      <ul class="list-unstyled mt-3 benefits">
        <?php if (!empty($agent_benefits)): ?>
          <?php foreach ($agent_benefits as $benefit): ?>
            <li class="mb-3 benefit">
              <i class="fa-solid fa-check"></i>
              <?php echo wp_kses_post($benefit['benefit_html']); ?>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  </div>
 
</div>