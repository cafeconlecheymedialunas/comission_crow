class CustomMediaUpload {
  constructor(selector) {
    this.selector = selector;
    this.init();
  }

  init() {
    jQuery(document).on("click", this.selector, () => {
      const mediaType = jQuery(this.selector).data("media-type");
      const isMultiple =
        jQuery(this.selector).data("multiple") === true ||
        jQuery(this.selector).data("multiple") === "true";
      const $mediaIdsField = jQuery(this.selector).siblings(".media-ids");
      const $previewContainer = jQuery(this.selector).siblings(
        `.${mediaType}-preview`,
      );
      const type =
        mediaType === "text"
          ? [
              "application/pdf",
              "application/msword",
              "text/plain",
              "text/html",
              "text/csv",
              "text/xml",
            ]
          : ["image"];
      const mediaUploader = wp.media({
        multiple: isMultiple,
        library: {
          type: type,
        },
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
      // If single selection, take only the first selected file
      const attachment = attachments[0];
      this.showPreview(attachment, $mediaIdsField, $previewContainer);
    } else {
      $previewContainer.html("");
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
      $previewContainer.html(
        `<div class="col-2 col-sm-3 col-md-4 preview-item d-flex flex-column justify-content-center align-items-center">
              <img width="100" src="${imageUrl}" style="max-width: 100%; height: auto;">
            </div>`,
      );
    } else if (mediaType === "text") {
      const content = `<div class="col-2 col-sm-3 col-md-4 preview-item d-flex flex-column justify-content-center align-items-center">
                            <img width="50" src="${attachment.icon}" style="max-width: 100%; height: auto;">
                            <span>${attachment.title}</span>
                          </div>`;
      $previewContainer.append(content);
    } else {
      console.error("Unsupported media type.");
    }

    $previewContainer.addClass("d-flex");
  }
}
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

  function displayFormErrors(form, errors) {
    // Clear previous error messages
    $(form).find(".error-message").remove();

    // Iterate over the errors and display them next to the respective fields
    $.each(errors, function (fieldName, errorMessage) {
        var field = $(form).find('[name="' + fieldName + '"]');
        if (field.length) {
            var errorElement = $('<div class="error-message text-danger"></div>').text(errorMessage);
            field.after(errorElement);
        }
    });
}
});
