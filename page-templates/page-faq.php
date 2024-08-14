<?php
/**
 * Template Name: Faq Page
 * Description: Page template for faqs
 */

// Start output buffering to prevent header modification errors
ob_start();


$faqs = carbon_get_post_meta(get_the_ID(), "faqs");


get_header();
?>

<section style="background-image:url(<?php echo get_template_directory_uri() . "/assets/img/breadcrumb-bg.jpg"; ?>);">
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-md-12 my-auto">
                    <h1 class="text-white"><?php echo esc_html("Faqs"); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pt-120 pb-95">
    <div class="container">
    <div class="accordion w-100" id="basicAccordion">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button data-mdb-button-init  data-mdb-collapse-init class="accordion-button collapsed" type="button"
        data-mdb-target="#basicAccordionCollapseOne" aria-expanded="false" aria-controls="collapseOne">
        Question #1
      </button>
    </h2>
    <div id="basicAccordionCollapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
      data-mdb-parent="#basicAccordion" style="">
      <div class="accordion-body">
        <strong>This is the first item's accordion body.</strong> It is shown by default,
        until the collapse plugin adds the appropriate classes that we use to style each
        element. These classes control the overall appearance, as well as the showing and
        hiding via CSS transitions. You can modify any of this with custom CSS or overriding
        our default variables. It's also worth noting that just about any HTML can go within
        the <code>.accordion-body</code>, though the transition does limit overflow.
      </div>
    </div>
  </div>
 
</div>
    </div>
</section>

<?php get_footer();?>


