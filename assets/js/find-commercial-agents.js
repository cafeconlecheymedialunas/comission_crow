jQuery(document).ready(function($) {
    function fetchResults() {
        var form = $('#filters-form');
        var formData = form.serialize();

        $('#spinner').show(); // Show spinner

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'GET',
            data: formData + '&action=load_commercial_agents',
            success: function(response) {
                $('#results-section').html(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            },
            complete: function() {
                $('#spinner').hide(); // Hide spinner
            }
        });
    }

    // Initial fetch
    fetchResults();

    // Trigger fetch when filters change
    $('.filter').on('change', fetchResults);

    // Clear filters
    $('#clear-filters').on('click', function() {
        $('#filters-form').trigger("reset");
        $('.filter').trigger('change'); // Trigger change event to refresh results
    });
});