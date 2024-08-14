jQuery(document).ready(function($) {
    
    function fetchResults() {
        var form = $('#filters-form');
        var formData = form.serialize();

        $('#spinner').show();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'GET',
            data: formData + '&action=load_commercial_agents',
            success: function(response) {
                $('#results-section').html(response);
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            },
            complete: function() {
                $('#spinner').hide();
            }
        });
    }

    $('#filters-form').on('input', '.filter', function() {
        fetchResults();
    });

    fetchResults();

    $('#clear-filters').on('click', function() {

        $('#filters-form')[0].reset();
        
        fetchResults();
    });
});
