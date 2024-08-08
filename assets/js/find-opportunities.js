jQuery(document).ready(function($) {
    function fetchResults() {
        var form = $('#filters-form');
        var formData = form.serialize(); // Serializa los datos del formulario

        // Mostrar el spinner
        $('#spinner').show();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'GET',
            data: formData + '&action=my_ajax_filter',
            success: function(response) {
                // Reemplazar el contenido de la sección de resultados con la respuesta de AJAX
                $('#results-section').html(response);
               // Extraer y limpiar los precios del HTML generado
        var prices = $(".result-item .price").map(function() {
            var priceText = $(this).text();
            // Eliminar el signo de dólar y las comas
            var cleanedPrice = priceText.replace(/[$,]/g, '');
            // Convertir el texto limpio en un número
            return parseFloat(cleanedPrice);
        }).get();

        // Verificar si la lista de precios no está vacía
        if (prices.length > 0) {
            // Encontrar el mínimo y máximo precio
            var minPrice = Math.min.apply(null, prices);
            var maxPrice = Math.max.apply(null, prices);

            // Configurar los sliders con los valores mínimos y máximos
            $('#fromSlider').attr('min', minPrice).attr('max', maxPrice).val(minPrice);
            $('#toSlider').attr('min', minPrice).attr('max', maxPrice).val(maxPrice);
            $('#fromInput').attr('min', minPrice).attr('max', maxPrice).val(minPrice);
            $('#toInput').attr('min', minPrice).attr('max', maxPrice).val(maxPrice);
        } else {
            // Manejar el caso cuando no hay precios disponibles
            $('#fromSlider').attr('min', 0).attr('max', 100).val(0);
            $('#toSlider').attr('min', 0).attr('max', 100).val(100);
            $('#fromInput').attr('min', 0).attr('max', 100).val(0);
            $('#toInput').attr('min', 0).attr('max', 100).val(100);
        }

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

    function controlFromInput(fromSlider, fromInput, toInput, controlSlider) {
        const [from, to] = getParsed(fromInput, toInput);
        fillSlider(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
        if (from > to) {
            fromSlider.value = to;
            fromInput.value = to;
        } else {
            fromSlider.value = from;
        }
    }
        
    function controlToInput(toSlider, fromInput, toInput, controlSlider) {
        const [from, to] = getParsed(fromInput, toInput);
        fillSlider(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
        setToggleAccessible(toInput);
        if (from <= to) {
            toSlider.value = to;
            toInput.value = to;
        } else {
            toInput.value = from;
        }
    }
    
    function controlFromSlider(fromSlider, toSlider, fromInput) {
      const [from, to] = getParsed(fromSlider, toSlider);
      fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
      if (from > to) {
        fromSlider.value = to;
        fromInput.value = to;
      } else {
        fromInput.value = from;
      }
    }
    
    function controlToSlider(fromSlider, toSlider, toInput) {

      const [from, to] = getParsed(fromSlider, toSlider);

      fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
      setToggleAccessible(toSlider);
      if (from <= to) {
        toSlider.value = to;
        toInput.value = to;
      } else {
        toInput.value = from;
        toSlider.value = from;
      }
    }
    
    function getParsed(currentFrom, currentTo) {
      const from = parseInt(currentFrom.value, 10);
      const to = parseInt(currentTo.value, 10);
      return [from, to];
    }
    
    function fillSlider(from, to, sliderColor, rangeColor, controlSlider) {
        const rangeDistance = to.max-to.min;
        const fromPosition = from.value - to.min;
        const toPosition = to.value - to.min;
        controlSlider.style.background = `linear-gradient(
          to right,
          ${sliderColor} 0%,
          ${sliderColor} ${(fromPosition)/(rangeDistance)*100}%,
          ${rangeColor} ${((fromPosition)/(rangeDistance))*100}%,
          ${rangeColor} ${(toPosition)/(rangeDistance)*100}%, 
          ${sliderColor} ${(toPosition)/(rangeDistance)*100}%, 
          ${sliderColor} 100%)`;
    }
    
    function setToggleAccessible(currentTarget) {
      const toSlider = document.querySelector('#toSlider');
      if (Number(currentTarget.value) <= 0 ) {
        toSlider.style.zIndex = 2;
      } else {
        toSlider.style.zIndex = 0;
      }
    }
    
    const fromSlider = document.querySelector('#fromSlider');
    const toSlider = document.querySelector('#toSlider');
    const fromInput = document.querySelector('#fromInput');
    const toInput = document.querySelector('#toInput');
    fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
    setToggleAccessible(toSlider);
    
    fromSlider.oninput = () => controlFromSlider(fromSlider, toSlider, fromInput);
    toSlider.oninput = () => controlToSlider(fromSlider, toSlider, toInput);
    fromInput.oninput = () => controlFromInput(fromSlider, fromInput, toInput, toSlider);
    toInput.oninput = () => controlToInput(toSlider, fromInput, toInput, toSlider);


});

