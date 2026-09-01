// POS checkout modal money masking (moved out of the inline <script> in
// sale/pos/index.blade.php). The checkout inputs live inside a modal that
// Livewire reveals via the "showCheckoutModal" browser event, so the masks are
// (re)applied each time that fires. Currency settings come from the
// #money-mask-config element rendered by the money-mask-js include.
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

    window.addEventListener('showCheckoutModal', function () {
        $('#checkoutModal').modal('show');

        $('#paid_amount').maskMoney(Object.assign({}, base, { allowZero: false }));
        $('#total_amount').maskMoney(Object.assign({}, base, { allowZero: true }));

        $('#paid_amount').maskMoney('mask');
        $('#total_amount').maskMoney('mask');

        const form = document.getElementById('checkout-form');
        if (form && !form.__posBound) {
            form.__posBound = true;
            $(form).submit(function () {
                $('#paid_amount').val($('#paid_amount').maskMoney('unmasked')[0]);
                $('#total_amount').val($('#total_amount').maskMoney('unmasked')[0]);
            });
        }
    });
});
