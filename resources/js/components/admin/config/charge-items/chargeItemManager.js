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
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        throw new Error(data.message || 'Validation failed');
                    }
                    throw new Error(data.message || 'Failed to create charge item');
                }

                this.showSuccessNotification('Charge item created successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to create charge item:', error);
                this.showErrorNotification(error.message || 'Failed to create charge item');
            }
        },

        async updateChargeItem() {
            this.errors = {};

            if (!this.selectedItem || !this.selectedItem.charge_item_id) {
                console.error('No charge item selected for update');
                this.showErrorNotification('No charge item selected for update');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.charge_item_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        throw new Error(data.message || 'Validation failed');
                    }
                    throw new Error(data.message || 'Failed to update charge item');
                }

                this.showSuccessNotification('Charge item updated successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to update charge item:', error);
                this.showErrorNotification(error.message || 'Failed to update charge item');
            }
        },

        async deleteChargeItem() {
            if (!this.selectedItem || !this.selectedItem.charge_item_id) {
                console.error('No charge item selected for deletion');
                this.showErrorNotification('No charge item selected for deletion');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.charge_item_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete charge item');
                }

                this.showSuccessNotification('Charge item deleted successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to delete charge item:', error);
                this.showErrorNotification(error.message || 'Failed to delete charge item');
            }
        },
    };
}

window.chargeItemManager = chargeItemManager;
