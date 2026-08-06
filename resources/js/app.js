import './bootstrap.js';
import '@coreui/coreui/dist/js/coreui.bundle.min.js';

$(function () {
    $('[data-toggle="tooltip"]').tooltip()

    // Light / dark theme switcher
    function applyStoredTheme() {
        var isDark = false;
        try {
            isDark = localStorage.getItem('theme') === 'dark';
        } catch (e) {}
        document.body.classList.toggle('c-dark-theme', isDark);
    }

    applyStoredTheme();

    $(document).on('click', '#theme-toggle', function () {
        var isDark = document.body.classList.toggle('c-dark-theme');
        try {
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (e) {}
    });
})

