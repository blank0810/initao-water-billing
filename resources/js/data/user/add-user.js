/**
 * Add User Form Management
 * Handles user creation with API integration
 */
class AddUserManager {
    constructor() {
        this.formData = {};
        this.roles = [];
        this.isSubmitting = false;
        this.usernameManuallyEdited = false;
        this.suggestionDebounceTimer = null;
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

        if (!formData.username || formData.username.trim() === '') {
            errors.push('Username is required');
        }

        if (formData.email && formData.email.trim() !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.email)) {
                errors.push('Please enter a valid email address');
            }
        }

        if (!formData.password || formData.password.length < 8) {
            errors.push('Password must be at least 8 characters');
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
                    username: formData.username.trim(),
                    email: formData.email ? formData.email.trim() : null,
                    password: formData.password,
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

    // Fetch username suggestions from API
    async fetchUsernameSuggestions(firstName, lastName) {
        if (!firstName || !lastName) return [];

        try {
            const params = new URLSearchParams({ first_name: firstName, last_name: lastName });
            const response = await fetch(`/user/suggest-username?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
            });

            if (response.ok) {
                const result = await response.json();
                return result.data || [];
            }
            return [];
        } catch (error) {
            console.error('Error fetching username suggestions:', error);
            return [];
        }
    }

    // Display username suggestion chips
    displaySuggestions(suggestions) {
        const container = document.getElementById('usernameSuggestions');
        const chips = document.getElementById('suggestionChips');
        if (!container || !chips) return;

        chips.innerHTML = '';

        if (suggestions.length === 0) {
            container.classList.add('hidden');
            return;
        }

        suggestions.forEach(username => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.textContent = username;
            chip.className = 'px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-700 cursor-pointer transition-colors';
            chip.addEventListener('click', () => {
                const usernameInput = document.getElementById('usernameInput');
                if (usernameInput) {
                    usernameInput.value = username;
                    this.usernameManuallyEdited = true;
                }
                container.classList.add('hidden');
            });
            chips.appendChild(chip);
        });

        container.classList.remove('hidden');
    }

    // Debounced suggestion fetch triggered by name field changes
    onNameChanged(firstName, lastName) {
        clearTimeout(this.suggestionDebounceTimer);
        this.suggestionDebounceTimer = setTimeout(async () => {
            if (firstName.trim() && lastName.trim()) {
                const suggestions = await this.fetchUsernameSuggestions(firstName.trim(), lastName.trim());
                this.displaySuggestions(suggestions);

                // Auto-fill username with first suggestion if admin hasn't manually edited it
                if (!this.usernameManuallyEdited && suggestions.length > 0) {
                    const usernameInput = document.getElementById('usernameInput');
                    if (usernameInput) {
                        usernameInput.value = suggestions[0];
                    }
                }
            }
        }, 400);
    }

    // Reset form
    resetForm() {
        this.formData = {};
        this.usernameManuallyEdited = false;
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

    // Username suggestion listeners
    const firstNameInput = document.querySelector('[name="first_name"]');
    const lastNameInput = document.querySelector('[name="last_name"]');
    const usernameInput = document.getElementById('usernameInput');

    if (firstNameInput && lastNameInput) {
        const triggerSuggestion = () => {
            manager.onNameChanged(firstNameInput.value, lastNameInput.value);
        };
        firstNameInput.addEventListener('input', triggerSuggestion);
        lastNameInput.addEventListener('input', triggerSuggestion);
    }

    // Track manual username edits
    if (usernameInput) {
        usernameInput.addEventListener('input', () => {
            manager.usernameManuallyEdited = true;
        });
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

            const data = {
                name: fullName,
                username: formData.get('username'),
                email: formData.get('email'),
                password: formData.get('password'),
                role_id: formData.get('role_id'),
                status_id: formData.get('status_id'),
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
                manager.usernameManuallyEdited = false;

                // Redirect to user list
                setTimeout(() => {
                    window.location.href = '/user/list';
                }, 1500);
            } else {
                // Show error message
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', message: result.message }
                }));
            }
        });
    }
});
