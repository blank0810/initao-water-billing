let addUserManager;

document.addEventListener('DOMContentLoaded', function() {
    addUserManager = new AddUserManager();
    initializeForm();
});

function initializeForm() {
    const form = document.getElementById('userRegistrationForm');
    form.addEventListener('submit', handleFormSubmit);
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    const submitButton = form.querySelector('button[type="submit"]');
    const originalHTML = submitButton.innerHTML;
    
    try {
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        submitButton.disabled = true;
        
        setTimeout(() => {
            const result = addUserManager.createUser(data);
            alert(result.message);
            form.reset();
            submitButton.innerHTML = originalHTML;
            submitButton.disabled = false;
        }, 1500);
    } catch (error) {
        alert(error.message);
        submitButton.innerHTML = originalHTML;
        submitButton.disabled = false;
    }
}