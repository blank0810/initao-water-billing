// resources/js/components/admin/config/account-types/accountTypeManager.js
import configTable from '../shared/configTable.js';

export default function accountTypeManager() {
    return {
        ...configTable('/config/account-types'),

        async createAccountType() {
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
                    throw new Error(data.message || 'Failed to create account type');
                }

                this.showSuccessNotification('Account type created successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to create account type:', error);
                this.showErrorNotification(error.message || 'Failed to create account type');
            }
        },

        async updateAccountType() {
            this.errors = {};

            if (!this.selectedItem || !this.selectedItem.at_id) {
                console.error('No account type selected for update');
                this.showErrorNotification('No account type selected for update');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.at_id}`, {
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
                    throw new Error(data.message || 'Failed to update account type');
                }

                this.showSuccessNotification('Account type updated successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to update account type:', error);
                this.showErrorNotification(error.message || 'Failed to update account type');
            }
        },

        async deleteAccountType() {
            if (!this.selectedItem || !this.selectedItem.at_id) {
                console.error('No account type selected for deletion');
                this.showErrorNotification('No account type selected for deletion');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.at_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete account type');
                }

                this.showSuccessNotification('Account type deleted successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to delete account type:', error);
                this.showErrorNotification(error.message || 'Failed to delete account type');
            }
        },
    };
}

window.accountTypeManager = accountTypeManager;
