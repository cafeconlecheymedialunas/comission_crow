jQuery(document).ready(function ($) {
  
        var $customSpinner = $(".custom-spinner");

  
        $("#save-deal").validate({
          // Specify client-side validation rules
          rules: {
            first_name: {
              required: true,
            },
            last_name: {
              required: true,
            },
            user_email: {
              required: true,
              email: true,
            },
          },
          // Specify client-side validation error messages
          messages: {},
          // Handler to submit the form when valid
          submitHandler: function (form, event) {
            event.preventDefault(); // Prevent the default form submission
            var formData = new FormData(form);
            formData.append("action", "save_deal");
  
            console.log(...formData.entries()); // Ver los datos enviados
  
            $.ajax({
              type: "POST",
              url: ajax_object.ajax_url,
              data: formData,
              processData: false,
              contentType: false,
              success: function (response) {
                if (response.success) {
                  console.log(response);
                  Swal.fire({
                    title: "User updated successfully!",
                    text: "Redirecting to the profile page.",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 2000,
                  }).then(function () {
                    window.location.href = "/dashboard/company/profile";
                  });
                } else {
                  console.log(response)
                  $("#update-user-data").validate().showErrors();
                }
              },
              error: function (error) {
                console.error("Error updating profile:", error);
              },
              complete: function () {
                $customSpinner.removeClass("d-flex");
              },
            });
          },
        });
  
    /**/
  });
  