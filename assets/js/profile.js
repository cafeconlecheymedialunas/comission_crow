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

  $("#agent-profile-form").on("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    var form = $(this);
    var formData = new FormData(form[0]); // Usar el primer elemento del formulario
    formData.append("action", "save_agent_profile");

    console.log(...formData.entries()); // Ver los datos enviados

    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();

        if (response.success) {
          console.log(response);

          Swal.fire({
            title: "Profile updated successfully!",
            text: "Redirecting to the profile page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000,
          }).then(function () {
            location.reload();
          });
        } else {
          displayFormErrors(form, response.data);
        }
      },
      error: function (error) {
        console.error("Error updating profile:", error);
        $customSpinner.removeClass("d-flex").hide();
      },
    });
  });

  $("#company-profile-form").on("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    var form = $(this);
    var formData = new FormData(form[0]); // Usar el primer elemento del formulario
    formData.append("action", "save_company_profile");

    $customSpinner.addClass("d-flex");

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();
        console.log(response);
        if (response.success) {
          Swal.fire({
            title: "Profile updated successfully!",
            text: "Redirecting to the profile page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000,
          }).then(function () {
            location.reload();
          });
        } else {
          displayFormErrors(form, response.data);
        }
      },
      error: function (error) {
        console.error("Error updating profile:", error);
      },
    });
  });

  $("#update-user-data").on("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    var form = $(this);
    var formData = new FormData(form[0]); // Usar el primer elemento del formulario
    formData.append("action", "update_user_data");

    $customSpinner.addClass("d-flex");

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();

        if (response.success) {
          Swal.fire({
            title: "User updated successfully!",
            text: "Redirecting to the profile page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000,
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
          displayFormErrors(form, response.data); // Asegúrate de que displayFormErrors esté definido para manejar errores
        }
      },
      error: function (error) {
        console.error("Error updating profile:", error);
      },
    });
  });
});
