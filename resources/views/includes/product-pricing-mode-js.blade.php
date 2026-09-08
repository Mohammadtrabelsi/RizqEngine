<script>
    document.addEventListener('DOMContentLoaded', function () {
        var $ = window.jQuery;
        var modeInputs = document.querySelectorAll('input[name="pricing_mode"]');
        var costInput = document.getElementById('product_cost');
        var priceInput = document.getElementById('product_price');
        var marginInput = document.getElementById('product_margin');
        var marginWrapper = document.getElementById('product_margin_wrapper');

        if (!modeInputs.length || !costInput || !priceInput || !marginInput || !marginWrapper) {
            return;
        }

        var maskAvailable = $ && typeof $.fn.maskMoney !== 'undefined';

        // Read a money-masked field as a plain number, whether or not the
        // mask plugin is active, tolerating the store's separators.
        function readMoney(input) {
            if (maskAvailable) {
                var unmasked = $(input).maskMoney('unmasked')[0];
                if (unmasked !== undefined && unmasked !== '' && !isNaN(unmasked)) {
                    return parseFloat(unmasked);
                }
            }

            var config = document.getElementById('money-mask-config');
            var raw = input.value || '';
            if (config) {
                var thousands = config.dataset.thousands || '';
                var decimal = config.dataset.decimal || '';
                if (thousands) {
                    raw = raw.split(thousands).join('');
                }
                if (decimal) {
                    raw = raw.split(decimal).join('.');
                }
            }
            raw = raw.replace(/[^0-9.\-]/g, '');
            var value = parseFloat(raw);
            return isNaN(value) ? 0 : value;
        }

        function selectedMode() {
            var checked = document.querySelector('input[name="pricing_mode"]:checked');
            return checked ? checked.value : 'price';
        }

        // Recompute the sale price from cost and margin while in margin mode.
        function recalculatePrice() {
            if (selectedMode() !== 'margin') {
                return;
            }

            var cost = readMoney(costInput);
            var margin = parseFloat(marginInput.value);
            if (isNaN(margin)) {
                margin = 0;
            }

            var price = cost * (1 + margin / 100);
            price = Math.round(price * 100) / 100;

            if (maskAvailable) {
                $(priceInput).maskMoney('mask', price);
            } else {
                priceInput.value = price;
            }
        }

        // In margin mode the sale price is derived, so lock its field and show
        // the margin field; in price mode do the reverse.
        function applyMode() {
            var margin = selectedMode() === 'margin';

            marginWrapper.style.display = margin ? '' : 'none';
            priceInput.readOnly = margin;
            priceInput.classList.toggle('bg-slate-100', margin);
            marginInput.disabled = !margin;

            if (margin) {
                recalculatePrice();
            }
        }

        modeInputs.forEach(function (input) {
            input.addEventListener('change', applyMode);
        });

        costInput.addEventListener('input', recalculatePrice);
        costInput.addEventListener('blur', recalculatePrice);
        marginInput.addEventListener('input', recalculatePrice);

        applyMode();
    });
</script>
