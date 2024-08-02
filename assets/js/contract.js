jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  $("#save-contract").on("submit", function (e) {
   
      event.preventDefault(); 
      const form = this
      var formData = new FormData(form);
      formData.append("action", "create_contract");

      $customSpinner.addClass("d-flex");
      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: formData,
        processData: false,
        contentType: false,
        cache:false,
        success: function (response) {
          if (response.success) {
            console.log(response);
            $customSpinner.removeClass("d-flex").hide();
            Swal.fire({
              title: "contract created successfully!",
              text: "Redirecting to the contract page.",
              icon: "success",
              showConfirmButton: false,
              timer: 2000,
            }).then(function () {
              let key =
                response.data.roles[0] === "commercial_agent"
                  ? "commercial-agent"
                  : "company";
              window.location.href = `/dashboard/${key}/contract/all`;
            });
          } else {
            console.log(response);
            console.log(response)
            displayFormErrors(form, response.data);
          }
        },
        error: function (error) {
          console.log(error);
          console.error("Error updating profile:", error);
        },
        complete: function () {
          $customSpinner.removeClass("d-flex");
        },
      });
    
  });

  $(".update-status-contract-form").on("submit", function (e) {
    e.preventDefault();

    var form = $(this);
    var data = form.serialize();
    $customSpinner.addClass("d-flex");
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "update_contract_status",
        security: form.find('input[name="security"]').val(),
        contract_id: form.find('input[name="contract_id"]').val(),
        status: form.find('input[name="status"]').val(),
      },
      success: function (response) {
        if (response.success) {
          $customSpinner.removeClass("d-flex").hide();
          Swal.fire({
            title: "contract updated successfully!",
            text: "Redirecting to the contract page.",
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
