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
    $('.industry .slider-container').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        dots: false,
        arrows: true,
        autoplay: false,
        autoplaySpeed: 3000,
        prevArrow: '<button type="button" class="prev"><i class="fas fa-chevron-left"></i></button>',
        nextArrow: '<button type="button" class="next"><i class="fas fa-chevron-right"></i></button>',
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
    $('.brands .slider-container').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        dots: false,
        arrows: false,
        autoplay: false,
        autoplaySpeed: 3000,
        draggable: true, // Asegúrate de que el arrastre esté habilitado
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 500,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
    


     // Función para verificar si un elemento está en la ventana de visualización
     function isElementInView(element) {
        var elementTop = $(element).offset().top;
        var elementBottom = elementTop + $(element).outerHeight();

        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    }

    // Función para animar los contadores
    function animateCounter($counter) {
        var countTo = $counter.data('count');
        $({ countNum: 0 }).animate({ countNum: countTo }, {
            duration: 2000, // Duración de la animación (2 segundos)
            easing: 'swing',
            step: function() {
                $counter.find('span').text(Math.floor(this.countNum));
            },
            complete: function() {
                $counter.find('span').text(this.countNum);
            }
        });
    }

    // Verificar si los contadores están en la vista y animarlos
    function checkCounters() {
        $('.count').each(function() {
            var $counter = $(this);
            if (isElementInView($counter) && !$counter.hasClass('animated')) {
                animateCounter($counter);
                $counter.addClass('animated');
            }
        });
    }

    // Ejecutar la función al cargar la página y al hacer scroll
    $(window).on('scroll', checkCounters);
    $(window).on('load', checkCounters);



  
       
    
      
    
    
});
