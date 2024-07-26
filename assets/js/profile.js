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
      $customSpinner.addClass("d-flex");
      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response.success) {
            console.log(response);

            $customSpinner.removeClass("d-flex").hide();
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
          }
        },
        error: function (error) {
          console.error("Error updating profile:", error);
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
      $customSpinner.addClass("d-flex");

      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response.success) {
            console.log(response);
            $customSpinner.removeClass("d-flex").hide();
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
          }
        },
        error: function (error) {
          console.error("Error updating profile:", error);
        },
      });
    },
  });

  $("#update-user-data").validate({
    rules: {
      first_name: { required: true },
      last_name: { required: true },
      user_email: { required: true, email: true },
    },
    messages: {},
    submitHandler: function (form, event) {
      event.preventDefault(); // Prevent the default form submission
      var formData = new FormData(form);
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
              let key =
                response.data.roles[0] === "commercial_agent"
                  ? "commercial-agent"
                  : "company";
              window.location.href = `/dashboard/${key}/profile`;
            });
          } else {
          }
        },
        error: function (error) {
          console.error("Error updating profile:", error);
        },
      });
    },
  });
});
