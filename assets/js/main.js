jQuery(document).ready(function ($) {
 
  




    var tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function (tooltip) {
        new bootstrap.Tooltip(tooltip);
    });


  $(".editor-container").each(function () {
    // Get the related hidden field ID from the data-target attribute
    var targetId = $(this).data("target");
    var $inputHidden = $("#" + targetId);

    // Initialize Quill
    var quill = new Quill(this, {
      theme: "snow",
    });

    // Get initial content from hidden input and paste into Quill
    var content = $inputHidden.val();
    quill.clipboard.dangerouslyPasteHTML(content);

    // Handle changes in Quill and update hidden input
    quill.on("text-change", function () {
      var html = quill.root.innerHTML;
      $inputHidden.val(html);
    });
  });

  window.onscroll = function() {stickyMenu()};

  var menu = document.getElementById("dashboard-header");
  var sticky = menu.offsetTop;

  function stickyMenu() {
      if (window.pageYOffset > sticky) {
          menu.classList.add("sticky");
      } else {
          menu.classList.remove("sticky");
      }
  }


// Selecciona todos los <select> con el atributo "multiple" en la página
var multipleSelects = $("select[multiple]");

// Verifica si hay elementos seleccionados
if (multipleSelects.length) {
  // Itera sobre cada elemento <select> encontrado
  multipleSelects.each(function() {

    $(this).select2({
      theme: "bootstrap-5",
      width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
      placeholder: $( this ).data( 'placeholder' ),
      closeOnSelect: false,
  
  });
  });
}



  
});
