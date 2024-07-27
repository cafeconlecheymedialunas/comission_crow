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

  var fileInput = $("#images")[0];
  var preview = $("#preview");
  var progressBar = $(".progress-bar");
  var loadingBar = $(".loading-bar");
  var statusContainer = $(".status");
  var stateText = $(".status .state");
  var percentageText = $(".status .percentage");

  function showPreview(files) {
    preview.empty(); // Vaciar la vista previa

    // Determine the column size based on the number of files
    var columnSize;
    if (files.length === 1) {
      columnSize = "col-md-12";
    } else if (files.length === 2) {
      columnSize = "col-md-6";
    } else {
      columnSize = "col-md-4"; // Default for 3 or more images
    }

    $.each(files, function (index, file) {
      var reader = new FileReader();
      reader.onload = function (e) {
        const newRow = `
                <div class="${columnSize}">
                    <img class="img-thumbnail" src="${e.target.result}">
                </div>`;
        preview.append(newRow);
      };
      reader.readAsDataURL(file);
    });
  }

  function simulateUpload(files) {
    // Vaciar la vista previa antes de comenzar una nueva carga
    preview.empty();

    // Mostrar la barra de carga y el estado
    loadingBar.show(); // Mostrar la barra de carga
    progressBar.css("width", "0%"); // Inicializar la barra de progreso en 0%
    stateText.text("Loading");
    percentageText.text("0%");
    statusContainer.addClass("visible"); // Mostrar el estado

    let progress = 0;

    var interval = setInterval(function () {
      progress += 5; // Incrementar el porcentaje
      if (progress > 100) {
        progress = 100;
        clearInterval(interval);
        stateText.text("Complete");
        setTimeout(function () {
          // Ocultar el estado y la barra de carga
          statusContainer.removeClass("visible"); // Ocultar estado
          loadingBar.fadeOut(100); // Desaparecer la barra de carga
          percentageText.fadeOut(100); // Desaparecer el porcentaje

          setTimeout(function () {
            // Mostrar vista previa después de la carga
            showPreview(files);
            // Reiniciar la barra de progreso a 0% para la próxima carga
            progressBar.css("width", "0%");
          }, 100); // Esperar un poco antes de mostrar la vista previa
        }, 100); // Esperar un poco antes de ocultar el estado
      } else {
        // Actualizar la barra de progreso y el porcentaje
        progressBar.css("width", progress + "%");
        percentageText.text(progress + "%");
      }
    }, 50); // Intervalo para simular la carga
  }

  $(".file_drag_area").on("dragover", function () {
    $(this).addClass("file_drag_over");
    return false;
  });

  $(".file_drag_area").on("dragleave", function () {
    $(this).removeClass("file_drag_over");
    return false;
  });

  $(".file_drag_area").on("drop", function (e) {
    e.preventDefault();
    $(this).removeClass("file_drag_over");

    var files_list = e.originalEvent.dataTransfer.files;

    var fileBuffer = Array.from(fileInput.files);

    for (var i = 0; i < files_list.length; i++) {
      fileBuffer.push(files_list[i]);
    }

    var dataTransfer =
      new ClipboardEvent("").clipboardData || new DataTransfer();
    fileBuffer.forEach((file) => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;

    simulateUpload(fileInput.files);
  });

  $(".file_drag_area").on("click", function () {
    fileInput.click();
  });

  fileInput.addEventListener("change", function (e) {
    var files_list = e.target.files;

    var fileBuffer = Array.from(fileInput.files);

    var dataTransfer =
      new ClipboardEvent("").clipboardData || new DataTransfer();
    fileBuffer.forEach((file) => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;

    simulateUpload(fileInput.files);
  });
});
