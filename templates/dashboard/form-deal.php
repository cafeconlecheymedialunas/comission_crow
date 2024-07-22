<?php
$current_user = wp_get_current_user();

$company = Company::get_instance();
$company_post = $company->get_company();

$commercial_agent = CommercialAgent::get_instance();
$commercial_agent_post = $commercial_agent->get_commercial_agent();

// Consultar los Commercial Agents
$args_agents = [
    'post_type' => 'commercial_agent',
    'posts_per_page' => -1
];
$commercial_agents_query = new WP_Query($args_agents);
$commercial_agents = $commercial_agents_query->posts;

// Consultar las Companies
$args_companies = [
    'post_type' => 'company',
    'posts_per_page' => -1
];
$companies_query = new WP_Query($args_companies);
$companies = $companies_query->posts;

// Consultar las Opportunities
$args_opportunities = [
    'post_type' => 'opportunity',
    'posts_per_page' => -1
];
$opportunities_query = new WP_Query($args_opportunities);
$opportunities = $opportunities_query->posts;
?>

<form id="save-deal" enctype="multipart/form-data">
    <h2>Deal</h2>
    <div class="row">
        <?php if($companies): ?>
            <div class="col-md-6">
                <label for="company" class="form-label">Company:</label>
                <select name="company[]" class="form-select">
                    <option value="">Select a Company</option>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?php echo esc_attr($company->ID); ?>">
                            <?php echo esc_html($company->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if($commercial_agents): ?>
            <div class="col-md-6">
                <label for="commercial_agent" class="form-label">Commercial Agent:</label>
                <select name="commercial_agent[]" class="form-select">
                    <option value="">Select a Commercial Agent</option>
                    <?php foreach ($commercial_agents as $agent): ?>
                        <option value="<?php echo esc_attr($agent->ID); ?>">
                            <?php echo esc_html($agent->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if($opportunities): ?>
            <div class="col-md-6">
                <label for="opportunity" class="form-label">Opportunity:</label>
                <select name="opportunity[]" class="form-select">
                    <option value="">Select an Opportunity</option>
                    <?php foreach ($opportunities as $opportunity): ?>
                        <option value="<?php echo esc_attr($opportunity->ID); ?>">
                            <?php echo esc_html($opportunity->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="col-md-6">
            <label for="minimal_price" class="form-label">Minimal Price:</label>
            <input type="text" name="minimal_price" class="form-control" placeholder="Minimal Price">
        </div>

        <div class="col-md-6">
            <label for="commission" class="form-label">Commission:</label>
            <input type="text" name="commission" class="form-control" placeholder="Commission">
        </div>
    </div>

    <input type="hidden" name="security" value="<?php echo wp_create_nonce('save_deal_nonce'); ?>">
    <?php if($company_post):?>
    <input type="hidden" name="company_id" value="<?php echo $company_post->ID;?>">
    <input type="hidden" name="entity_type" value="<?php echo "company";?>">
    <?php else:?>
    <input type="hidden" name="company_id" value="<?php echo $commercial_agent_post->ID;?>">
    <input type="hidden" name="entity_type" value="<?php echo "commercial_agent";?>">
    <?php endif;?>
    <div class="errors"></div>
    <div class="col-12 mt-3">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
