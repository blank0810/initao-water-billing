// Add User Form Management
class AddUserManager {
    constructor() {
        this.formData = {};
    }

    validateForm(formData) {
        const required = ['last_name', 'first_name', 'email', 'password', 'role', 'status'];
        
        for (const field of required) {
            if (!formData[field] || formData[field].trim() === '') {
                return { valid: false, message: `${field.replace('_', ' ')} is required` };
            }
        }

        if (formData.password.length < 8) {
            return { valid: false, message: 'Password must be at least 8 characters long' };
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            return { valid: false, message: 'Please enter a valid email address' };
        }

        return { valid: true };
    }

    generateUserId() {
        const timestamp = Date.now().toString().slice(-6);
        return `USR${timestamp}`;
    }

    createUser(formData) {
        const validation = this.validateForm(formData);
        if (!validation.valid) {
            throw new Error(validation.message);
        }

        const userId = this.generateUserId();
        
        const newUser = {
            id: userId,
            UserName: `${formData.first_name.trim()} ${formData.last_name.trim()}`,
            Email: formData.email.trim(),
            Role: formData.role,
            Status: formData.status,
            DateCreated: new Date().toISOString().split('T')[0],
            created_at: new Date().toISOString()
        };

        // Reset form data
        this.formData = {};

        return {
            success: true,
            userId,
            user: newUser,
            message: `User created successfully!\nUser ID: ${userId}\nRole: ${formData.role}`
        };
    }

    resetForm() {
        this.formData = {};
    }

    getRoleOptions() {
        return {
            'admin': 'Administrator',
            'user': 'User'
        };
    }

    getStatusOptions() {
        return {
            'active': 'Active',
            'inactive': 'Inactive'
        };
    }
}

// Export for global access
window.AddUserManager = AddUserManager;