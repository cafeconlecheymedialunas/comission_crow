jQuery(document).ready(function ($) {
  // Initialize components for each file type
  function displayFormErrors(form, data) {
    // Clear previous error messages
    $(form).find(".error-message").remove();

    // Iterate over the errors and display them next to the respective fields
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

  /* let table = new DataTable('#opportunity-table', {
    responsive: true
});*/

  $(".add-new-url").click(function (e) {
    e.preventDefault();

    const newUrlRow = `
        <div class="row mb-3">
            <div class="col-sm-10 my-auto">
                <input type="url" name="videos[]" class="form-control" placeholder="Video URL">
                <small class="text-danger error-message" style="display: none;">Please fill out the URL field before adding another.</small>
            </div>
            <div class="col-sm-2">
                <i class="fas fa-trash remove-url"></i>
            </div>
        </div>
    `;
    $(".url-videos").append(newUrlRow);
  });

  $(document).on("click", ".remove-url", function () {
    $(this).closest(".row").remove();
  });

  var $customSpinner = $(".custom-spinner");

  $("#opportunity-form").on("submit", function (event) {
    event.preventDefault();
    const form = this;
    var formData = new FormData(form);
    formData.append("action", "create_opportunity");
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      processData: false,
      contentType: false,
      type: "POST",

      dataType: "json",

      cache: false,
      processData: false,
      contentType: false,
      enctype: "multipart/form-data",
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
            window.location.href = `/dashboard/company/opportunity/all`;
          });
        } else {
          displayFormErrors(form, response.data);
        }
      },
      error: function (error) {
        console.error("Error creating opportunity:", error);
      },
    });
  });

  $(".delete-opportunity-form").submit(function (e) {
    e.preventDefault();

    var form = this; // Save form reference
    var formData = new FormData(form);
    formData.append("action", "delete_opportunity");
    //$customSpinner.addClass("d-flex");
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
        $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            if (response.success) {
              Swal.fire({
                title: "The opportunity has been deleted.",
                icon: "success",
                showConfirmButton: false,
                timer: 2000,
              }).then(function () {
                location.reload();
              });
            } else {
              Swal.fire(
                "Error!",
                response.data.message ||
                  "There was an error deleting the opportunity.",
                "error",
              );
            }
          },
          error: function (error) {
            Swal.fire(
              "Error!",
              "There was an error deleting the opportunity. Please try again later.",
              "error",
            );
          },
        });
      }
    });
  });
});
