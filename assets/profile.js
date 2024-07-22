jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  jQuery(document).ready(function ($) {
    var $customSpinner = $(".custom-spinner");

    jQuery(document).ready(function ($) {
      var $customSpinner = $(".custom-spinner");

      $("#agent-profile-form").validate({
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
          years_of_experience: {
            number: true,
          },
          skills: {
            required: true,
          },
          location: {
            required: true,
          },
          languages: {
            required: true,
          },
          selling_methods: {
            required: true,
          },
          industry: {
            required: true,
          },
          seller_type: {
            required: true,
          },
        },
        // Specify client-side validation error messages
        messages: {},
        // Handler to submit the form when valid
        submitHandler: function (form, event) {
          event.preventDefault(); // Prevent the default form submission
          var formData = new FormData(form);
          formData.append("action", "save_agent_profile");

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
                  title: "Profile updated successfully!",
                  text: "Redirecting to the profile page.",
                  icon: "success",
                  showConfirmButton: false,
                  timer: 2000,
                }).then(function () {
                  window.location.href = "/dashboard/commercial-agent/profile";
                });
              } else {
                console.log(response);
                $("#agent-profile-form .error").removeClass("error");
                $("#agent-profile-form label.error").remove();
                if (response.data) {
                  $.each(response.data, function (field, errorMessage) {
                    var $element = $("#" + field);
                    $element.addClass("error");
                    $element.after(
                      '<label class="error">' + errorMessage + "</label>",
                    );
                  });

                  var $firstErrorField = $("#" + Object.keys(response.data)[0]);
                  if ($firstErrorField.length) {
                    $("html, body").animate(
                      {
                        scrollTop: $firstErrorField.offset().top - 100,
                      },
                      100,
                    );
                  }
                }
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

      $("#company-profile-form").validate({
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
          years_of_experience: {
            number: true,
          },
          skills: {
            required: true,
          },
          location: {
            required: true,
          },
          languages: {
            required: true,
          },
          selling_methods: {
            required: true,
          },
          industry: {
            required: true,
          },
          seller_type: {
            required: true,
          },
        },
        // Specify client-side validation error messages
        messages: {},
        // Handler to submit the form when valid
        submitHandler: function (form, event) {
          event.preventDefault(); // Prevent the default form submission
          var formData = new FormData(form);
          formData.append("action", "save_company_profile");

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
                  title: "Profile updated successfully!",
                  text: "Redirecting to the profile page.",
                  icon: "success",
                  showConfirmButton: false,
                  timer: 2000,
                }).then(function () {
                  window.location.href = "/dashboard/company/profile";
                });
              } else {
                console.log(response);
                $("#company-profile-form .error").removeClass("error");
                $("#company-profile-form label.error").remove();
                if (response.data) {
                  $.each(response.data, function (field, errorMessage) {
                    var $element = $("#" + field);
                    $element.addClass("error");
                    $element.after(
                      '<label class="error">' + errorMessage + "</label>",
                    );
                  });

                  var $firstErrorField = $("#" + Object.keys(response.data)[0]);
                  if ($firstErrorField.length) {
                    $("html, body").animate(
                      {
                        scrollTop: $firstErrorField.offset().top - 100,
                      },
                      100,
                    );
                  }
                }
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

      $("#update-user-data").validate({
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
          formData.append("action", "update_user_data");

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
    });
  });

  /**/
});
