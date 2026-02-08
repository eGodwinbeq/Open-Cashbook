import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Force light mode - prevent dark mode activation
document.addEventListener('DOMContentLoaded', function() {
    // Remove dark class if present and ensure light class is set
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');

    // Prevent any localStorage dark mode settings
    localStorage.removeItem('theme');
    localStorage.setItem('theme', 'light');
});

