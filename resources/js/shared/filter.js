// resources/js/filter.js
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filter-form');
    const results = document.getElementById('results');
    
    if (!form || !results) return; 

    let debounceTimer;
    
    form.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const url = form.action + '?' + new URLSearchParams(new FormData(form)).toString();
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                results.innerHTML = html;
            });
        }, 100); // wait 100ms after the last input before sending the request
    });
});