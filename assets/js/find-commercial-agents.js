jQuery(document).ready(function($) {
    
    function fetchResults() {
        var form = $('#filters-form');
        var formData = form.serialize(); // Serializa los datos del formulario

        // Mostrar el spinner
        $('#spinner').show();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'GET',
            data: formData + '&action=load_commercial_agents',
            success: function(response) {
                // Reemplazar el contenido de la sección de resultados con la respuesta de AJAX
                $('#results-section').html(response);
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            },
            complete: function() {
                // Ocultar el spinner después de que la solicitud se complete
                $('#spinner').hide();
            }
        });
    }

    // Ejecutar fetchResults cuando cambien los filtros
    $('#filters-form').on('input', '.filter', function() {
        fetchResults();
    });

    // Ejecutar fetchResults cuando se cargue la página por primera vez
    fetchResults();

    // Manejador para el botón "Limpiar Filtros"
    $('#clear-filters').on('click', function() {
        // Restablecer todos los campos del formulario
        $('#filters-form')[0].reset();
        
        // Ejecutar fetchResults sin filtros
        fetchResults();
    });
});
