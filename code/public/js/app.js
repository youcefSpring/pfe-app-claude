// Bootstrap will be loaded via script tags in HTML
document.addEventListener('DOMContentLoaded', function() {
    if (window.axios) {
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }
});