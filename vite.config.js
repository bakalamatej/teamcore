import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/js/app.js' , 
                'resources/js/event-search.js',
                'resources/js/event-update.js',
                'resources/js/event-create.js',
                'resources/js/club-search.js',
            ],
            refresh: true,
        }),
    ],
});
