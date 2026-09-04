import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            tailwindcss(),
            laravel([
                'resources/css/app.css',
                'resources/css/errors.css',
                'resources/js/app.js',
                'resources/js/chart-config.js',
                'resources/js/filepond.js',
                'resources/js/login.js',
                'resources/js/money-mask.js',
                'resources/js/product-dropzone.js',
                'resources/js/pos-checkout.js',
            ]),
        ],
        server: {
            cors: {
                // Allow the configured app URL (when set) plus common local
                // dev origins: Herd/Valet ".test" domains, localhost, and the
                // IPv4/IPv6 loopback addresses Vite may serve assets from.
                origin: [
                    env.APP_URL,
                    /^https?:\/\/(?:.+\.)?test(?::\d+)?$/,
                    /^https?:\/\/(?:.+\.)?localhost(?::\d+)?$/,
                    /^https?:\/\/127\.0\.0\.1(?::\d+)?$/,
                    /^https?:\/\/\[::1\](?::\d+)?$/,
                ].filter(Boolean),
            },
        },
    };
});
