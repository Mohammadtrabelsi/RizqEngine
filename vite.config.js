import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel([
                'resources/css/app.css',
                'resources/css/errors.css',
                'resources/js/app.js',
                'resources/js/chart-config.js',
                'resources/js/filepond.js',
                'resources/js/login.js',
            ]),
        ],
        server: {
            cors: {
                origin: [env.APP_URL],
            },
        },
    };
});
