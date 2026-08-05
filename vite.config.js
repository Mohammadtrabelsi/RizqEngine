import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel([
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/chart-config.js',
            ]),
        ],
        server: {
            cors: {
                origin: [env.APP_URL],
            },
        },
    };
});
