<footer class="c-footer app-footer">
    <div class="app-footer-inner">
        <div class="app-footer-brand">
            @include('layouts.logo', ['size' => 22, 'label' => 'Triangle POS', 'labelSize' => 14])
            <span class="app-footer-tag">{{ __('app.point-of-sale') ?? 'Point of Sale' }}</span>
        </div>

        <div class="app-footer-meta">
            <span class="app-footer-copy">
                &copy; {{ date('Y') }} Triangle POS. {{ __('app.all-rights-reserved') ?? 'All rights reserved.' }}
            </span>
            <span class="app-footer-sep">•</span>
            <span class="app-footer-credit">
                {{ __('app.developed-by') }}
                <a target="_blank" rel="noopener" href="https://fahimanzam.netlify.app">Fahim Anzam Dip</a>
                / {{ __('app.developed') }} <strong>Mohammad TRABELSI</strong>
            </span>
        </div>

        <div class="app-footer-actions">
            <span class="app-footer-version">
                <i class="bi bi-patch-check-fill"></i> {{ __('app.version') }} <strong>v3.4.0</strong>
            </span>
        </div>
    </div>
</footer>
