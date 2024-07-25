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

      console.log(...formData.entries()); // Ver los datos enviados
      console.log(formData.entries());
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
              title: "agreement updated successfully!",
              text: "Redirecting to the agreement page.",
              icon: "success",
              showConfirmButton: false,
              timer: 2000,
            }).then(function () {
              console.log(response);
              // window.location.href = "/dashboard/company/agreement/all";
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
          alert("Agreement status updated successfully.");
          location.reload(); // Recargar la página para reflejar los cambios
        } else {
          alert("Failed to update agreement status: " + response.data);
        }
      },
      error: function () {
        alert("An error occurred while updating the agreement status.");
      },
    });
  });

 


  $(".add-new-item").click(function (e) {
    e.preventDefault();
    let isValid = true;
    // Validate fields
    $(".commission-request-items .item").last().find("input").each(function () {
      if ($(this).val() === "" && $(this).attr("type") !== "file") {
        $(this).next(".error-message").show();
        isValid = false;
      } else {
        $(this).next(".error-message").hide();
      }
    });
    // If valid, add new row
    if (isValid) {
      const newItemRow = `
             <div class="item row mb-3">
        <div class="col-md-3">
          <label for="price" class="form-label">Price:</label>
          <input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price">
          <small class="text-danger error-message">Please fill out the price field.</small>
        </div>
        <div class="col-md-3">
          <label for="quantity" class="form-label">Quantity:</label>
          <input type="number" name="quantity[]" class="form-control" placeholder="Quantity">
          <small class="text-danger error-message">Please fill out the quantity field.</small>
        </div>
        <div class="col-md-3">
          <label for="invoice" class="form-label">Invoice:</label>
          <input type="file" name="invoice[]" class="form-control">
          <small class="text-danger error-message">Please upload the invoice file.</small>
        </div>
        <div class="col-md-3">
          <label for="subtotal" class="form-label">Subtotal:</label>
          <span class="subtotal">0.00</span>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <i class="fa fa-trash-o remove-item" aria-hidden="true"></i>
        </div>
      </div>`;
      $(".commission-request-items").append(newItemRow);
    }
  });

  // Function to remove item row
  $(document).on("click", ".remove-item", function () {
    $(this).closest(".item").remove();
    calculateTotal();
  });

  // Function to calculate subtotal and total
  $(document).on("input", ".commission-request-items input[name='price[]'], .commission-request-items input[name='quantity[]']", function () {
    const row = $(this).closest(".item");
    const price = parseFloat(row.find("input[name='price[]']").val()) || 0;
    const quantity = parseFloat(row.find("input[name='quantity[]']").val()) || 0;
    const subtotal = price * quantity;
    row.find(".subtotal").text(subtotal.toFixed(2));
    calculateTotal();
  });

  // Function to calculate the total
  function calculateTotal() {
    let total = 0;
    $(".commission-request-items .subtotal").each(function () {
      total += parseFloat($(this).text()) || 0;
    });
    $("#total").text(total.toFixed(2));
  }



  $(".commission-request-button ").on("click", function (e) {
    e.preventDefault();

    const agreement_id = $(this).data("agreementId")
    $("#commission-form #hidden_agreement_id").val(agreement_id)
  
  });

    $("#commission-form").on("submit", function (e) {
      e.preventDefault(); // Prevent the default form submission

      var form = this; // Get the native form element
      var formData = new FormData(form); // Create a new FormData object

      formData.append("action", "create_commission_request"); // Append action for AJAX handler

      $.ajax({
          url: ajax_object.ajax_url, // AJAX URL
          type: "POST",
          data: formData,
          processData: false, // Prevent jQuery from automatically transforming the data into a query string
          contentType: false, // Prevent jQuery from setting the content-type header
          success: function (response) {
              if (response.success) {
                Swal.fire({
                  title: "You have successfully created an commission requestre!",
                  text: "You will be redirected in two seconds...",
                  icon: "success",
                  showConfirmButton: false,
                  timer: 2000,
                }).then(function () {
                  location.reload()
                });
              } else {
                  console.log(response)
                 
              }
          },
          error: function (xhr, status, error) {
              console.error("Error:", error); // Log any errors
              alert("An error occurred while creating the commission request.");
          },
      });
  });

});
