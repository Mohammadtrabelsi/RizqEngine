import './bootstrap.js';
import '@coreui/coreui/dist/js/coreui.bundle.min.js';

const THEME_COOKIE = 'theme';

function readThemeCookie() {
    const match = document.cookie.match(/(?:^|;\s*)theme=(dark|light)/);
    return match ? match[1] : null;
}

function writeThemeCookie(value) {
    // Persist for a year so the server can render the correct theme class on
    // the <body> tag and avoid a flash of the wrong theme on the next load.
    document.cookie = `${THEME_COOKIE}=${value};path=/;max-age=31536000;samesite=lax`;
}

// jQuery is provided globally by the CDN script in the authenticated layout.
// The standalone auth pages load this bundle without jQuery, so guard against
// its absence instead of throwing.
window.jQuery && $(function () {
    $('[data-toggle="tooltip"]').tooltip()

    // Light / dark theme switcher. The initial theme is applied server-side via
    // the body class (see layouts/app.blade.php); here we keep the DOM in sync
    // and handle toggling.
    document.body.classList.toggle('c-dark-theme', readThemeCookie() === 'dark');

    $(document).on('click', '#theme-toggle', function () {
        const isDark = document.body.classList.toggle('c-dark-theme');
        writeThemeCookie(isDark ? 'dark' : 'light');
    });

    // Logout link (replaces the previous inline onclick handler).
    $(document).on('click', '#logout-link', function (e) {
        e.preventDefault();
        document.getElementById('logout-form').submit();
    });
})

// ---------------------------------------------------------------------
// Keep the fixed-header offset in sync with the header's real height.
//
// CoreUI offsets the page content with a hardcoded 104px margin that assumes
// a single-row navbar plus breadcrumb subheader. On small screens the header
// can be taller (or shorter), which left the page content hidden underneath
// the breadcrumb. Measure the header and expose its height as a CSS variable
// (--c-header-height) that the layout uses for the content offset.
// ---------------------------------------------------------------------
(function () {
    function syncHeaderHeight() {
        const header = document.querySelector('.c-header.c-header-fixed');
        if (!header) return;
        document.documentElement.style.setProperty(
            '--c-header-height',
            header.offsetHeight + 'px'
        );
    }

    window.addEventListener('DOMContentLoaded', syncHeaderHeight);
    window.addEventListener('load', syncHeaderHeight);
    window.addEventListener('resize', syncHeaderHeight);
    window.addEventListener('orientationchange', syncHeaderHeight);

    // Recalculate when the header size changes for reasons other than a window
    // resize (e.g. the breadcrumb content swapping, fonts loading).
    if (window.ResizeObserver) {
        const observe = function () {
            const header = document.querySelector('.c-header.c-header-fixed');
            if (header) new ResizeObserver(syncHeaderHeight).observe(header);
        };
        if (document.readyState !== 'loading') observe();
        else window.addEventListener('DOMContentLoaded', observe);
    }
})();
