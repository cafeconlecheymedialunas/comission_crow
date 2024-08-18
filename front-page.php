<?php
get_header();

the_post();

// Obtener metadatos del post



$opportunities_title = carbon_get_post_meta(get_the_ID(), 'opportunities_title');
$opportunities_description = carbon_get_post_meta(get_the_ID(), 'opportunities_description');
$opportunities_select = carbon_get_post_meta(get_the_ID(), 'opportunities_select');

$industry_title = carbon_get_post_meta(get_the_ID(), 'industry_title');
$industry_description = carbon_get_post_meta(get_the_ID(), 'industry_description');
$industry_select = carbon_get_post_meta(get_the_ID(), 'industry_select');

$selected_agents_title = carbon_get_post_meta(get_the_ID(), 'selected_agents_title');
$selected_agents_description = carbon_get_post_meta(get_the_ID(), 'selected_agents_description');
$selected_agents = carbon_get_post_meta(get_the_ID(), 'selected_agents');

$counters = carbon_get_post_meta(get_the_ID(), 'counters');

// Datos de héroe
$hero_title = carbon_get_post_meta(get_the_ID(), 'hero_title');
$hero_description = carbon_get_post_meta(get_the_ID(), 'hero_description');
$hero_image = carbon_get_post_meta(get_the_ID(), 'hero_image');

$blog_title = carbon_get_post_meta(get_the_ID(), 'blog_title');
$blog_description = carbon_get_post_meta(get_the_ID(), 'blog_description');
$blog_image = carbon_get_post_meta(get_the_ID(), 'blog_image');
$brands = carbon_get_post_meta(get_the_ID(), 'brands');
$hero_button_title = carbon_get_post_meta(get_the_ID(), 'hero_button_title');
$hero_button_description = carbon_get_post_meta(get_the_ID(), 'hero_button_description');
$hero_button_image = carbon_get_post_meta(get_the_ID(), 'hero_button_image');
$hero_button_button_text = carbon_get_post_meta(get_the_ID(), 'hero_button_button_text');
$hero_button_image = wp_get_attachment_image($hero_button_image,"full");

?>

<div id="post-<?php the_ID();?>" <?php post_class('content');?>>
   

<?php
        wp_footer();


    require_once locate_template('templates/frontend/hero.php');
    require_once locate_template('templates/frontend/features.php');
    require_once locate_template('templates/frontend/opportunities.php');
    require_once locate_template('templates/frontend/industries.php');
    require_once locate_template('templates/frontend/selected-agents.php');
    require_once locate_template('templates/frontend/counters.php');
    require_once locate_template('templates/frontend/blog.php');
    require_once locate_template('templates/frontend/brands.php');
    require_once locate_template('templates/frontend/hero-button.php');
?>

   

  


 

 
   
   
      
    
   


</div>

<?php get_footer();?>
