import './bootstrap';
import './reverb';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.initializeReverb === 'function') {
        window.initializeReverb();
    }
});