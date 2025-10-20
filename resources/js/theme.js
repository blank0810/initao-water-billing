// Simplified theme management
console.log('Theme.js loaded!');

// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    console.log('Saved theme:', savedTheme);
    console.log('System prefers dark:', systemPrefersDark);

    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        document.documentElement.classList.add('dark');
        console.log('✅ Applied dark theme');
    } else {
        document.documentElement.classList.remove('dark');
        console.log('✅ Applied light theme');
    }
}

// Toggle theme function
function toggleTheme() {
    const html = document.documentElement;

    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        console.log('🔄 Switched to light theme');
        return 'light';
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        console.log('🔄 Switched to dark theme');
        return 'dark';
    }
}

// Alpine.js component
document.addEventListener('alpine:init', () => {
    console.log('Alpine.js initialized - registering theme component');

    Alpine.data('themeToggle', () => {
        return {
            isDark: false,

            init() {
                // Set initial state
                this.isDark = document.documentElement.classList.contains('dark');
                console.log('Alpine theme initialized, isDark:', this.isDark);

                // Watch for external changes to theme
                this.$watch('isDark', (value) => {
                    console.log('Alpine isDark changed to:', value);
                });
            },

            toggleTheme() {
                const newTheme = window.toggleTheme();
                this.isDark = newTheme === 'dark';
                console.log('Theme toggled via Alpine, isDark:', this.isDark);
            }
        }
    });
});

// Initialize theme when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing theme');
    initTheme();
});
