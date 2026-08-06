// Login form behaviour (moved out of the inline <script> in auth/login.blade.php).
document.addEventListener('DOMContentLoaded', function () {
    const login = document.getElementById('login');
    const submit = document.getElementById('submit');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const spinner = document.getElementById('spinner');

    if (!login) {
        return;
    }

    login.addEventListener('submit', function () {
        submit.disabled = true;
        email.readonly = true;
        password.readonly = true;

        spinner.style.display = 'block';

        login.submit();
    });

    setTimeout(function () {
        submit.disabled = false;
        email.readonly = false;
        password.readonly = false;

        spinner.style.display = 'none';
    }, 3000);
});
