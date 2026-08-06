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
