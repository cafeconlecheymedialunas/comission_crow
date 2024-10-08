jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

 

  $("#create-dispute").on("submit", function (e) {
    e.preventDefault();
    const form = this;
    var formData = new FormData(form);
    formData.append("action", "create_dispute");

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
            title: "The Dispute has been saved.",
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

  $("#open-dispute-modal").on("click", function (e) {
    e.preventDefault();
    
    Swal.fire({
      title: "Are you sure you want to create a dispute?",
      text: "Attempting not to pay salespeople who have made a sale can have legal consequences.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, open modal",
      cancelButtonText: "Cancel"
    }).then((result) => {
      if (result.isConfirmed) {
        $("#modal-dispute").modal('show');
      }
    });
  });
  $(document).on("click", ".delete-dispute-button", function (e) {
    e.preventDefault();

    var disputeId = $(this).data("dispute-id");
    var nonce = $(this).data("nonce");

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
        $customSpinner.addClass("d-flex");
        $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          cache: false,
          data: {
            action: "delete_dispute",
            security: nonce,
            dispute_id: disputeId,
          },
          success: function (response) {
            console.log(response);
            $customSpinner.removeClass("d-flex").hide();
            if (response.success) {
              Swal.fire({
                title: "The dispute has been deleted.",
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
            Swal.fire(
              "Error!",
              "There was an error deleting the dispute. Please try again later.",
              "error",
            );
          },
          complete: function () {
            $customSpinner.removeClass("d-flex").hide();
          },
        });
      }
    });
  });
});