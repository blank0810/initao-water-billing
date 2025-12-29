// Network Detection and Auto-redirect
(function() {
    let isOnline = navigator.onLine;
    
    function handleNetworkChange() {
        const newStatus = navigator.onLine;
        
        if (!newStatus && isOnline) {
            // Just went offline
            setTimeout(() => {
                if (!navigator.onLine) {
                    window.location.href = '/no-internet-found';
                }
            }, 3000); // Wait 3 seconds to confirm offline status
        }
        
        isOnline = newStatus;
    }
    
    // Listen for network changes
    window.addEventListener('online', handleNetworkChange);
    window.addEventListener('offline', handleNetworkChange);
    
    // Check network on failed requests
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args).catch(error => {
            if (!navigator.onLine) {
                window.location.href = '/no-internet-found';
            }
            throw error;
        });
    };
})();
