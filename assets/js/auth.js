jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  function displayFormErrors(form, data) {
    // Clear previous error messages
    $(form).find(".error-message").remove();

    // Iterate over the errors and display them next to the respective fields
    $.each(data.fields, function (fieldName, errorMessages) {
      var field = $(form).find('[name="' + fieldName + '"]');
      if (field.length) {
        // Remove existing error messages for the field
        field.next(".error-message").remove();

        // Create and append error messages
        $.each(errorMessages, function (index, message) {
          var errorElement = $(
            '<div class="error-message text-sm text-danger"></div>',
          ).text(message);
          field.after(errorElement);
        });
      }
    });

    if (data.general && data.general.length > 0) {
      var generalErrorsElement = $(".general-errors");
      if (generalErrorsElement.length) {
        // Clear previous general errors
        generalErrorsElement.empty();

        // Append all general error messages
        const errors = [];
        $.each(data.general, function (index, message) {
          var errorElement = $(
            '<div class="error-message text-sm text-danger"></div>',
          ).text(message);
          errors.push(errorElement);
        });
        generalErrorsElement.html(errors);
      }
    }
  }

  $("#registration_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=register_user",
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();

        if (response.success) {
          Swal.fire({
            title: "You have successfully registered!",
            text: "You will be redirected in a few seconds to the login page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000, // 2 segundos
          }).then(function () {
            window.location.href = "/auth/?action=login";
          });
        } else {
          displayFormErrors(form, response.data);
        }
      },
    });
  });

  $("#login_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=login_user",
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();
        if (response.success) {
          Swal.fire({
            title: "You are successfully logged in!",
            text: "You will be redirected to the dashboard",
            icon: "success",
            showConfirmButton: false,
            timer: 2000, // 2 segundos
          }).then(function () {
            if (response.data.roles[0]) {
              let key =
                response.data.roles[0] === "commercial_agent"
                  ? "commercial-agent"
                  : "company";
              window.location.href = `/dashboard/${key}/profile`;
            }
          });
        } else {
          displayFormErrors(form, response.data);
        }
      },
    });
  });

  $("#reset_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=reset_password",
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();
        if (response.success) {
          Swal.fire({
            title: "Application received",
            text: "A link to reset your password was emailed to you.",
            icon: "success",
            showConfirmButton: true,
          });
        } else {
          displayFormErrors(form, response.data);
        }
      },
    });
  });

  $("#new_password_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=set_new_password",
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();
        if (response.success) {
          Swal.fire({
            title: "You have successfully changed the password!",
            text: "You will be redirected in a few seconds to the login page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000, // 2 segundos
          }).then(function () {
            window.location.href = "/auth/?action=login";
          });
        } else {
          displayFormErrors(form, response.data);
        }
      },
    });
  });
});
