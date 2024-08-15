jQuery(document).ready(function ($) {
  $(".custom-select").select2({
    placeholder: "Select an option",
    allowClear: true,
    theme: "bootstrap-5",
  });
  $(".custom-select-multiple").select2({
    dropdownAutoWidth: true,
    multiple: true,
    width: "100%",
    height: "30px",
    placeholder: "Select an option",
    allowClear: true,
    theme: "bootstrap-5",
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



  
});
