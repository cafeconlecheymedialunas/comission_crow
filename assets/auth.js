jQuery(document).ready(function($) {
    $('#kamerpower_registration_form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        $.ajax({
            type: 'POST',
            url: authjs_ajax.ajax_url,
            data: form.serialize() + '&action=kamerpower_register_user',
            success: function(response) {
                var data = JSON.parse(response);
                if(data.registered) {
                    window.location.href = "/";
                } else {
                    $('#kamerpower_registration_errors').html(data.message);
                }
            }
        });
    });

    $('#kamerpower_login_form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        $.ajax({
            type: 'POST',
            url: authjs_ajax.ajax_url,
            data: form.serialize() + '&action=kamerpower_login_user',
            success: function(response) {
                var data = JSON.parse(response);
                if(data.loggedin) {
                    window.location.href = "/";
                } else {
                    $('#kamerpower_login_errors').html(data.message);
                }
            }
        });
    });

    $('#kamerpower_reset_form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        $.ajax({
            type: 'POST',
            url:authjs_ajax.ajax_url,
            data: form.serialize() + '&action=kamerpower_reset_password',
            success: function(response) {
                var data = JSON.parse(response);
                if(data.reset) {
                    $('#kamerpower_reset_errors').html(data.message);
                } else {
                    $('#kamerpower_reset_errors').html(data.message);
                }
            }
        });
    });

    $('#kamerpower_new_password_form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        $.ajax({
            type: 'POST',
            url:authjs_ajax.ajax_url,
            data: form.serialize() + '&action=kamerpower_set_new_password',
            success: function(response) {
                var data = JSON.parse(response);
                if(data.reset) {
                    $('#kamerpower_new_password_errors').html(data.message);
                } else {
                    $('#kamerpower_new_password_errors').html(data.message);
                }
            }
        });
    });

});
