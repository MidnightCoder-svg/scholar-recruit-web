
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Tab functionality
    const tabs = document.querySelectorAll('[data-toggle="tab"]');
    if (tabs.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('active');
                    document.querySelector(t.getAttribute('href')).classList.remove('show', 'active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                document.querySelector(this.getAttribute('href')).classList.add('show', 'active');
            });
        });
    }
    
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltipTriggerList.length > 0) {
        [...tooltipTriggerList].map(tooltipTriggerEl => {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Form password matching validation
function validatePassword() {
    const password = document.getElementById('password');
    const confirm = document.getElementById('confirm_password');
    
    if (password && confirm) {
        if (password.value != confirm.value) {
            confirm.setCustomValidity("Passwords don't match");
        } else {
            confirm.setCustomValidity('');
        }
    }
}
