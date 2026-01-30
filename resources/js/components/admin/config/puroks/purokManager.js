// resources/js/components/admin/config/puroks/purokManager.js
import configTable from '../shared/configTable.js';

export default function purokManager() {
    return {
        ...configTable('/config/puroks'),

        async createPurok() {
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
                    throw new Error(data.message || 'Failed to create purok');
                }

                this.showSuccessNotification('Purok created successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to create purok:', error);
                this.showErrorNotification(error.message || 'Failed to create purok');
            }
        },

        async updatePurok() {
            this.errors = {};

            if (!this.selectedItem || !this.selectedItem.p_id) {
                console.error('No purok selected for update');
                this.showErrorNotification('No purok selected for update');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.p_id}`, {
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
                    throw new Error(data.message || 'Failed to update purok');
                }

                this.showSuccessNotification('Purok updated successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to update purok:', error);
                this.showErrorNotification(error.message || 'Failed to update purok');
            }
        },

        async deletePurok() {
            if (!this.selectedItem || !this.selectedItem.p_id) {
                console.error('No purok selected for deletion');
                this.showErrorNotification('No purok selected for deletion');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.p_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete purok');
                }

                this.showSuccessNotification('Purok deleted successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to delete purok:', error);
                this.showErrorNotification(error.message || 'Failed to delete purok');
            }
        },
    };
}

window.purokManager = purokManager;
