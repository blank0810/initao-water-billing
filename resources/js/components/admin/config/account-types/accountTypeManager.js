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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to create account type');
                }

                this.showNotification('Account type created successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to create account type:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async updateAccountType() {
            this.errors = {};

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.at_id}`, {
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
                    throw new Error(data.message || 'Failed to update account type');
                }

                this.showNotification('Account type updated successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to update account type:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async deleteAccountType() {
            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.at_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete account type');
                }

                this.showNotification('Account type deleted successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to delete account type:', error);
                this.showNotification(error.message, 'error');
            }
        },
    };
}

window.accountTypeManager = accountTypeManager;
