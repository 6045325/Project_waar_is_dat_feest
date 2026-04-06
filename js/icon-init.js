/**
 * Icon Initializer - Beheerd Lucide icon rendering
 */
class IconInitializer {
    constructor() {
        this.initializeIcons();
        this.observeForNewContent();
    }

    initializeIcons() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    observeForNewContent() {
        // Re-initialize icons when fetch requests complete
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args).then(response => {
                response.clone().json().then(() => {
                    lucide.createIcons();
                }).catch(() => {});
                return response;
            });
        };
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new IconInitializer();
});
