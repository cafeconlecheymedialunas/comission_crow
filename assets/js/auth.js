jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  $("#kamerpower_registration_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=kamerpower_register_user",
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
          $("#kamerpower_registration_errors").html(response.data);
        }
      },
    });
  });

  $("#kamerpower_login_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=kamerpower_login_user",
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
            let key =
              response.data.roles[0] === "commercial_agent"
                ? "commercial-agent"
                : "company";
            window.location.href = `/dashboard/${key}/profile`;
          });
        } else {
          $("#kamerpower_login_errors").html(response.data);
        }
      },
    });
  });

  $("#kamerpower_reset_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=kamerpower_reset_password",
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
          $("#kamerpower_reset_errors").html(response.data);
        }
      },
    });
  });

  $("#kamerpower_new_password_form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    $customSpinner.addClass("d-flex");
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: form.serialize() + "&action=kamerpower_set_new_password",
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
          $("#kamerpower_new_password_errors").html(response.data);
        }
      },
    });
  });
});
