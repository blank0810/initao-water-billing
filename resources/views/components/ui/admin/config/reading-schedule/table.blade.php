<table class="w-full">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Area</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reader</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scheduled Dates</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <template x-if="items.length === 0">
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-4"></i>
                    <p>No reading schedules found</p>
                </td>
            </tr>
        </template>

        <template x-for="schedule in items" :key="schedule.schedule_id">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="schedule.period_name"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="schedule.area_name"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="schedule.reader_name"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="schedule.scheduled_start_date + ' - ' + schedule.scheduled_end_date"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <template x-if="schedule.total_meters > 0">
                        <div>
                            <div class="w-24 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2 rounded-full" :style="'width: ' + (schedule.completion_percentage || 0) + '%'"></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="schedule.meters_read + '/' + schedule.total_meters"></span>
                        </div>
                    </template>
                    <template x-if="!schedule.total_meters || schedule.total_meters === 0">
                        <span class="text-xs text-gray-400">N/A</span>
                    </template>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getStatusClass(schedule.status)" x-text="getStatusLabel(schedule.status)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end gap-2">
                        <!-- View (always) -->
                        <button @click="openViewModal(schedule)" class="p-2 text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors" title="View Details">
                            <i class="fas fa-eye text-sm"></i>
                        </button>

                        <!-- Pending actions -->
                        <template x-if="schedule.status === 'pending'">
                            <div class="flex items-center gap-1">
                                <button @click="startSchedule(schedule)" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" title="Start">
                                    <i class="fas fa-play text-sm"></i>
                                </button>
                                <button @click="downloadSchedule(schedule)" class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors" title="Download CSV">
                                    <i class="fas fa-download text-sm"></i>
                                </button>
                                <button @click="openEditScheduleModal(schedule)" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                            </div>
                        </template>

                        <!-- In Progress actions -->
                        <template x-if="schedule.status === 'in_progress'">
                            <div class="flex items-center gap-1">
                                <button @click="downloadSchedule(schedule)" class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors" title="Download CSV">
                                    <i class="fas fa-download text-sm"></i>
                                </button>
                                <button @click="openCompleteScheduleModal(schedule)" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" title="Complete">
                                    <i class="fas fa-check-circle text-sm"></i>
                                </button>
                                <button @click="openDelayScheduleModal(schedule)" class="p-2 text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors" title="Mark Delayed">
                                    <i class="fas fa-exclamation-triangle text-sm"></i>
                                </button>
                            </div>
                        </template>

                        <!-- Delete (non-completed only) -->
                        <template x-if="schedule.status !== 'completed'">
                            <button @click="openDeleteModal(schedule)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </template>
                    </div>
                </td>
            </tr>
        </template>
    </tbody>
</table>
