import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark Mode System - Initialize on page load
(function() {
    const darkMode = localStorage.getItem('darkMode') === 'true';
    const html = document.documentElement;
    
    if (darkMode) {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
})();

Alpine.start();
