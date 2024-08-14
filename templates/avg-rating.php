<?php 

$rating = Rating::get_instance();

$avg_score = $rating->calculate_average_rating($agent_id);

// Validar si se obtuvo un puntaje promedio válido y mayor a 0
if ($avg_score && $avg_score > 0) {
    // Partes entera y decimal del avg_score
    $int_part = floor($avg_score);
    $decimal_part = $avg_score - $int_part;
    $empty_stars = 5 - ceil($avg_score);
    ?>
    
    <div class="star-rating">
        <?php
        // Mostrar estrellas llenas
        for ($i = 0; $i < $int_part; $i++) {
            echo '<span class="fa fa-star filled"></span>';
        }

        // Mostrar una estrella parcial si hay decimales
        if ($decimal_part > 0) {
            echo '<span class="fa fa-star-half-o filled"></span>';
        }

        // Mostrar estrellas vacías
        for ($i = 0; $i < $empty_stars; $i++) {
            echo '<span class="fa fa-star empty"></span>';
        }
        ?>
        <span class="average-score">(<?php echo number_format($avg_score, 1); ?>)</span>
    </div>
    
    <?php
}

?>
