<script>
    document.addEventListener('DOMContentLoaded', function () {
        var taxTypeSelect = document.getElementById('product_tax_type');
        var dbTaxesWrapper = document.getElementById('product_db_taxes_wrapper');
        var totalDisplay = document.getElementById('product-tax-total');
        var checkboxes = dbTaxesWrapper ? dbTaxesWrapper.querySelectorAll('.product-tax-checkbox') : [];

        if (!taxTypeSelect || !dbTaxesWrapper) {
            return;
        }

        function updateTotalDisplay() {
            if (!totalDisplay) {
                return;
            }

            // Taxes compound one after another (19% then 7% is 27.33%, not
            // 26%) rather than summing, matching the server-side calculation.
            var multiplier = 1;
            var any = false;
            checkboxes.forEach(function (checkbox) {
                if (checkbox.checked && checkbox.dataset.type === 'percentage') {
                    multiplier *= 1 + (parseFloat(checkbox.dataset.rate) || 0) / 100;
                    any = true;
                }
            });

            var combined = Math.round((multiplier - 1) * 10000) / 100;
            totalDisplay.textContent = any ? '(' + combined + '%)' : '';
        }

        function toggle() {
            var hasTaxType = taxTypeSelect.value !== '';
            dbTaxesWrapper.style.display = hasTaxType ? '' : 'none';

            checkboxes.forEach(function (checkbox) {
                checkbox.disabled = !hasTaxType;
            });

            updateTotalDisplay();
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateTotalDisplay);
        });

        taxTypeSelect.addEventListener('change', toggle);
        toggle();
    });
</script>
