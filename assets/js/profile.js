jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");
  $("#agent-profile-form").on("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    var form = $(this);
    var formData = new FormData(form[0]); // Usar el primer elemento del formulario
    formData.append("action", "save_agent_profile");

    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();
        console.log(response)
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
      cache: false,
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
      cache: false,
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();
        console.log(response)
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

  $("#update-email-stripe-form").on("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    var form = $(this);
    var formData = new FormData(form[0]); // Usar el primer elemento del formulario
    formData.append("action", "update_stripe_email");

    // Asumiendo que tienes un spinner de carga
    var $customSpinner = $(".custom-spinner");
    $customSpinner.addClass("d-flex");

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      success: function (response) {
        $customSpinner.removeClass("d-flex").hide();
        console.log(response);
        if (response.success) {
          Swal.fire({
            title: "Updated Stripe email successfully!",
            text: "Redirecting to the profile page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000,
          }).then(function () {
            window.location.href = `/dashboard/commercial-agent/deposit/all`;
          });
        } else {
          displayFormErrors(form, response.data); // Asegúrate de que displayFormErrors esté definido para manejar errores
        }
      },
      error: function (error) {
        console.error("Error updating email:", error);
      },
    });
  });
});
