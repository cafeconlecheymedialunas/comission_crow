<section class="header-page-title" style="background-image:url(<?php echo get_template_directory_uri() . "/assets/img/breadcrumb-bg.jpg"; ?>);">
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-md-12 my-auto">
                    <h1 class="text-white">
                        <?php
                    
                        if (isset($page_custom_title) && !empty($page_custom_title)) {
                            echo esc_html($page_custom_title);
                        } else {
                            // Lógica actual para mostrar el título basado en el contexto de la página
                            if (is_home()) {
                                // Página del blog (página de inicio de las publicaciones)
                                echo 'Blog';
                            } elseif (is_search()) {
                                // Página de resultados de búsqueda
                                printf('Search results for: %s', get_search_query());
                            } elseif (is_category()) {
                                // Página de archivo de categoría
                                echo 'Archive: ' . single_cat_title('', false);
                            } elseif (is_tag()) {
                                // Página de archivo de etiqueta
                                echo 'Archive: ' . single_tag_title('', false);
                            } elseif (is_author()) {
                                // Página de archivo de autor
                                echo 'Archive: ' . get_the_author();
                            } elseif (is_archive()) {
                                // Otras páginas de archivo (como fechas, taxonomías personalizadas, etc.)
                                echo 'Archive';
                            } else {
                                // Título por defecto para páginas normales
                                the_title();
                            }
                        }
                        ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</section>
