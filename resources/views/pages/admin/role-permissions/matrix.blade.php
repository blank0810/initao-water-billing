<x-app-layout>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-full mx-auto flex flex-col space-y-6">

                    <!-- Header Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="mb-4 lg:mb-0">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 flex items-center gap-3">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                    <i class="fas fa-th text-indigo-600 dark:text-indigo-400 text-xl"></i>
                                </div>
                                Role-Permission Matrix
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-300">
                                Manage permission assignments across all roles at a glance
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.roles.index') }}"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                                <i class="fas fa-user-shield mr-2"></i>Manage Roles
                            </a>
                            <button onclick="saveAllChanges()" id="saveChangesBtn"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap items-center gap-6 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-green-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span class="text-gray-600 dark:text-gray-400">Permission Granted</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600"></div>
                                <span class="text-gray-600 dark:text-gray-400">Permission Not Granted</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-yellow-400 flex items-center justify-center">
                                    <i class="fas fa-infinity text-white text-xs"></i>
                                </div>
                                <span class="text-gray-600 dark:text-gray-400">Super Admin (All Access)</span>
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <span id="changesIndicator" class="hidden px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 rounded text-xs font-medium">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Unsaved changes
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Matrix Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="sticky left-0 z-20 bg-gray-100 dark:bg-gray-700 px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-64 border-r border-gray-200 dark:border-gray-600">
                                            Permission
                                        </th>
                                        @foreach($roles as $role)
                                        <th class="px-4 py-4 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-28">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="whitespace-nowrap">{{ ucwords(str_replace('_', ' ', $role->role_name)) }}</span>
                                                @if($role->role_name === 'super_admin')
                                                    <span class="text-yellow-600 dark:text-yellow-400 text-[10px] normal-case">(All Access)</span>
                                                @endif
                                            </div>
                                        </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($permissionsGrouped as $module => $permissions)
                                        <!-- Module Header Row -->
                                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                                            <td colspan="{{ count($roles) + 1 }}" class="px-6 py-3">
                                                <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    <i class="fas fa-folder text-blue-500"></i>
                                                    {{ ucfirst($module) }}
                                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ count($permissions) }} permissions)</span>
                                                </div>
                                            </td>
                                        </tr>

                                        @foreach($permissions as $permissionName)
                                            @php $permission = $allPermissions[$permissionName] ?? null; @endphp
                                            @if($permission)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                <td class="sticky left-0 z-10 bg-white dark:bg-gray-800 px-6 py-3 border-r border-gray-200 dark:border-gray-700">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $permission->permission_name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $permission->description ?? '' }}</div>
                                                </td>
                                                @foreach($roles as $role)
                                                    <td class="px-4 py-3 text-center">
                                                        @if($role->role_name === 'super_admin')
                                                            <div class="w-6 h-6 mx-auto rounded bg-yellow-400 flex items-center justify-center" title="Super Admin has all permissions">
                                                                <i class="fas fa-infinity text-white text-xs"></i>
                                                            </div>
                                                        @else
                                                            <label class="inline-flex items-center cursor-pointer">
                                                                <input type="checkbox"
                                                                    class="permission-checkbox sr-only peer"
                                                                    data-role-id="{{ $role->role_id }}"
                                                                    data-role-name="{{ $role->role_name }}"
                                                                    data-permission-name="{{ $permission->permission_name }}"
                                                                    {{ $role->permissions->contains('permission_name', $permission->permission_name) ? 'checked' : '' }}
                                                                    onchange="handlePermissionToggle(this)">
                                                                <div class="w-6 h-6 rounded border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 peer-checked:bg-green-500 peer-checked:border-green-500 flex items-center justify-center transition-colors">
                                                                    <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                                </div>
                                                            </label>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="matrixAlertContainer" class="fixed top-4 right-4 z-[60] space-y-2"></div>

    @vite('resources/js/data/admin/role-permission-matrix.js')
</x-app-layout>
