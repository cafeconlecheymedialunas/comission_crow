jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  $("#save-agreement").validate({
    // Specify client-side validation rules
    rules: {
      opportunity: {
        required: true,
      },
      commission: {
        required: true,
        number: true,
      },
      minimal_price: {
        required: true,
        number: true,
      },
    },
    // Specify client-side validation error messages
    messages: {},
    // Handler to submit the form when valid
    submitHandler: function (form, event) {
      event.preventDefault(); // Prevent the default form submission
      var formData = new FormData(form);
      formData.append("action", "create_agreement");

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
              title: "agreement updated successfully!",
              text: "Redirecting to the agreement page.",
              icon: "success",
              showConfirmButton: false,
              timer: 2000,
            }).then(function () {
              let key =
                response.data.roles[0] === "commercial_agent"
                  ? "commercial-agent"
                  : "company";
              window.location.href = `/dashboard/${key}/agreement/all`;
            });
          } else {
            console.log(response);
            //$("#update-user-data").validate().showErrors();
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

  $(".update-status-agreement-form").on("submit", function (e) {
    e.preventDefault();

    var form = $(this);
    var data = form.serialize();
    $customSpinner.addClass("d-flex");
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "update_agreement_status",
        security: form.find('input[name="security"]').val(),
        agreement_id: form.find('input[name="agreement_id"]').val(),
        status: form.find('input[name="status"]').val(),
      },
      success: function (response) {
        if (response.success) {
          $customSpinner.removeClass("d-flex").hide();
          Swal.fire({
            title: "agreement updated successfully!",
            text: "Redirecting to the agreement page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000,
          }).then(function () {
            location.reload();
          });
        } else {
          console.log(response);
        }
      },
      error: function (e) {
        console.log(e);
      },
    });
  });
});
