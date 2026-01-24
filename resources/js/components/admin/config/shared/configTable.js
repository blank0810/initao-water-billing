/**
 * Reusable Alpine.js utility for config tables
 * Provides common functionality: search, filters, pagination, modals
 */
export default function configTable(fetchUrl) {
    return {
        // Data
        items: [],
        search: '',
        statusFilter: '',
        currentPage: 1,
        totalPages: 1,
        perPage: 15,
        loading: false,

        // Modal states
        showCreateModal: false,
        showEditModal: false,
        showViewModal: false,
        showDeleteModal: false,

        // Selected item
        selectedItem: null,

        // Form data
        form: {},
        errors: {},

        // Notifications
        showSuccess: false,
        showError: false,
        successMessage: '',
        errorMessage: '',

        // Initialize
        async init() {
            await this.fetchItems();
        },

        // Fetch items from API
        async fetchItems() {
            this.loading = true;

            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.statusFilter,
                    page: this.currentPage,
                    per_page: this.perPage,
                });

                const response = await fetch(`${fetchUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                this.items = data.data || [];
                this.totalPages = data.meta?.last_page || 1;

            } catch (error) {
                console.error('Failed to fetch items:', error);
                this.showErrorNotification('Failed to load data');
            } finally {
                this.loading = false;
            }
        },

        // Modal management
        openCreateModal() {
            this.form = {};
            this.errors = {};
            this.showCreateModal = true;
        },

        openEditModal(item) {
            this.selectedItem = item;
            this.form = { ...item };
            this.errors = {};
            this.showEditModal = true;
        },

        openViewModal(item) {
            this.selectedItem = item;
            this.showViewModal = true;
        },

        openDeleteModal(item) {
            this.selectedItem = item;
            this.showDeleteModal = true;
        },

        closeAllModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showViewModal = false;
            this.showDeleteModal = false;
            this.selectedItem = null;
        },

        // Notifications
        showSuccessNotification(message) {
            this.successMessage = message;
            this.showSuccess = true;
            setTimeout(() => { this.showSuccess = false; }, 5000);
        },

        showErrorNotification(message) {
            this.errorMessage = message;
            this.showError = true;
            setTimeout(() => { this.showError = false; }, 5000);
        },

        // Pagination
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.fetchItems();
            }
        },

        // Get CSRF token
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content;
        },
    };
}
