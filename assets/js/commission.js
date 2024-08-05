jQuery(document).ready(function ($) {
  var $customSpinner = $(".custom-spinner");

  function addRow() {
    // Variables
    var templateRow, newRow, element;
    var s, t;

    // Find the template row
    templateRow = $(".row_to_clone").first();
    if (templateRow.length === 0) return false;

    // Clone the template row
    newRow = templateRow.clone();

    // Update the names of the inputs
    newRow.find("input, textarea").each(function () {
      element = $(this);
      s = element.attr("name");
      if (s) {
        t = s.split("[]");
        if (t.length > 1) {
          s = t[0] + "[" + $(".row_to_clone").length + "]";
          element.attr("name", s);
        }
      }
      // Clear the value of the input/textarea
      element.val("");
    });

    // Add the new row to the table
    $("#data-table tbody").append(newRow);
    return true;
  }

  function calculateSum() {
    var sum = 0;
    // Iterate through each price and quantity to calculate the total sum
    $("#data-table .price").each(function () {
      var price = parseFloat($(this).val()) || 0;
      var quantity =
        parseFloat($(this).closest("tr").find(".quantity").val()) || 0;
      sum += price * quantity;
    });

    // Update the sum in the DOM
    $("#sum").html(sum.toFixed(2));
  }

  // Function to validate fields
  function validateFields() {
    let isValid = true;

    // Iterate over each item to check for validation
    $(".commission-request-items .item").each(function () {
      const price = $(this).find("input[name='price[]']");
      const quantity = $(this).find("input[name='quantity[]']");

      if (price.val() === "") {
        price.next(".error-message").show();
        isValid = false;
      } else {
        price.next(".error-message").hide();
      }

      if (quantity.val() === "") {
        quantity.next(".error-message").show();
        isValid = false;
      } else {
        quantity.next(".error-message").hide();
      }
    });

    return isValid;
  }

  // Function to remove row and recalculate sum
  function removeRow(e) {
    e.preventDefault();
    $(this).closest("tr").remove();
    calculateSum();
  }

  // Event bindings
  $(".addRow").on("click", function (e) {
    e.preventDefault();
    addRow();
  });

  $("#data-table").on("input", ".price, .quantity", function () {
    calculateSum();
  });

  $("#data-table").on("click", ".removeRow", removeRow);

  $("#modal-dispute").on("show.bs.modal", function (e) {
    // Obtén el botón que abrió el modal desde el evento relatedTarget
    const button = $(e.relatedTarget);

    console.log(button);

    // Obtén el valor del data-attribute del botón
    const commissionRequestId = button.data("commission-request");

    console.log(commissionRequestId);

    if (commissionRequestId !== undefined) {
      $("#commission_request_select").val(commissionRequestId).change();
      $("#commission_request_select").attr("disabled", true);
    } else {
      $("#commission_request_select").val("").change();
      $("#commission_request_select").attr("disabled", false);
    }
    $("#commission_request_id").val(commissionRequestId);
  });

  $("#commission_request_select").on("change", function (e) {
    const commissionRequestId = $("#commission_request_select").val();

    $("#commission_request_id").val(commissionRequestId);
  });

  // Form submission handling
  $("#commission-form").on("submit", function (e) {
    e.preventDefault();

    if (!validateFields()) {
      return;
    }

    var form = this;
    var formData = new FormData(form);

    formData.append("action", "create_commission_request");
    $customSpinner.addClass("d-flex");
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log(response);
        $customSpinner.removeClass("d-flex").hide();
        if (response.success) {
          Swal.fire({
            title: "Commission request created successfully!",
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
        console.error("Error:", error);
        alert("An error occurred while creating the commission request.");
      },
      completed: function () {},
    });
  });

  $(".delete-commission-request-form").submit(function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);
    formData.append("action", "delete_commission_request");

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
          data: formData,
          processData: false,
          contentType: false,
          cache: false,

          success: function (response) {
            console.log(response);
            $customSpinner.removeClass("d-flex").hide();
            if (response.success) {
              Swal.fire({
                title: "The commission request has been deleted.",
                icon: "success",
                showConfirmButton: false,
                timer: 2000,
              }).then(function () {
                location.reload();
              });
            } else {
              Swal.fire(
                "Error!",
                response.data.message ||
                  "There was an error deleting the commission request.",
                "error",
              );
            }
          },
          error: function (error) {
            Swal.fire(
              "Error!",
              "There was an error deleting the commission request. Please try again later.",
              "error",
            );
          },
          completed: function () {
            $customSpinner.removeClass("d-flex").hide();
          },
        });
      }
    });
  });
});
