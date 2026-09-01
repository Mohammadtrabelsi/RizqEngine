// Money input masking (moved out of the per-form inline <script> blocks).
//
// Any <input data-money-mask> is masked with the store currency (read from the
// #money-mask-config element's data-* attributes). Add:
//   data-money-allow-zero   to allow a zero value,
//   data-money-prefill      to mask the value already present (edit screens).
// A control with data-money-fill="<selector>" masks that target on click,
// using its data-money-value as the amount (empty = just re-mask). Every form
// that contains a masked input has those inputs un-masked to raw numbers on
// submit so the server receives plain values.
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery;
    const config = document.getElementById('money-mask-config');

    if (!$ || typeof $.fn.maskMoney === 'undefined' || !config) {
        return;
    }

    const base = {
        prefix: config.dataset.prefix || '',
        thousands: config.dataset.thousands || '',
        decimal: config.dataset.decimal || '',
    };

    const masked = document.querySelectorAll('[data-money-mask]');

    masked.forEach(function (input) {
        const options = Object.assign({}, base);
        if (input.hasAttribute('data-money-allow-zero')) {
            options.allowZero = true;
        }
        $(input).maskMoney(options);
        if (input.hasAttribute('data-money-prefill')) {
            $(input).maskMoney('mask');
        }
    });

    document.querySelectorAll('[data-money-fill]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const target = document.querySelector(trigger.getAttribute('data-money-fill'));
            if (!target) {
                return;
            }
            const value = trigger.getAttribute('data-money-value');
            if (value === null || value === '') {
                $(target).maskMoney('mask');
            } else {
                $(target).maskMoney('mask', Number(value));
            }
        });
    });

    const boundForms = new Set();
    masked.forEach(function (input) {
        const form = input.closest('form');
        if (!form || boundForms.has(form)) {
            return;
        }
        boundForms.add(form);
        form.addEventListener('submit', function () {
            form.querySelectorAll('[data-money-mask]').forEach(function (field) {
                field.value = $(field).maskMoney('unmasked')[0];
            });
        });
    });
});
