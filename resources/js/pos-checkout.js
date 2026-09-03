// POS checkout modal money masking (moved out of the inline <script> in
// sale/pos/index.blade.php). The checkout inputs live inside a modal that the
// Checkout Livewire component reveals by dispatching the "showCheckoutModal"
// event, so the masks are (re)applied each time that fires. Currency settings
// come from the #money-mask-config element rendered by the money-mask-js
// include.
//
// The Checkout component fires this via Livewire's `$this->dispatch(...)`, which
// travels on the Livewire event bus — it is NOT a native browser/window event,
// so it must be caught with `Livewire.on()` rather than
// `window.addEventListener()`. Listening on window (as this file previously did)
// meant the modal never opened and the "Proceed" button appeared to do nothing.
function bindPosCheckoutModal() {
    const $ = window.jQuery;

    // jQuery is required to show the Bootstrap modal at all. Everything else
    // (the money mask, the config element) is a progressive enhancement — the
    // modal must still open so the cashier can complete the sale even when the
    // maskMoney plugin failed to load.
    if (!$) {
        return;
    }

    const config = document.getElementById('money-mask-config');
    const hasMask = typeof $.fn.maskMoney !== 'undefined';

    const base = config
        ? {
            prefix: config.dataset.prefix || '',
            thousands: config.dataset.thousands || '',
            decimal: config.dataset.decimal || '',
        }
        : {};

    Livewire.on('showCheckoutModal', function () {
        $('#checkoutModal').modal('show');

        if (hasMask && config) {
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
        }
    });
}

// Register on livewire:init when Livewire boots after this module, or bind
// immediately when Livewire is already available (this deferred module may load
// after livewire:init has already fired).
if (window.Livewire) {
    bindPosCheckoutModal();
} else {
    document.addEventListener('livewire:init', bindPosCheckoutModal);
}
