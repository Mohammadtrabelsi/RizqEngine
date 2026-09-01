<script src="{{ asset('js/jquery-mask-money.js') }}"></script>

<span id="money-mask-config"
      class="d-none"
      data-prefix="{{ settings()->currency->symbol }}"
      data-thousands="{{ settings()->currency->thousand_separator }}"
      data-decimal="{{ settings()->currency->decimal_separator }}"></span>

@vite('resources/js/money-mask.js')
