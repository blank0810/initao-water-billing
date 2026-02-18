import configTable from '../shared/configTable.js';

/**
 * Reading Schedule Manager - extends configTable with schedule-specific operations
 * Uses the existing /reading-schedules API endpoints
 */
export default function readingScheduleManager() {
    return {
        ...configTable('/reading-schedules'),

        // Dropdown data
        areas: [],
        periods: [],
        meterReaders: [],

        // Stats
        stats: {
            total_schedules: 0,
            pending: 0,
            in_progress: 0,
            completed: 0,
            delayed: 0,
        },

        // Extra modal states
        showCompleteModal: false,
        showDelayModal: false,
        delayNotes: '',

        // Status filter for schedules
        scheduleStatusFilter: '',

        // Override init to also load dropdowns
        async init() {
            await Promise.all([
                this.fetchItems(),
                this.loadDropdowns(),
            ]);
        },

        // Override fetchItems - the reading schedule API returns {success, data, stats} not {data, meta, links}
        async fetchItems() {
            this.loading = true;

            try {
                let url = '/reading-schedules';
                if (this.scheduleStatusFilter) {
                    url += `?status=${this.scheduleStatusFilter}`;
                }

                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' },
                });

                const result = await response.json();

                if (result.success) {
                    this.items = result.data || [];
                    if (result.stats) {
                        this.stats = result.stats;
                    }
                } else {
                    this.items = [];
                }
            } catch (error) {
                console.error('Failed to fetch schedules:', error);
                this.showErrorNotification('Failed to load reading schedules');
            } finally {
                this.loading = false;
            }
        },

        // Load dropdown data for create/edit forms
        async loadDropdowns() {
            try {
                const [areasRes, periodsRes, readersRes] = await Promise.all([
                    fetch('/reading-schedules/areas', { headers: { 'Accept': 'application/json' } }),
                    fetch('/reading-schedules/periods', { headers: { 'Accept': 'application/json' } }),
                    fetch('/reading-schedules/meter-readers', { headers: { 'Accept': 'application/json' } }),
                ]);

                const [areasData, periodsData, readersData] = await Promise.all([
                    areasRes.json(),
                    periodsRes.json(),
                    readersRes.json(),
                ]);

                if (areasData.success) this.areas = areasData.data || [];
                if (periodsData.success) this.periods = periodsData.data || [];
                if (readersData.success) this.meterReaders = readersData.data || [];
            } catch (error) {
                console.error('Failed to load dropdowns:', error);
            }
        },

        // Handle area selection - auto-populate reader
        onAreaChange() {
            const area = this.areas.find(a => a.id == this.form.area_id);
            if (area && area.assigned_reader_id) {
                this.form.reader_id = area.assigned_reader_id;
            }
        },

        // Filter by status
        filterByStatus() {
            this.fetchItems();
        },

        // Open create modal with defaults
        openCreateScheduleModal() {
            this.form = {
                period_id: '',
                area_id: '',
                reader_id: '',
                scheduled_start_date: new Date().toISOString().split('T')[0],
                scheduled_end_date: '',
                total_meters: '',
                notes: '',
            };
            this.errors = {};
            this.showCreateModal = true;
        },

        // Open edit modal
        openEditScheduleModal(schedule) {
            this.selectedItem = schedule;
            this.form = {
                reader_id: schedule.reader_id || '',
                scheduled_start_date: schedule.scheduled_start_date || '',
                scheduled_end_date: schedule.scheduled_end_date || '',
                total_meters: schedule.total_meters || '',
                notes: schedule.notes || '',
            };
            this.errors = {};
            this.showEditModal = true;
        },

        // Open complete modal
        openCompleteScheduleModal(schedule) {
            this.selectedItem = schedule;
            this.form = {
                meters_read: schedule.meters_read || '',
                meters_missed: schedule.meters_missed || '',
            };
            this.showCompleteModal = true;
        },

        // Open delay modal
        openDelayScheduleModal(schedule) {
            this.selectedItem = schedule;
            this.delayNotes = '';
            this.showDelayModal = true;
        },

        // Close all modals (override to include extra modals)
        closeAllModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showViewModal = false;
            this.showDeleteModal = false;
            this.showCompleteModal = false;
            this.showDelayModal = false;
            this.selectedItem = null;
            this.delayNotes = '';
        },

        // Create schedule
        async createSchedule() {
            this.errors = {};

            try {
                const response = await fetch('/reading-schedules', {
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
                    throw new Error(data.message || 'Failed to create schedule');
                }

                if (data.success) {
                    this.showSuccessNotification(data.message || 'Schedule created successfully');
                    this.closeAllModals();
                    await this.fetchItems();
                } else {
                    this.showErrorNotification(data.message || 'Failed to create schedule');
                }
            } catch (error) {
                console.error('Create schedule error:', error);
                this.showErrorNotification(error.message || 'Failed to create schedule');
            }
        },

        // Update schedule
        async updateSchedule() {
            this.errors = {};

            if (!this.selectedItem) return;

            try {
                const response = await fetch(`/reading-schedules/${this.selectedItem.schedule_id}`, {
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
                    throw new Error(data.message || 'Failed to update schedule');
                }

                if (data.success) {
                    this.showSuccessNotification(data.message || 'Schedule updated successfully');
                    this.closeAllModals();
                    await this.fetchItems();
                } else {
                    this.showErrorNotification(data.message || 'Failed to update schedule');
                }
            } catch (error) {
                console.error('Update schedule error:', error);
                this.showErrorNotification(error.message || 'Failed to update schedule');
            }
        },

        // Delete schedule
        async deleteSchedule() {
            if (!this.selectedItem) return;

            try {
                const response = await fetch(`/reading-schedules/${this.selectedItem.schedule_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (data.success) {
                    this.showSuccessNotification(data.message || 'Schedule deleted successfully');
                    this.closeAllModals();
                    await this.fetchItems();
                } else {
                    this.showErrorNotification(data.message || 'Failed to delete schedule');
                }
            } catch (error) {
                console.error('Delete schedule error:', error);
                this.showErrorNotification(error.message || 'Failed to delete schedule');
            }
        },

        // Start schedule
        async startSchedule(schedule) {
            try {
                const response = await fetch(`/reading-schedules/${schedule.schedule_id}/start`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (data.success) {
                    this.showSuccessNotification(data.message || 'Schedule started');
                    await this.fetchItems();
                } else {
                    this.showErrorNotification(data.message || 'Failed to start schedule');
                }
            } catch (error) {
                console.error('Start schedule error:', error);
                this.showErrorNotification('Failed to start schedule');
            }
        },

        // Complete schedule
        async completeSchedule() {
            if (!this.selectedItem) return;

            try {
                const response = await fetch(`/reading-schedules/${this.selectedItem.schedule_id}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (data.success) {
                    this.showSuccessNotification(data.message || 'Schedule completed');
                    this.closeAllModals();
                    await this.fetchItems();
                } else {
                    this.showErrorNotification(data.message || 'Failed to complete schedule');
                }
            } catch (error) {
                console.error('Complete schedule error:', error);
                this.showErrorNotification('Failed to complete schedule');
            }
        },

        // Delay schedule
        async delaySchedule() {
            if (!this.selectedItem) return;

            try {
                const response = await fetch(`/reading-schedules/${this.selectedItem.schedule_id}/delay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify({ notes: this.delayNotes }),
                });

                const data = await response.json();

                if (data.success) {
                    this.showSuccessNotification(data.message || 'Schedule marked as delayed');
                    this.closeAllModals();
                    await this.fetchItems();
                } else {
                    this.showErrorNotification(data.message || 'Failed to delay schedule');
                }
            } catch (error) {
                console.error('Delay schedule error:', error);
                this.showErrorNotification('Failed to delay schedule');
            }
        },

        // Download CSV
        downloadSchedule(schedule) {
            window.location.href = `/reading-schedules/${schedule.schedule_id}/download`;
        },

        // Helper: get status badge classes
        getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'delayed': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            };
            return classes[status] || classes['pending'];
        },

        // Helper: format status label
        getStatusLabel(status) {
            return (status || '').replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
        },
    };
}

// Make it available globally for Alpine.js
window.readingScheduleManager = readingScheduleManager;
