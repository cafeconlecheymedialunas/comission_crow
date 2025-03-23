<?php
/**
 * Template Name: FAQs Page
 * Description: Page template for FAQs
 */

get_header();

$faqs_company = carbon_get_post_meta(get_the_ID(), 'faqs_company');
$faqs_company_title = carbon_get_post_meta(get_the_ID(), 'faqs_company_title');
$faqs_agent = carbon_get_post_meta(get_the_ID(), 'faqs_agent');
$faqs_agent_title = carbon_get_post_meta(get_the_ID(), 'faqs_agent_title');

?>

<section class="faqs">
    <div class="container">
        <div class="row">

            <div class="col-md-6">
                <h2 class="faqs-title"><?php echo esc_html($faqs_company_title); ?></h2>
                <div class="accordion" id="accordionCompany">
                    <?php if ($faqs_company): ?>
                        <?php foreach ($faqs_company as $index => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingCompany<?php echo $index; ?>">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompany<?php echo $index; ?>" aria-expanded="true" aria-controls="collapseCompany<?php echo $index; ?>">
                                        <?php echo esc_html($faq['question']); ?>
                                    </button>
                                </h2>
                                <div id="collapseCompany<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="headingCompany<?php echo $index; ?>" data-bs-parent="#accordionCompany">
                                    <div class="accordion-body">
                                        <?php echo wp_kses_post($faq['answer']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="faqs-title"><?php echo esc_html($faqs_agent_title); ?></h2>
                <div class="accordion" id="accordionAgent">
                    <?php if ($faqs_agent): ?>
                        <?php foreach ($faqs_agent as $index => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingAgent<?php echo $index; ?>">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAgent<?php echo $index; ?>" aria-expanded="true" aria-controls="collapseAgent<?php echo $index; ?>">
                                        <?php echo esc_html($faq['question']); ?>
                                    </button>
                                </h2>
                                <div id="collapseAgent<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="headingAgent<?php echo $index; ?>" data-bs-parent="#accordionAgent">
                                    <div class="accordion-body">
                                        <?php echo wp_kses_post($faq['answer']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
get_footer();
