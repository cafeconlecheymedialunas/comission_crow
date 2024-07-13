jQuery(document).ready(function ($) {
  $(".custom-select").select2();
  $("#agent-profile-form").on("submit", function (event) {
    console.log(event);
    event.preventDefault(); // Evita el envío normal del formulario

    // Datos del formulario
    var formData = $(this).serialize();
    console.log(formData);

    // Mostrar carga o spinner mientras se procesa
    $("#profile-update-message").html(
      '<div class="alert alert-info" role="alert">Updating profile...</div>',
    );

    // Envío AJAX
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url, // Variable definida por WordPress que apunta a admin-ajax.php
      data: formData + "&action=update_agent_profile",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Éxito: Mostrar mensaje de éxito y limpiar formulario si necesario
          $("#profile-update-message").html(
            '<div class="alert alert-success" role="alert">' +
              response.data +
              "</div>",
          );
          // Limpiar formulario o realizar otras acciones después de actualizar
        } else {
          console.log(response);
          // Error: Mostrar mensaje de error
          $("#profile-update-message").html(
            '<div class="alert alert-danger" role="alert">' +
              response.data +
              "</div>",
          );
        }
      },
      error: function (error) {
        console.log(error);
        // Manejo de errores en caso de falla en la solicitud AJAX
        $("#profile-update-message").html(
          '<div class="alert alert-danger" role="alert">Error updating profile. Please try again later.</div>',
        );
      },
    });
  });

  $("#opportunity-form").submit(function (e) {
    e.preventDefault();

    var formData = $(this).serialize();
    formData += "&action=create_opportunity";

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: formData,
      success: function (response) {
        // Manejar la respuesta después de crear la oportunidad
        alert("Opportunity created successfully!");
        console.log(response);
      },
      error: function (error) {
        console.error("Error creating opportunity:", error);
      },
    });
  });
  $(document).on("click", ".upload-image-button", function (e) {
    e.preventDefault();

    var field = $(this).closest(".media-gallery-field");
    var input = field.find('input[name="image_ids"]');
    var imagePreview = field.find(".image-preview");
    var button = $(this);

    var media = wp
      .media({
        title: "Upload Image",
        library: { type: "image" }, // Permitir solo imágenes
        multiple: true, // Permitir múltiples imágenes
      })
      .open()
      .on("select", function () {
        var selectedImages = media.state().get("selection").toJSON();
        var imageIds = selectedImages.map(function (image) {
          return image.id;
        });
        input.val(imageIds.join(",")); // Separar IDs por coma para múltiples imágenes

        if (selectedImages.length > 0) {
          imagePreview.attr("src", selectedImages[0].url).show(); // Mostrar solo la primera imagen seleccionada
          button.text("Change Image");
          imagePreview.show();
        } else {
          imagePreview.hide();
          button.text("Upload/Add Image");
        }
      });
  });

  $("#add-video-url-button").click(function (e) {
    e.preventDefault();
    $("#video-urls-container").append(
      '<div class="video-url-field"><input type="text" name="video_urls[]" class="form-control" placeholder="Enter video URL"><button type="button" class="remove-video-url-button button">Remove</button></div>',
    );
  });

  // Manejar el clic en el botón "Remove"
  $(document).on("click", ".remove-video-url-button", function (e) {
    e.preventDefault();
    $(this).closest(".video-url-field").remove();
  });
});
