import configTable from '../shared/configTable.js';

/**
 * Area Manager - extends configTable with area-specific operations
 */
export default function areaManager() {
    return {
        ...configTable('/config/areas'),

        // Create area
        async createArea() {
            this.errors = {};

            try {
                const response = await fetch('/config/areas', {
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
                    throw new Error(data.message || 'Failed to create area');
                }

                this.showSuccessNotification('Area created successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Create area error:', error);
                this.showErrorNotification(error.message || 'Failed to create area');
            }
        },

        // Update area
        async updateArea() {
            this.errors = {};

            try {
                const response = await fetch(`/config/areas/${this.selectedItem.a_id}`, {
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
                    throw new Error(data.message || 'Failed to update area');
                }

                this.showSuccessNotification('Area updated successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Update area error:', error);
                this.showErrorNotification(error.message || 'Failed to update area');
            }
        },

        // Delete area
        async deleteArea() {
            try {
                const response = await fetch(`/config/areas/${this.selectedItem.a_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete area');
                }

                this.showSuccessNotification('Area deleted successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Delete area error:', error);
                this.showErrorNotification(error.message || 'Failed to delete area');
            }
        },
    };
}

// Make it available globally for Alpine.js
window.areaManager = areaManager;
