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
                        successCallback(response); // Llama a la función de éxito definida
                    } else {
                        // Mostrar mensajes de error en lugar de recargar la página
                        $('#profile-update-message').html('<p>' + response.data + '</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText); // Manejar errores de AJAX
                }
            });
        });
    }

    $('#password_recovery_form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'password_recovery',
                user_email: $('#user_email').val(),
            },
            success: function (response) {
                if (response.success) {
                    $('#password_recovery_message').html('<p>' + response.data + '</p>');
                } else {
                    $('#password_recovery_message').html('<p>' + response.data + '</p>');
                }
            }
        });
    });

    $('#password_reset_form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'password_reset',
                key: $('#reset_key').val(),
                login: $('#reset_login').val(),
                new_password: $('#new_password').val(),
            },
            success: function (response) {
                $('#password_reset_message').html('<p>' + response.data + '</p>');
            }
        });
    });

    $('#seller-profile-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $('#seller-profile-form').serialize();

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {...formData,action:"update_seller_profile"},
            
            beforeSend: function() {
                // Puedes mostrar un spinner o mensaje de carga aquí
            },
            success: function(response) {
                $('#profile-update-message').html(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $(document).ready(function () {
        handleAjaxFormSubmit('#register_form', 'user_register', function (response) {
            $('#register_message').html('<p>' + response.data + '</p>');
        });

        handleAjaxFormSubmit('#seller-profile-form', 'update_seller_profile', function (response) {
            // Aquí puedes decidir qué hacer en caso de éxito, por ejemplo, mostrar un mensaje de éxito
            $('#profile-update-message').html('<p>' + response.data + '</p>');
        });

        handleAjaxFormSubmit('#login_form', 'user_login', function () {
            window.location.href = '/dashboard';
        });
    });
});
