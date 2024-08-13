jQuery(document).ready(function($) {
    console.log(ajax_object)
    // Función para actualizar el href del enlace y redirigir
    function updateRedirectUrl() {
        var postType = $('select[name="post_type"]').val(); // Obtener el valor del select
        var baseUrl = ajax_object.home_url+ "/"; // Cambia esto a la URL base de tu sitio
        var targetUrl;

        // Determinar la URL de destino basada en el valor seleccionado
        if (postType === 'commercial_agent') {
            targetUrl = baseUrl + 'find-agents'; // URL para "Commercial Agents"
        } else if (postType === 'opportunity') {
            targetUrl = baseUrl + 'find-opportunities'; // URL para "Opportunities"
        }

        // Actualizar el href del enlace y redirigir
        $('#view-button').attr('href', targetUrl).on('click', function(e) {
            e.preventDefault(); // Prevenir el comportamiento por defecto del enlace
            window.location.href = targetUrl; // Redirigir a la URL
        });
    }

    // Ejecutar la función cuando cambie el select
    $('select[name="post_type"]').on('change', updateRedirectUrl);

    // Ejecutar la función al cargar la página para asegurar el href correcto al inicio
    updateRedirectUrl();
});
