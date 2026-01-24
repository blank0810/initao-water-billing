import configTable from '../shared/configTable.js';

export default function waterRateManager() {
    return {
        ...configTable('/config/water-rates'),

        // Additional state for water rates
        periodFilter: '',
        periods: [],

        // Override init to load periods
        async init() {
            await this.loadPeriods();
            await this.fetchItems();
        },

        // Load available periods for filtering
        async loadPeriods() {
            try {
                const response = await fetch('/api/periods', {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load periods');
                }

                const data = await response.json();
                this.periods = data.data || [];

                // Populate the period dropdown
                const periodSelect = document.querySelector('select[x-model="periodFilter"]');
                if (periodSelect && this.periods.length > 0) {
                    this.periods.forEach(period => {
                        const option = document.createElement('option');
                        option.value = period.p_id;
                        option.textContent = `${period.p_month} ${period.p_year}`;
                        periodSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading periods:', error);
            }
        },

        // Override fetchItems to handle grouping by account type
        async fetchItems() {
            this.loading = true;
            this.errors = {};

            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.statusFilter,
                    period: this.periodFilter,
                });

                const response = await fetch(`${this.apiUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch water rates');
                }

                const data = await response.json();

                // Group rates by account type
                this.items = data.data || {};
                this.pagination = data.meta || null;
            } catch (error) {
                console.error('Fetch items error:', error);
                this.showErrorNotification(error.message || 'Failed to load water rates');
            } finally {
                this.loading = false;
            }
        },

        // Create new water rate
        async createRate() {
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
                    throw new Error(data.message || 'Failed to create water rate');
                }

                this.showSuccessNotification('Water rate created successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Create rate error:', error);
                this.showErrorNotification(error.message || 'Failed to create water rate');
            }
        },

        // Update existing water rate
        async updateRate() {
            this.errors = {};

            if (!this.selectedItem?.wr_id) {
                this.showErrorNotification('No water rate selected');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.wr_id}`, {
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
                    throw new Error(data.message || 'Failed to update water rate');
                }

                this.showSuccessNotification('Water rate updated successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Update rate error:', error);
                this.showErrorNotification(error.message || 'Failed to update water rate');
            }
        },

        // Delete water rate
        async deleteRate() {
            if (!this.selectedItem?.wr_id) {
                this.showErrorNotification('No water rate selected');
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.wr_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to delete water rate');
                }

                this.showSuccessNotification('Water rate deleted successfully');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Delete rate error:', error);
                this.showErrorNotification(error.message || 'Failed to delete water rate');
            }
        },

        // Override openCreateModal to initialize rate-specific form
        openCreateModal() {
            this.form = {
                wr_tier: '',
                wr_rangemin: '',
                wr_rangemax: '',
                wr_baserate: '',
                wr_incrate: '',
                accounttype: '',
                p_id: this.periodFilter || null,
                stat_id: 1,
            };
            this.errors = {};
            this.showCreateModal = true;
        },

        // Override openEditModal to populate rate-specific form
        openEditModal(item) {
            this.selectedItem = item;
            this.form = {
                wr_tier: item.wr_tier,
                wr_rangemin: item.wr_rangemin,
                wr_rangemax: item.wr_rangemax,
                wr_baserate: item.wr_baserate,
                wr_incrate: item.wr_incrate,
                accounttype: item.accounttype,
                p_id: item.p_id,
                stat_id: item.stat_id,
            };
            this.errors = {};
            this.showEditModal = true;
        },
    };
}

// Make function globally available
window.waterRateManager = waterRateManager;
