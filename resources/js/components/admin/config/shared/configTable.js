/**
 * Reusable Alpine.js utility for config tables
 * Provides common functionality: search, filters, pagination, modals
 */
export default function configTable(fetchUrl) {
    return {
        // API URL
        apiUrl: fetchUrl,

        // Data
        items: [],
        search: '',
        statusFilter: '',
        loading: false,

        // Pagination state
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 15,
            total: 0,
            from: 0,
            to: 0,
        },

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
                    page: this.pagination.currentPage,
                    per_page: this.pagination.perPage,
                });

                const response = await fetch(`${fetchUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                this.items = data.data || [];
                this.updatePagination(data.meta);

            } catch (error) {
                console.error('Failed to fetch items:', error);
                this.showErrorNotification('Failed to load data');
            } finally {
                this.loading = false;
            }
        },

        // Update pagination data
        updatePagination(meta) {
            if (meta) {
                this.pagination = {
                    currentPage: meta.current_page || 1,
                    lastPage: meta.last_page || 1,
                    perPage: meta.per_page || 15,
                    total: meta.total || 0,
                    from: meta.from || 0,
                    to: meta.to || 0,
                };
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

        // Helper methods to fetch item and open modal
        async viewItem(id) {
            try {
                const response = await fetch(`${this.apiUrl}/${id}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const result = await response.json();
                this.openViewModal(result.data);
            } catch (error) {
                console.error('Failed to fetch item:', error);
                this.showErrorNotification('Failed to load item details');
            }
        },

        async editItem(id) {
            try {
                const response = await fetch(`${this.apiUrl}/${id}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const result = await response.json();
                this.openEditModal(result.data);
            } catch (error) {
                console.error('Failed to fetch item:', error);
                this.showErrorNotification('Failed to load item details');
            }
        },

        async deleteItem(id) {
            try {
                const response = await fetch(`${this.apiUrl}/${id}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const result = await response.json();
                this.openDeleteModal(result.data);
            } catch (error) {
                console.error('Failed to fetch item:', error);
                this.showErrorNotification('Failed to load item details');
            }
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

        // Generic notification method for backward compatibility
        showNotification(message, type = 'success') {
            if (type === 'success') {
                this.showSuccessNotification(message);
            } else {
                this.showErrorNotification(message);
            }
        },

        // Pagination
        goToPage(page) {
            if (page >= 1 && page <= this.pagination.lastPage) {
                this.pagination.currentPage = page;
                this.fetchItems();
            }
        },

        // Get CSRF token
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content;
        },
    };
}
