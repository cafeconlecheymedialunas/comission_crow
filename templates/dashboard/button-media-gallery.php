
<div class="col-md-6">
    <label for="media-gallery" class="form-label">Select Media</label>
    <button type="button" class="button" id="media-gallery">Select Media</button>
    <input type="hidden" id="media-gallery-ids" name="media_gallery_ids">
    <div id="media-gallery-preview"></div>
</div>
<script>
    jQuery(document).ready(function(){

   
    var mediaFrame;

jQuery('#media-gallery').on('click', function (e) {
    e.preventDefault();

    // Si la biblioteca ya está abierta, no hacer nada
    if (mediaFrame) {
        mediaFrame.open();
        return;
    }

    // Crear un nuevo marco de medios
    mediaFrame = wp.media({
        title: 'Select Media',
        button: {
            text: 'Use Media'
        },
        multiple: true // Permitir selección múltiple
    });

    // Cuando se seleccionan archivos
    mediaFrame.on('select', function () {
        var selection = mediaFrame.state().get('selection');
        var ids = [];
        var previews = '';

        selection.each(function (attachment) {
            var attachmentId = attachment.id;
            var attachmentUrl = attachment.attributes.url;
            var attachmentTitle = attachment.attributes.title;

            ids.push(attachmentId);
            previews += '<div class="media-preview">' +
                            '<img src="' + attachmentUrl + '" alt="' + attachmentTitle + '" style="max-width: 100px; height: auto;" />' +
                            '<p>' + attachmentTitle + '</p>' +
                        '</div>';
        });

        jQuery('#media-gallery-ids').val(ids.join(','));
        jQuery('#media-gallery-preview').html(previews);
    });

    // Abrir el marco de medios
    mediaFrame.open();
});
})
</script>

<style>
    .media-preview {
    display: inline-block;
    margin: 10px;
}
.media-preview img {
    display: block;
    margin-bottom: 5px;
}
</style>