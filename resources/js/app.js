import './bootstrap.js';
import '@coreui/coreui/dist/js/coreui.bundle.min.js';

const THEME_COOKIE = 'theme';

function writeThemeCookie(value) {
    document.cookie = `${THEME_COOKIE}=${value};path=/;max-age=31536000;samesite=lax`;
}

// jQuery is provided globally by the CDN script in the authenticated layout.
// The standalone auth pages load this bundle without jQuery, so guard against
// its absence instead of throwing.
window.jQuery && $(function () {
    $('[data-toggle="tooltip"]').tooltip()

    document.body.classList.remove('c-dark-theme');
    writeThemeCookie('light');

    // Logout link (replaces the previous inline onclick handler).
    $(document).on('click', '#logout-link', function (e) {
        e.preventDefault();
        document.getElementById('logout-form').submit();
    });
})
