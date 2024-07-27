jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");
  // Function to validate the commission request form
  function validateCommissionForm() {
    let isValid = true;
    let hasValidRow = false;

    $(".commission-request-items .item").each(function () {
      let price = $(this).find("input[name='price[]']").val();
      let quantity = $(this).find("input[name='quantity[]']").val();
      let priceError = $(this).find(".error-message-price");
      let quantityError = $(this).find(".error-message-quantity");

      if (price === "") {
        priceError.show();
        isValid = false;
      } else {
        priceError.hide();
      }

      if (quantity === "") {
        quantityError.show();
        isValid = false;
      } else {
        quantityError.hide();
      }

      if (price !== "" && quantity !== "") {
        hasValidRow = true;
      }
    });

    if (!hasValidRow) {
      $(".detail-error").text(
        "Please add at least one row with filled price and quantity.",
      );
      isValid = false;
    }

    return isValid;
  }

  // Handler for adding new item
  $(".add-new-item").click(function (e) {
    e.preventDefault();
    let isValid = true;

    // Validate fields of the last item
    $(".commission-request-items .item")
      .last()
      .find("input")
      .each(function () {
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
            <li class="item list-group-item position-relative">
              <div class="row">
                <div class="col-6 col-md-2">
                  <label for="price" class="form-label">Price:</label>
                  <input type="number" step="0.01" name="price[]" class="form-control" placeholder="Price">
                  <small class="text-danger error-message">Please fill out the price field.</small>
                </div>
                <div class="col-6 col-md-2">
                  <label for="quantity" class="form-label">Quantity:</label>
                  <input type="number" name="quantity[]" class="form-control" placeholder="Quantity">
                  <small class="text-danger error-message">Please fill out the quantity field.</small>
                </div>
                <div class="col-6 col-md-2">
                  <label for="invoice" class="form-label">Invoice:</label>
                  <input type="file" name="invoice[]" class="form-control">
                  <small class="text-danger error-message">Please upload the invoice file.</small>
                </div>
                <div class="col-6 col-md-4">
                    <label for="detail" class="form-label">Detail:</label>
                    <textarea id="detail" name="detail[]"></textarea>
                </div>
                <div class="col-6 col-md-2">
                  <label for="subtotal" class="form-label">Subtotal:</label>
                  <span class="subtotal">0.00</span>
                </div>
              </div>
              <span class="position-absolute top-0 end-0 remove-item"><i class="fa fa-trash" aria-hidden="true"></i></span>
              </li>`;
      $(".commission-request-items").append(newItemRow);
    }
  });

  // Function to remove item row
  $(document).on("click", ".remove-item", function () {
    $(this).closest(".item").remove();
    calculateTotal();
  });

  // Function to calculate subtotal and total
  $(document).on(
    "input",
    ".commission-request-items input[name='price[]'], .commission-request-items input[name='quantity[]']",
    function () {
      const row = $(this).closest(".item");
      const price = parseFloat(row.find("input[name='price[]']").val()) || 0;
      const quantity =
        parseFloat(row.find("input[name='quantity[]']").val()) || 0;
      const subtotal = price * quantity;
      row.find(".subtotal").text(subtotal.toFixed(2));
      calculateTotal();
    },
  );

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

    const contract_id = $(this).data("contractId");

    if (contract_id) {
      $("#commission-form #hidden_contract_id").val(contract_id);
    } else {
      $("#contract-select").show();
    }
  });
  $("#contract-select").on("change", function (e) {
    $("#commission-form #hidden_contract_id").val(e.target.value);
  });

  $("#commission-form").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Validate the form before submitting
    if (!validateCommissionForm()) {
      return;
    }

    var form = this; // Get the native form element
    var formData = new FormData(form); // Create a new FormData object

    formData.append("action", "create_commission_request"); // Append action for AJAX handler
    $customSpinner.addClass("d-flex");
    $.ajax({
      url: ajax_object.ajax_url, // AJAX URL
      type: "POST",
      data: formData,
      processData: false, // Prevent jQuery from automatically transforming the data into a query string
      contentType: false, // Prevent jQuery from setting the content-type header
      success: function (response) {
        if (response.success) {
          Swal.fire({
            title: "You have successfully created a commission request!",
            text: "You will be redirected in two seconds...",
            icon: "success",
            showConfirmButton: false,
            timer: 2000,
          }).then(function () {
            location.reload();
          });
        } else {
           // Display errors using the utility function
           if (response.errors) {
            displayFormErrors(form, response.errors);
            } else {
                console.log(response);
            }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error); // Log any errors
        alert("An error occurred while creating the commission request.");
      },
      complete: function () {
        $customSpinner.hide();
      },
    });
  });
});
