// resources/js/components/admin/config/charge-items/chargeItemManager.js
import configTable from '../shared/configTable.js';

export default function chargeItemManager() {
    return {
        ...configTable('/config/charge-items'),

        chargeTypeFilter: '',

        async fetchItems() {
            this.loading = true;
            this.errors = {};

            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.statusFilter,
                    charge_type: this.chargeTypeFilter,
                    page: this.pagination.currentPage,
                    per_page: this.pagination.perPage,
                });

                const response = await fetch(`${this.apiUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Failed to fetch charge items');

                const data = await response.json();
                this.items = data.data || [];
                this.updatePagination(data.meta);
            } catch (error) {
                console.error('Failed to fetch charge items:', error);
                this.showNotification('Failed to load charge items', 'error');
            } finally {
                this.loading = false;
            }
        },

        async createChargeItem() {
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
                    throw new Error(data.message || 'Failed to create charge item');
                }

                this.showNotification('Charge item created successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to create charge item:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async updateChargeItem() {
            this.errors = {};

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.charge_item_id}`, {
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
                    throw new Error(data.message || 'Failed to update charge item');
                }

                this.showNotification('Charge item updated successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to update charge item:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async deleteChargeItem() {
            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.charge_item_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete charge item');
                }

                this.showNotification('Charge item deleted successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to delete charge item:', error);
                this.showNotification(error.message, 'error');
            }
        },
    };
}

window.chargeItemManager = chargeItemManager;
