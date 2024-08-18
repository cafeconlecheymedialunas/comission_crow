<?php
/**
 * Template Name: Contact Page
 * Description: Page template for contact
 */

ob_start();


$latitud = carbon_get_post_meta(get_the_ID(), "latitud");
$longitud = carbon_get_post_meta(get_the_ID(), "longitud");
$street = carbon_get_theme_option('billing_address_street');
$number = carbon_get_theme_option('billing_address_number');
$city = carbon_get_theme_option('billing_address_city');
$state = carbon_get_theme_option('billing_address_state');

$email = carbon_get_theme_option('billing_company_email');
$phone = carbon_get_theme_option('billing_company_phone');

$address_parts = array_filter(array(
    $street,
    $number,
    $city,
    $state,
    $location
));

// Combinar los componentes con una coma y un espacio
$combined_address = implode(', ', $address_parts);
get_header();
?>


<section class="mb-4 mt-4 contact">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="contact-info email">
           
                <div class="icon">
                    <i class="fa-regular fa-envelope"></i> 
                </div>
                <h3>Email</h3>
                <div class="contact">
                    <p><?php echo $email;?></p>
                </div>
                </div>
               
            </div>
            <div class="col-md-4">
                <div class="contact-info contact">
                    
                    <div class="icon">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <h3>Contact</h3>
                    <div class="contact">
                        <p><?php echo $phone;?></p>
                    </div>
                </div>
             
            </div>
            <div class="col-md-4">
                <div class="contact-info location">
                    
                    <div class="icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <h3>Location</h3>
                    <div class="contact">
                        <p><?php echo $combined_address;?></p>
                    </div>
                </div>
             
            </div>
        </div>
        <div id="map" class=""></div>
        <div class="pt-4">

       
        <?php
         $front_page_id = get_option('page_on_front');
       $hero_button_title = carbon_get_post_meta( $front_page_id, 'hero_button_title');
       $hero_button_description = carbon_get_post_meta( $front_page_id, 'hero_button_description');
       $hero_button_image = carbon_get_post_meta( $front_page_id, 'hero_button_image');
       $hero_button_button_text = carbon_get_post_meta( $front_page_id, 'hero_button_button_text');
       $hero_button_image = wp_get_attachment_image($hero_button_image,"full");
$spinner_template = 'templates/frontend/hero-button.php';
if (locate_template($spinner_template)) {
    include locate_template($spinner_template);
}
?>
 </div>
    </div>
</section>
<style>
    #map {
    height: 500px; /* Ajusta la altura según sea necesario */
    width: 100%;   /* Asegúrate de que el mapa ocupe todo el ancho del contenedor */
}
</style>
<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Coordenadas obtenidas con PHP
            var lat = <?php echo json_encode($latitud); ?>;
            var lng = <?php echo json_encode($longitud); ?>;
            
            // Inicializar el mapa
            var map = L.map('map',{ dragging: false,  scrollWheelZoom: false, // Deshabilita el zoom con la rueda del ratón
            dragging: false, // Deshabilita el arrastre del mapa
            touchZoom: false, // Deshabilita el zoom táctil en dispositivos móviles
            doubleClickZoom: false, // Deshabilita el zoom con doble clic
            boxZoom: false, // Deshabilita el zoom con caja de selección
            
             }).setView([lat, lng], 13);
            
            // Añadir una capa de mapa base
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Añadir un marcador en las coordenadas
            L.marker([lat, lng]).addTo(map)
                .bindPopup('Ubicación: ' + lat + ', ' + lng)
                .openPopup();
        });
    </script>

<?php get_footer();?>


