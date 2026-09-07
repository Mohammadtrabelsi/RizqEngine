<script>
    document.addEventListener('DOMContentLoaded', function () {
        var taxTypeSelect = document.getElementById('product_tax_type');
        var dbTaxesWrapper = document.getElementById('product_db_taxes_wrapper');
        var manualInput = document.getElementById('product_order_tax');
        var checkboxes = dbTaxesWrapper ? dbTaxesWrapper.querySelectorAll('.product-tax-checkbox') : [];

        if (!taxTypeSelect || !dbTaxesWrapper || !manualInput) {
            return;
        }

        function recalcFromCheckboxes() {
            var sum = 0;
            checkboxes.forEach(function (checkbox) {
                if (checkbox.checked && checkbox.dataset.type === 'percentage') {
                    sum += parseFloat(checkbox.dataset.rate) || 0;
                }
            });
            manualInput.value = sum;
        }

        function toggle() {
            var isExclusive = taxTypeSelect.value === '1';
            dbTaxesWrapper.style.display = isExclusive ? '' : 'none';

            checkboxes.forEach(function (checkbox) {
                checkbox.disabled = !isExclusive;
            });

            if (isExclusive && Array.prototype.some.call(checkboxes, function (checkbox) { return checkbox.checked; })) {
                recalcFromCheckboxes();
            }
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', recalcFromCheckboxes);
        });

        taxTypeSelect.addEventListener('change', toggle);
        toggle();
    });
</script>
