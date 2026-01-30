import configTable from '../shared/configTable.js';

/**
 * Barangay Manager - extends configTable with barangay-specific operations
 */
export default function barangayManager() {
    return {
        ...configTable('/config/barangays'),

        // Create barangay
        async createBarangay() {
            this.errors = {};

            try {
                const response = await fetch('/config/barangays', {
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
                    throw new Error(data.message || 'Failed to create barangay');
                }

                this.showSuccessNotification('Barangay created successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Create barangay error:', error);
                this.showErrorNotification(error.message || 'Failed to create barangay');
            }
        },

        // Update barangay
        async updateBarangay() {
            this.errors = {};

            try {
                const response = await fetch(`/config/barangays/${this.selectedItem.b_id}`, {
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
                    throw new Error(data.message || 'Failed to update barangay');
                }

                this.showSuccessNotification('Barangay updated successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Update barangay error:', error);
                this.showErrorNotification(error.message || 'Failed to update barangay');
            }
        },

        // Delete barangay
        async deleteBarangay() {
            try {
                const response = await fetch(`/config/barangays/${this.selectedItem.b_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete barangay');
                }

                this.showSuccessNotification('Barangay deleted successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Delete barangay error:', error);
                this.showErrorNotification(error.message || 'Failed to delete barangay');
            }
        },
    };
}

// Make it available globally for Alpine.js
window.barangayManager = barangayManager;
