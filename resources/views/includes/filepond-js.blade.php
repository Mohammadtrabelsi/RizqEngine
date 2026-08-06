<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>

<span id="filepond-config"
      class="d-none"
      data-process-url="{{ route('filepond.upload') }}"
      data-revert-url="{{ route('filepond.delete') }}"
      data-csrf-token="{{ csrf_token() }}"></span>

@vite('resources/js/filepond.js')
