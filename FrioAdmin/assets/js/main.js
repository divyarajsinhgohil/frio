/**
 * FRIO Admin Console - Shared JavaScript Micro-interactions
 * Handles top nav search bar expand transitions, input active styling, etc.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Search input transition effect
    const searchInput = document.querySelector('header input[type="text"]');
    if (searchInput) {
        searchInput.addEventListener('focus', () => {
            searchInput.parentElement.classList.add('scale-[1.02]');
        });
        searchInput.addEventListener('blur', () => {
            searchInput.parentElement.classList.remove('scale-[1.02]');
        });
    }

    // Dynamic row hover effect in lists
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', () => {
            row.classList.add('z-10', 'relative');
        });
        row.addEventListener('mouseleave', () => {
            row.classList.remove('z-10', 'relative');
        });
    });
});
