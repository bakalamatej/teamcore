import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/js/app.js' , 
                'resources/js/events/event-form.js',
                'resources/js/clubs/address-toggle.js',
                'resources/js/shared/table-search.js',
                'resources/js/shared/filter.js',
            ],
            refresh: true,
        }),
    ],
});
