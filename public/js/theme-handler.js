/**
 * Theme Handler for Livewire Navigation
 * Handles dark/light mode switching with Livewire wire:navigate
 */
document.addEventListener('livewire:navigated', function() {
    // Re-apply theme after Livewire navigation
    var theme = new URLSearchParams(window.location.search).get('theme') || localStorage.getItem('tablerTheme') || 'light';
    
    if (theme === 'dark') {
        document.body.setAttribute('data-bs-theme', 'dark');
        localStorage.setItem('tablerTheme', 'dark');
    } else {
        document.body.removeAttribute('data-bs-theme');
        localStorage.setItem('tablerTheme', 'light');
    }
});