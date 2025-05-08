import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input:
            ['resources/css/app.css',
                'resources/css/e-form.css',
                'resources/js/app.js',
                'resources/js/register.js',
                'resources/js/upload-ktp.js',
                'resources/js/upload-bukti.js',
                'resources/css/sidebar.css',
                'resources/js/timeline.js',
                ],
            refresh: true,
        }),
    ],
});
