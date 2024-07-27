<style>
        #wrapper {
  position: relative;
  width: 100%;
}

.file_drag_area {
  width: 100%;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  vertical-align: center;
  min-height: 250px;
  background: #fafafa;
  border-radius: 10px;
  border: 3px dashed #ddd;
}

.file_drag_over {
  border-color: #000;
}

.preview-container {
  margin-top: 10px;
}

.preview-container img {
  max-width: 100%;
  max-height: 100px;
  margin: 5px;
}

.loading-bar {
  width: 100%;
  height: 20px;
  background: #dfe6e9;
  border-radius: 5px;
  position: relative;
  display: none; /* Ocultar barra por defecto */
}

.progress-bar {
  height: 100%;
  background: #a29bfe;
  border-radius: 5px;
  position: absolute;
  left: 0;
  top: 0;
  width: 0%; /* Inicialmente 0% */
  transition: width 0.4s ease;
}

.status {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
  font-size: 0.9em;
  letter-spacing: 1pt;
  opacity: 0;
  transition: opacity 0.5s ease;
  position: absolute;
  top: 10000px;
}

.status.visible {
  position: static;
  opacity: 1; /* Mostrar cuando tenga la clase .visible */
}

.status .state {
  float: left;
  width: 100px;
  text-align: center;
}

.status .percentage {
  float: right;
  width: 100px;
  text-align: center;
}

@keyframes fadeInOut {
  0% {
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

    </style>
<div class="col-md-12">
    <div id="wrapper">
        <div class="file_drag_area">
            <span><strong>Add Images</strong> to give more information or reference</span>
            <div id="preview" class="preview-container row mt-4"></div>
            <div class="loading-bar" id="loadingBar">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <div class="status">
                <div class="state" id="stateText">Loading</div>
                <div class="percentage" id="percentageText">0%</div>
            </div>
        </div>
        <input type="file" id="images" name="images[]" accept="image/*" multiple style="display:none;">
    </div>

    </div>
    <script>
        jQuery(document).on("ready",function(){

      
        var fileInput = jQuery("#images")[0];
  var preview = jQuery("#preview");
  var progressBar = jQuery(".progress-bar");
  var loadingBar = jQuery(".loading-bar");
  var statusContainer = jQuery(".status");
  var stateText = jQuery(".status .state");
  var percentageText = jQuery(".status .percentage");

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

    jQuery.each(files, function (index, file) {
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

  jQuery(".file_drag_area").on("dragover", function () {
    jQuery(this).addClass("file_drag_over");
    return false;
  });

  jQuery(".file_drag_area").on("dragleave", function () {
    jQuery(this).removeClass("file_drag_over");
    return false;
  });

  jQuery(".file_drag_area").on("drop", function (e) {
    e.preventDefault();
    jQuery(this).removeClass("file_drag_over");

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

  jQuery(".file_drag_area").on("click", function () {
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
    </script>
