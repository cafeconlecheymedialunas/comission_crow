jQuery(document).ready(function ($) {
  function displayFormErrors(form, data) {
    // Clear previous error messages
    $(form).find(".error-message").remove();

    // Iterate over the errors and display them next to the respective fields
    if (data.fields && data.fields.length > 0) {
      $.each(data.fields, function (fieldName, errorMessages) {
        var field = $(form).find("#" + fieldName);
        if (field.length) {
          // Remove existing error messages for the field
          field.next(".error-message").remove();

          // Display only the first error message
          if (errorMessages.length > 0) {
            var errorElement = $(
              '<div class="error-message text-sm text-danger"></div>',
            ).text(errorMessages[0]);
            field.after(errorElement);
          }
        }
      });
    }

    if (data.general && data.general.length > 0) {
      var generalErrorsElement = $(".general-errors");
      if (generalErrorsElement.length) {
        // Clear previous general errors
        generalErrorsElement.empty();

        // Append all general error messages
        const errors = [];
        $.each(data.general, function (index, message) {
          var errorElement = $('<div class="text-danger"></div>').text(message);
          errors.push(errorElement);
        });
        generalErrorsElement.html(errors);
        generalErrorsElement.show();
      }
    }
  }
  var $customSpinner = $(".custom-spinner");

  $("#create-dispute").on("submit", function (e) {
    e.preventDefault();
    const form = this;
    var formData = new FormData(form);
    formData.append("action", "create_dispute");

    //formData.append("commission_request_ids", "create_dispute");

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: formData,

      cache: false,
      processData: false,
      contentType: false,
      beforeSend: function () {
        $customSpinner.addClass("d-flex");
      },
      success: function (response) {
        console.log(response);
        if (response.success) {
          Swal.fire({
            title: "The Dispute has been saved.",
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
      error: function (xhr, status, error) {
        alert("AJAX error: " + error);
      },
      complete: function () {
        $customSpinner.removeClass("d-flex").hide();
      },
    });
  });

  $(".delete-dispute-form").submit(function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);
    formData.append("action", "delete_dispute");

    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        $customSpinner.addClass("d-flex");
        $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: formData,
          processData: false,
          contentType: false,
          cache: false,
          success: function (response) {
            console.log(response);
            if (response.success) {
              Swal.fire({
                title: "The dispute has been deleted.",
                icon: "success",
                showConfirmButton: false,
                timer: 2000,
              }).then(function () {
                location.reload(); // Puedes eliminar esta línea si no quieres recargar la página
              });
            } else {
              Swal.fire(
                "Error!",
                response.data.message ||
                  "There was an error deleting the dispute.",
                "error",
              );
            }
          },
          error: function (error) {
            Swal.fire(
              "Error!",
              "There was an error deleting the dispute. Please try again later.",
              "error",
            );
          },
          complete: function () {
            $customSpinner.removeClass("d-flex").hide();
          },
        });
      }
    });
  });
});
