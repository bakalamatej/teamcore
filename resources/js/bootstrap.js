import axios from 'axios';
window.axios = axios;

// Sets header for AJAX requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token from meta tag
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
