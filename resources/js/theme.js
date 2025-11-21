// Simple, reliable theme management
// The theme is already initialized in app.blade.php head script
// This file just provides the toggle functionality

const STORAGE_KEY = 'theme-preference';
const DARK_CLASS = 'dark';

/**
 * Toggle between light and dark mode
 * Persists the choice to localStorage
 */
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.contains(DARK_CLASS);

    if (isDark) {
        // Switch to light mode
        html.classList.remove(DARK_CLASS);
        localStorage.setItem(STORAGE_KEY, 'light');
    } else {
        // Switch to dark mode
        html.classList.add(DARK_CLASS);
        localStorage.setItem(STORAGE_KEY, 'dark');
    }

    // Dispatch event for components to listen to
    window.dispatchEvent(new CustomEvent('theme-changed', {
        detail: { isDark: !isDark }
    }));

    return !isDark ? 'dark' : 'light';
}

/**
 * Check if dark mode is currently active
 */
function isDarkTheme() {
    return document.documentElement.classList.contains(DARK_CLASS);
}

/**
 * Set theme to a specific mode
 */
function setTheme(isDark) {
    const html = document.documentElement;

    if (isDark) {
        html.classList.add(DARK_CLASS);
        localStorage.setItem(STORAGE_KEY, 'dark');
    } else {
        html.classList.remove(DARK_CLASS);
        localStorage.setItem(STORAGE_KEY, 'light');
    }

    window.dispatchEvent(new CustomEvent('theme-changed', {
        detail: { isDark }
    }));
}

// Make functions available globally
window.toggleTheme = toggleTheme;
window.isDarkTheme = isDarkTheme;
window.setTheme = setTheme;



