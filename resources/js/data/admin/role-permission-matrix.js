/**
 * Role-Permission Matrix JavaScript
 * Handles checkbox toggles and bulk permission updates
 */

(function() {
    'use strict';

    // Track changes
    let pendingChanges = {};
    let originalState = {};

    // Initialize - store original state
    document.addEventListener('DOMContentLoaded', function() {
        storeOriginalState();
        setupCheckboxStyles();
    });

    // Store original checkbox states
    function storeOriginalState() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            const key = `${cb.dataset.roleId}-${cb.dataset.permissionName}`;
            originalState[key] = cb.checked;
        });
    }

    // Setup checkbox visual styles
    function setupCheckboxStyles() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            updateCheckboxVisual(cb);
        });
    }

    // Update checkbox visual appearance
    function updateCheckboxVisual(checkbox) {
        const icon = checkbox.parentElement.querySelector('.fa-check');
        if (checkbox.checked) {
            icon.classList.remove('opacity-0');
            icon.classList.add('opacity-100');
        } else {
            icon.classList.remove('opacity-100');
            icon.classList.add('opacity-0');
        }
    }

    // Handle permission toggle
    window.handlePermissionToggle = function(checkbox) {
        const roleId = checkbox.dataset.roleId;
        const permissionName = checkbox.dataset.permissionName;
        const key = `${roleId}-${permissionName}`;

        // Update visual
        updateCheckboxVisual(checkbox);

        // Track change
        if (checkbox.checked !== originalState[key]) {
            pendingChanges[key] = {
                role_id: roleId,
                permission_name: permissionName,
                enabled: checkbox.checked
            };
        } else {
            delete pendingChanges[key];
        }

        updateSaveButton();
    };

    // Update save button state
    function updateSaveButton() {
        const saveBtn = document.getElementById('saveChangesBtn');
        const indicator = document.getElementById('changesIndicator');
        const hasChanges = Object.keys(pendingChanges).length > 0;

        saveBtn.disabled = !hasChanges;
        if (hasChanges) {
            indicator.classList.remove('hidden');
        } else {
            indicator.classList.add('hidden');
        }
    }

    // Save all changes
    window.saveAllChanges = async function() {
        const changes = Object.values(pendingChanges);
        if (changes.length === 0) return;

        const saveBtn = document.getElementById('saveChangesBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        saveBtn.disabled = true;

        try {
            // Group changes by role for efficiency
            const changesByRole = {};
            changes.forEach(change => {
                if (!changesByRole[change.role_id]) {
                    changesByRole[change.role_id] = [];
                }
                changesByRole[change.role_id].push(change);
            });

            // Process each role's changes
            const promises = Object.entries(changesByRole).map(async ([roleId, roleChanges]) => {
                // Get current permissions for this role
                const checkboxes = document.querySelectorAll(`.permission-checkbox[data-role-id="${roleId}"]`);
                const enabledPermissions = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.dataset.permissionName);

                const response = await fetch(`/admin/role-permissions/${roleId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ permissions: enabledPermissions }),
                });

                return response.json();
            });

            const results = await Promise.all(promises);
            const allSuccessful = results.every(r => r.success);

            if (allSuccessful) {
                showMatrixAlert('success', 'All changes saved successfully');
                // Update original state
                pendingChanges = {};
                storeOriginalState();
                updateSaveButton();
            } else {
                const errors = results.filter(r => !r.success).map(r => r.message);
                showMatrixAlert('error', errors.join(', '));
            }
        } catch (error) {
            console.error('Error:', error);
            showMatrixAlert('error', 'An error occurred while saving changes');
        } finally {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = Object.keys(pendingChanges).length === 0;
        }
    };

    // Show alert
    window.showMatrixAlert = function(type, message, duration = 5000) {
        const container = document.getElementById('matrixAlertContainer');
        const alertId = 'alert-' + Date.now();

        const alertColors = {
            success: 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
            error: 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
            warning: 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200',
            info: 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
        };

        const alertIcons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle',
        };

        const alertHtml = `
            <div id="${alertId}" class="flex items-center gap-3 p-4 rounded-lg border shadow-lg min-w-80 max-w-md ${alertColors[type]} transform translate-x-full opacity-0 transition-all duration-300">
                <i class="fas ${alertIcons[type]} text-lg flex-shrink-0"></i>
                <p class="flex-1 text-sm font-medium">${message}</p>
                <button onclick="dismissMatrixAlert('${alertId}')" class="flex-shrink-0 hover:opacity-70 transition-opacity">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', alertHtml);

        // Animate in
        setTimeout(() => {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                alertElement.classList.remove('translate-x-full', 'opacity-0');
            }
        }, 10);

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => dismissMatrixAlert(alertId), duration);
        }
    };

    window.dismissMatrixAlert = function(alertId) {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => alertElement.remove(), 300);
        }
    };

    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (Object.keys(pendingChanges).length > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

})();
