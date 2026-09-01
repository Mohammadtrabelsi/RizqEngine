<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<span id="product-dropzone-config"
      class="d-none"
      data-upload-url="{{ route('dropzone.upload') }}"
      data-delete-url="{{ route('dropzone.delete') }}"
      @isset($product) data-existing="{{ json_encode($product->getMedia('images')) }}" @endisset></span>

@vite('resources/js/product-dropzone.js')
