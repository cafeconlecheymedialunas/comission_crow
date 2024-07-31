jQuery(document).ready(function ($) {
  const id = $("#commission-request-body").data("id");
  if (!id) return;
  console.log(id);
  $("#preloader").show();
  $.ajax({
    url: ajax_object.ajax_url,
    method: "POST",
    data: {
      action: "calculate_total",
      commission_request_id: id,
    },
    success: function (response) {
      if (response.success) {
        var total = response.data.total;

        // Almacenar el total en un campo oculto o en una variable para usarlo al crear el PaymentIntent
        $("#wpfs-custom-amount-unique--NDUzYmM").val(total); // Convertir a centavos
        $("#wpfs-custom-input--NDUzYmM--0").val(id);
      } else {
        console.error("Error al calcular el total:", response.data.message);
      }
      $("#preloader").hide();
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud AJAX:", error);
    },
  });
});
