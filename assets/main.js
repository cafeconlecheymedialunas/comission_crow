jQuery(document).ready(function ($) {
  // Inicializar Select2 en todos los selectores que tienen la clase "form-control"
  $(".form-control").select2({
    placeholder: "Select an option",
    allowClear: true,
  });
});
