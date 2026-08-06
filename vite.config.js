import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/sass/app.scss',
            'resources/sass/errors.scss',
            'resources/js/app.js',
            'resources/js/chart-config.js',
            'resources/js/login.js',
            'resources/js/filepond.js',
        ]),
    ],
});
