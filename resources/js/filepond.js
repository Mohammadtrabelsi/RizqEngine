// FilePond initialisation (moved out of the inline <script> in
// includes/filepond-js.blade.php). Server-provided values (upload/delete
// endpoints and the CSRF token) are read from data-* attributes on the
// #filepond-config element so no server data is embedded in this bundle.
document.addEventListener('DOMContentLoaded', function () {
    const fileElement = document.querySelector('input[id="image"]');
    const config = document.getElementById('filepond-config');

    if (!fileElement || !config || typeof FilePond === 'undefined') {
        return;
    }

    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType
    );

    FilePond.create(fileElement, {
        acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
    });

    FilePond.setOptions({
        server: {
            process: config.dataset.processUrl,
            revert: config.dataset.revertUrl,
            headers: {
                'X-CSRF-TOKEN': config.dataset.csrfToken,
            },
        },
    });
});
