import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/js/app.js' , 
                'resources/js/events/event-update.js',
                'resources/js/events/event-create.js',
                'resources/js/clubs/club-create.js',
                'resources/js/clubs/club-update.js',
                'resources/js/users/user-search.js',
                'resources/js/shared/table-search.js',
            ],
            refresh: true,
        }),
    ],
});
