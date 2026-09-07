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

            var sum = 0;
            checkboxes.forEach(function (checkbox) {
                if (checkbox.checked && checkbox.dataset.type === 'percentage') {
                    sum += parseFloat(checkbox.dataset.rate) || 0;
                }
            });

            totalDisplay.textContent = sum > 0 ? '(' + sum + '%)' : '';
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
