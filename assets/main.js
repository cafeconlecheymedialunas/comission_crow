jQuery(function ($) {
    'use strict';

    function handleAjaxFormSubmit(formId, action, successCallback) {
        $(formId).on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: $(this).serialize() + '&action=' + action,
                success: function (response) {
                    if (response.success) {
                        successCallback(response);
                    } else {
                        $(formId + '_message').html('<p>' + response.data + '</p>');
                    }
                }
            });
        });
    }

    $(document).ready(function () {
        handleAjaxFormSubmit('#register_form', 'user_register', function (response) {
            $('#register_message').html('<p>' + response.data + '</p>');
        });

        handleAjaxFormSubmit('#login_form', 'user_login', function () {
            window.location.href = '/dashboard';
        });
    });
});
