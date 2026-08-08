import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel([
                'resources/sass/app.scss',
                'resources/sass/errors.scss',
                'resources/js/app.js',
                'resources/js/chart-config.js',
                'resources/js/filepond.js',
                'resources/js/login.js',
            ]),
        ],
        css: {
            preprocessorOptions: {
                scss: {
                    // Silence deprecation warnings emitted by third-party
                    // dependencies (CoreUI / Bootstrap 4), e.g. the legacy
                    // `/` division syntax removed in Dart Sass 2.0.0.
                    quietDeps: true,
                    silenceDeprecations: ['slash-div', 'import'],
                },
            },
        },
        server: {
            cors: {
                origin: [env.APP_URL],
            },
        },
    };
});
