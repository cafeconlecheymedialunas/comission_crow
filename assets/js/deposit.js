jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  $("#withdraw-funds-form").on("submit", function (e) {
    e.preventDefault();
    const form = this;
    var formData = new FormData(form);
    formData.append("action", "withdraw_funds");

    $customSpinner.addClass("d-flex");
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: formData,

      cache: false,
      processData: false,
      contentType: false,

      success: function (response) {
        console.log(response);
        if (response.success) {
          Swal.fire({
            title: "Your withdrawal request was successfully completed.",
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
});
