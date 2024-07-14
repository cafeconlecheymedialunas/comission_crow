jQuery(document).ready(function ($) {
  $(".custom-select").select2();
  $(".custom-select-multiple").select2({
    dropdownAutoWidth: true,
    multiple: true,
    width: "100%",
    height: "30px",
    placeholder: "Select a option",
    allowClear: true,
  });
  class CustomMediaUpload {
    constructor(selector) {
      this.selector = selector;
      this.init();
    }

    init() {
      $(document).on("click", this.selector, () => {
        const mediaType = $(this.selector).data("media-type");
        const isMultiple =
          $(this.selector).data("multiple") === true ||
          $(this.selector).data("multiple") === "true";
        const $mediaIdsField = $(this.selector).siblings(".media-ids");
        const $previewContainer = $(this.selector).siblings(
          `.${mediaType}-preview`,
        );

        const mediaUploader = wp.media({
          multiple: isMultiple,
          library: { type: mediaType },
        });

        mediaUploader.on("select", () => {
          this.handleMediaSelection(
            mediaUploader.state().get("selection").toJSON(),
            $mediaIdsField,
            $previewContainer,
            isMultiple,
          );
        });

        mediaUploader.open();
      });
    }

    handleMediaSelection(
      attachments,
      $mediaIdsField,
      $previewContainer,
      isMultiple,
    ) {
      if (!attachments || attachments.length === 0) {
        console.error("No files selected.");
        return;
      }

      const attachmentIds = attachments
        .map((attachment) => attachment.id)
        .join(",");

      if (!isMultiple) {
        // Si es selección única, tomar solo el primer archivo seleccionado
        const attachment = attachments[0];
        this.showPreview(attachment, $mediaIdsField, $previewContainer);
      } else {
        // Si es selección múltiple, mostrar previsualizaciones para todos los archivos seleccionados
        attachments.forEach((attachment) => {
          this.showPreview(attachment, $mediaIdsField, $previewContainer);
        });
      }

      $mediaIdsField.val(attachmentIds);
    }

    showPreview(attachment, $mediaIdsField, $previewContainer) {
      const mediaType = attachment.type;
      const mediaId = attachment.id;

      if (mediaType === "image") {
        const imageUrl = attachment.url;
        $previewContainer
          .find("#image-preview-container")
          .append(
            `<img src="${imageUrl}" style="max-width: 100%; height: auto;">`,
          );
      } else if (mediaType === "text") {
        console.log(attachment.title);
        const textContent = attachment.url; // Modificar según la propiedad que deseas mostrar
        $previewContainer.find("#text-preview").text(attachment.title);
      } else {
        console.error("Unsupported media type.");
      }

      $previewContainer.show();
    }
  }

  // Inicialización de los componentes para cada tipo de archivo
  const imageUploader = new CustomMediaUpload("#select-image-button");
  const textUploader = new CustomMediaUpload("#select-text-button");

  $("#opportunity-form").submit(function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append("action", "create_opportunity");

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          Swal.fire({
            title: "You have successfully registered!",
            text: "You will be redirected in a few seconds to the login page.",
            icon: "success",
            showConfirmButton: false,
            timer: 2000, // 2 segundos
          }).then(function () {
            window.location.href = "/auth/?action=login";
          });
        } else {
          displayFirstError(response.data);
        }
      },
      error: function (error) {
        console.error("Error creating opportunity:", error);
      },
    });
  });

  function displayFirstError(errors) {
    // Iterar sobre cada campo en el objeto de errores
    Object.keys(errors).forEach(function (field) {
      // Obtener el primer mensaje de error para el campo actual
      var errorMessage = errors[field][0]; // El primer error de cada campo

      console.log(field);
      // Mostrar el mensaje de error en algún elemento HTML debajo del campo correspondiente
      // Por ejemplo, supongamos que cada campo tiene un elemento con un ID como 'field-error'
      var errorElement = document.getElementById(field + "-error");
      if (errorElement) {
        errorElement.innerHTML = errorMessage;
        errorElement.style.display = "block"; // Mostrar el mensaje de error
      }
    });
  }

  $(".add-new-url").click(function (e) {
    e.preventDefault();

    const lastUrlField = $('.url-videos input[name="videos[]"]').last();
    const errorMessage = lastUrlField.next(".error-message");
    const urlPattern = /^(https?:\/\/)?([a-z\d-]+\.)+[a-z]{2,6}(\/[^\s]*)?$/i;

    if (lastUrlField.val() === "") {
      errorMessage
        .text("Please fill out the URL field before adding another.")
        .show();
      return;
    }

    if (!urlPattern.test(lastUrlField.val())) {
      errorMessage.text("Please enter a valid URL.").show();
      return;
    }

    errorMessage.hide(); // Hide error message if the field is filled and is a valid URL

    const newUrlRow = `
        <div class="row mb-3">
            <div class="col-sm-10 my-auto">
                <input type="url" name="videos[]" class="form-control" placeholder="Video URL">
                <small class="text-danger error-message" style="display: none;">Please fill out the URL field before adding another.</small>
            </div>
            <div class="col-sm-2">
                <i class="fas fa-trash remove-url"></i>
            </div>
        </div>
    `;
    $(".url-videos").append(newUrlRow);
  });

  $(document).on("click", ".remove-url", function () {
    $(this).closest(".row").remove();
  });
});
