// resources/js/components/admin/config/puroks/purokManager.js
import configTable from '../shared/configTable.js';

export default function purokManager() {
    return {
        ...configTable('/config/puroks'),

        barangays: [],
        barangayFilter: '',

        async init() {
            await this.loadBarangays();
            await this.fetchItems();
        },

        async loadBarangays() {
            try {
                const response = await fetch('/barangays/list?all=true', {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Failed to load barangays');

                const data = await response.json();
                this.barangays = data.data || [];
            } catch (error) {
                console.error('Failed to load barangays:', error);
            }
        },

        async fetchItems() {
            this.loading = true;
            this.errors = {};

            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.statusFilter,
                    barangay_id: this.barangayFilter,
                    page: this.pagination.currentPage,
                    per_page: this.pagination.perPage,
                });

                const response = await fetch(`${this.apiUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Failed to fetch puroks');

                const data = await response.json();
                this.items = data.data || [];
                this.updatePagination(data.meta);
            } catch (error) {
                console.error('Failed to fetch puroks:', error);
                this.showNotification('Failed to load puroks', 'error');
            } finally {
                this.loading = false;
            }
        },

        async createPurok() {
            this.errors = {};

            try {
                const response = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to create purok');
                }

                this.showNotification('Purok created successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to create purok:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async updatePurok() {
            this.errors = {};

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.p_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to update purok');
                }

                this.showNotification('Purok updated successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to update purok:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async deletePurok() {
            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.p_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete purok');
                }

                this.showNotification('Purok deleted successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to delete purok:', error);
                this.showNotification(error.message, 'error');
            }
        },
    };
}

window.purokManager = purokManager;
