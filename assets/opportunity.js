jQuery(document).ready(function ($) {
  // Initialize components for each file type
  const imageUploader = new CustomMediaUpload("#select-image-button");
  const textUploader = new CustomMediaUpload("#select-text-button");
  var $customSpinner = $(".custom-spinner");

  $("#opportunity-form").validate({
    // Specify client-side validation rules
    rules: {
      price: {
        required: true,
        number: true,
      },
      commission: {
        required: true,
        number: true,
      },
      title: {
        required: true,
      },
      sales_cycle_estimation: {
        required: true,
        number: true,
      },
    },
    // Specify client-side validation error messages
    messages: {
      title: {
        required: "Title is a required field.",
      },
      price: {
        required: "Price is a required field.",
        number: "Price must be a number.",
      },
      commission: {
        required: "Commission is a required field.",
        number: "Commission must be a number.",
      },
      sales_cycle_estimation: {
        required: "Sales Cycle Estimation is a required field.",
        number: "Sales Cycle Estimation must be a number.",
      },
    },
    // Handler to submit the form when valid
    submitHandler: function (form) {
      var formData = new FormData(form);
      formData.append("action", "create_opportunity");

      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          console.log(response);
          if (response.success) {
            // Show success message and redirect
            Swal.fire({
              title: "You have successfully created an opportunity!",
              text: "You will be redirected in two seconds to the opportunities.",
              icon: "success",
              showConfirmButton: false,
              timer: 2000,
            }).then(function () {
              //window.location.href = "/dashboard/company/opportunity/list";
            });
          } else {
            // Show server errors if any
            if (response.data) {
              // Iterate over server errors and display them

              console.log(response);
            }
          }
        },
        error: function (error) {
          console.error("Error creating opportunity:", error);
        },
        complete: function () {
          $customSpinner.removeClass("d-flex");
        },
      });
    },
  });

  $(".delete-opportunity-form").submit(function (e) {
    e.preventDefault();

    var form = this; // Save form reference
    var formData = new FormData(form);
    formData.append("action", "delete_opportunity");
    $customSpinner.addClass("d-flex");
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
              Swal.fire(
                "Deleted!",
                "The opportunity has been deleted.",
                "success",
              );
              // Remove corresponding row from DOM
              $(
                'form.delete-opportunity-form input[value="' +
                  formData.get("opportunity_id") +
                  '"]',
              )
                .closest("tr")
                .remove();
              $customSpinner.removeClass("d-flex");
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
