import { consumerData } from './consumer.js';

(function() {
    // Get consumer ID from URL
    const pathSegments = window.location.pathname.split('/');
    const consumerId = pathSegments[pathSegments.length - 1];

    // Find the consumer from data
    const consumer = consumerData.find(c => c.cust_id == consumerId);

    if (consumer) {
        // Update consumer information card
        document.getElementById('consumer-id').textContent = consumer.cust_id;
        document.getElementById('consumer-name').textContent = consumer.name;
        document.getElementById('consumer-address').textContent = consumer.address;

        // Update meter & billing card
        document.getElementById('consumer-meter').textContent = consumer.meter_no;
        document.getElementById('consumer-rate').textContent = consumer.rate;
        document.getElementById('consumer-bill').textContent = consumer.total_bill;

        // Update account status card
        document.getElementById('consumer-status').textContent = consumer.status;
        document.getElementById('consumer-ledger').textContent = consumer.ledger_balance;
        document.getElementById('consumer-updated').textContent = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

        // Update page title
        document.title = `${consumer.name} - Consumer Details`;

        // Store consumer data globally for tab access
        window.currentConsumer = consumer;
    } else {
        console.error(`Consumer with ID ${consumerId} not found`);
        document.getElementById('consumer-name').textContent = 'Consumer not found';
    }

    // Tab switching functionality
    window.switchTab = function(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active styling from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });

        // Show selected tab content
        const contentId = `${tabName}-content`;
        const contentElement = document.getElementById(contentId);
        if (contentElement) {
            contentElement.classList.remove('hidden');
        }

        // Highlight selected tab button
        const tabButton = document.getElementById(`tab-${tabName}`);
        if (tabButton) {
            tabButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            tabButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        }

        // Load ledger data if ledger tab is selected
        if (tabName === 'ledger') {
            console.log('Ledger tab activated for consumer:', consumer.cust_id);
        }
    };
})();
