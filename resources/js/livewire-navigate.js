/**
 * Re-run Chart.js initializers after Livewire SPA navigation.
 * Fixes charts breaking when wire:navigate swaps page content.
 */
document.addEventListener('livewire:navigated', () => {
    if (typeof window.initAdminDashboardChart === 'function') {
        window.initAdminDashboardChart();
    }
});
