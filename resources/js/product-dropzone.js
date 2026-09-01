// Product image Dropzone (moved out of the inline <script> in the product
// create/edit views). Endpoints come from the #product-dropzone-config data-*
// attributes; the CSRF token from the page <meta>; and any already-attached
// images from the config element's data-existing JSON (edit screen only).
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Dropzone === 'undefined') {
        return;
    }

    const config = document.getElementById('product-dropzone-config');
    const element = document.getElementById('document-dropzone');

    if (!config || !element) {
        return;
    }

    Dropzone.autoDiscover = false;

    const $ = window.jQuery;
    const token = document.querySelector('meta[name="csrf-token"]');
    const csrf = token ? token.getAttribute('content') : '';
    const uploadedDocumentMap = {};

    new Dropzone(element, {
        url: config.dataset.uploadUrl,
        maxFilesize: 1,
        acceptedFiles: '.jpg, .jpeg, .png',
        maxFiles: 3,
        addRemoveLinks: true,
        dictRemoveFile: "<i class='bi bi-x-circle text-danger'></i> remove",
        headers: {
            'X-CSRF-TOKEN': csrf,
        },
        success: function (file, response) {
            $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">');
            uploadedDocumentMap[file.name] = response.name;
        },
        removedfile: function (file) {
            file.previewElement.remove();
            const name = typeof file.file_name !== 'undefined' ? file.file_name : uploadedDocumentMap[file.name];
            $.ajax({
                type: 'POST',
                url: config.dataset.deleteUrl,
                data: {
                    _token: csrf,
                    file_name: '' + name,
                },
            });
            $('form').find('input[name="document[]"][value="' + name + '"]').remove();
        },
        init: function () {
            const raw = config.dataset.existing;
            if (!raw) {
                return;
            }
            const files = JSON.parse(raw);
            for (const i in files) {
                const file = files[i];
                this.options.addedfile.call(this, file);
                this.options.thumbnail.call(this, file, file.original_url);
                file.previewElement.classList.add('dz-complete');
                $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">');
            }
        },
    });
});
