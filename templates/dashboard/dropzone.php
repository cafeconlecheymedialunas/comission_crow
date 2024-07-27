<style>
    .dropzone {
        border: 2px dashed #0087F7;
        border-radius: 5px;
        background: white;
        min-height: 150px;
        padding: 20px;
        box-sizing: border-box;
        text-align: center;
        cursor: pointer;
    }

    .dropzone.dragover {
        background: #eee;
    }

    .dropzone img {
        max-width: 100px;
        margin: 10px;
    }

    .image-preview {
        position: relative;
        display: inline-block;
    }

    .image-preview img {
        display: block;
    }

    .image-preview .delete-button {
        position: absolute;
        top: 5px;
        right: 5px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        cursor: pointer;
    }
</style>

<div class="dropzone" id="dropzone">
    Arrastra y suelta imágenes aquí o haz clic para seleccionar
</div>
<input type="hidden" id="image_urls" name="image_urls">
<div id="preview"></div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('dropzone');
        const preview = document.getElementById('preview');
        const imageUrlsInput = document.getElementById('image_urls');

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        dropzone.addEventListener('click', () => {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.multiple = true;
            fileInput.style.display = 'none';

            fileInput.addEventListener('change', (e) => {
                const files = e.target.files;
                handleFiles(files);
            });

            document.body.appendChild(fileInput);
            fileInput.click();
            document.body.removeChild(fileInput);
        });

        function handleFiles(files) {
            [...files].forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('image-preview');
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    imgContainer.appendChild(img);

                    const deleteButton = document.createElement('button');
                    deleteButton.classList.add('delete-button');
                    deleteButton.innerHTML = '&times;';
                    deleteButton.addEventListener('click', () => {
                        removeImage(imgContainer);
                    });
                    imgContainer.appendChild(deleteButton);

                    preview.appendChild(imgContainer);
                    uploadFile(file, imgContainer);
                };
                reader.readAsDataURL(file);
            });
        }

        function uploadFile(file, imgContainer) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('action', 'upload_image');
            formData.append('security', '<?php echo wp_create_nonce("upload_image_nonce"); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    imgContainer.dataset.url = data.url;
                    const urls = imageUrlsInput.value ? JSON.parse(imageUrlsInput.value) : [];
                    urls.push(data.url);
                    imageUrlsInput.value = JSON.stringify(urls);
                } else {
                    alert('Error al subir la imagen');
                    preview.removeChild(imgContainer);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al subir la imagen');
                preview.removeChild(imgContainer);
            });
        }

        function removeImage(imgContainer) {
            const url = imgContainer.dataset.url;
            const urls = imageUrlsInput.value ? JSON.parse(imageUrlsInput.value) : [];
            const updatedUrls = urls.filter(item => item !== url);
            imageUrlsInput.value = JSON.stringify(updatedUrls);
            preview.removeChild(imgContainer);

            // Enviar solicitud para eliminar la imagen del servidor
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: new URLSearchParams({
                    action: 'delete_image',
                    security: '<?php echo wp_create_nonce("delete_image_nonce"); ?>',
                    url: url
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Error al eliminar la imagen del servidor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la imagen del servidor');
            });
        }
    });
</script>
