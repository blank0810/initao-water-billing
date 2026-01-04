/**
 * Add User Form Management
 * Handles user creation with API integration
 */
class AddUserManager {
    constructor() {
        this.formData = {};
        this.roles = [];
        this.isSubmitting = false;
    }

    // Get CSRF token
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    // Fetch available roles from API
    async fetchRoles() {
        try {
            const response = await fetch('/api/roles/available', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
            });

            if (response.ok) {
                const result = await response.json();
                this.roles = result.data || [];
                return this.roles;
            }
            return [];
        } catch (error) {
            console.error('Error fetching roles:', error);
            return [];
        }
    }

    // Populate role dropdown
    async populateRoleDropdown(selectElement) {
        if (!selectElement) return;

        const roles = await this.fetchRoles();

        // Clear existing options except placeholder
        selectElement.innerHTML = '<option value="">Select Role</option>';

        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.role_id;
            option.textContent = role.role_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            selectElement.appendChild(option);
        });
    }

    // Validate form
    validateForm(formData) {
        const errors = [];

        if (!formData.name || formData.name.trim() === '') {
            errors.push('Full name is required');
        }

        if (!formData.email || formData.email.trim() === '') {
            errors.push('Email is required');
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.email)) {
                errors.push('Please enter a valid email address');
            }
        }

        if (!formData.password || formData.password.length < 8) {
            errors.push('Password must be at least 8 characters');
        }

        if (formData.password !== formData.password_confirmation) {
            errors.push('Passwords do not match');
        }

        if (!formData.role_id) {
            errors.push('Please select a role');
        }

        if (!formData.status_id) {
            errors.push('Please select a status');
        }

        return {
            valid: errors.length === 0,
            errors,
            message: errors.join(', ')
        };
    }

    // Create user via API
    async createUser(formData) {
        if (this.isSubmitting) {
            return { success: false, message: 'Already submitting...' };
        }

        const validation = this.validateForm(formData);
        if (!validation.valid) {
            return { success: false, message: validation.message, errors: validation.errors };
        }

        this.isSubmitting = true;

        try {
            const response = await fetch('/user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
                body: JSON.stringify({
                    name: formData.name.trim(),
                    email: formData.email.trim(),
                    password: formData.password,
                    password_confirmation: formData.password_confirmation,
                    role_id: parseInt(formData.role_id),
                    status_id: parseInt(formData.status_id),
                }),
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.formData = {};
                return {
                    success: true,
                    user: result.data,
                    message: result.message || 'User created successfully!'
                };
            } else {
                // Handle validation errors from Laravel
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat();
                    return {
                        success: false,
                        message: errorMessages.join(', '),
                        errors: result.errors
                    };
                }
                return {
                    success: false,
                    message: result.message || 'Failed to create user'
                };
            }
        } catch (error) {
            console.error('Error creating user:', error);
            return {
                success: false,
                message: 'Network error. Please try again.'
            };
        } finally {
            this.isSubmitting = false;
        }
    }

    // Reset form
    resetForm() {
        this.formData = {};
    }

    // Get role options (for backward compatibility)
    getRoleOptions() {
        const options = {};
        this.roles.forEach(role => {
            options[role.role_id] = role.role_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        });
        return options;
    }

    // Get status options
    getStatusOptions() {
        return {
            'active': 'Active',
            'inactive': 'Inactive'
        };
    }
}

// Export for global access
window.AddUserManager = AddUserManager;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const manager = new AddUserManager();
    window.addUserManager = manager;

    // Populate role dropdown if it exists (check multiple possible IDs)
    const roleSelect = document.getElementById('roleSelect')
        || document.getElementById('role')
        || document.querySelector('[name="role_id"]');
    if (roleSelect) {
        manager.populateRoleDropdown(roleSelect);
    }

    // Handle form submission (check multiple possible form IDs)
    const addUserForm = document.getElementById('userRegistrationForm')
        || document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(addUserForm);

            // Build name from first_name + last_name or use name directly
            const firstName = formData.get('first_name') || '';
            const lastName = formData.get('last_name') || '';
            const fullName = formData.get('name') || `${firstName} ${lastName}`.trim();

            // Get status_id from select, or convert status string to ID
            let statusId = formData.get('status_id');
            if (!statusId) {
                // Fallback: if status field exists with 'active'/'inactive' values
                const statusValue = formData.get('status');
                if (statusValue) {
                    // This requires fetching status IDs - for now use the select value directly
                    statusId = statusValue;
                }
            }

            const data = {
                name: fullName,
                email: formData.get('email'),
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation'),
                role_id: formData.get('role_id') || formData.get('role'),
                status_id: statusId,
            };

            // Show loading state
            const submitBtn = addUserForm.querySelector('[type="submit"]');
            const originalText = submitBtn?.innerHTML;
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
                submitBtn.disabled = true;
            }

            const result = await manager.createUser(data);

            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }

            if (result.success) {
                // Show success message
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'success', message: result.message }
                }));

                // Reset form
                addUserForm.reset();

                // Redirect to user list
                setTimeout(() => {
                    window.location.href = '/user/list';
                }, 1500);
            } else {
                // Show error message
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', message: result.message }
                }));

                // Fallback alert if no alert system
                if (!window.hasEventListener?.('show-alert')) {
                    alert('Error: ' + result.message);
                }
            }
        });
    }
});
