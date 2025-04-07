
/**
 * Main JavaScript file for ScholarRecruit
 */

document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Enable Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Password strength indicator
    var passwordInputs = document.querySelectorAll('input[type="password"][data-password-strength]');
    
    passwordInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            checkPasswordStrength(input);
        });
    });
    
    function checkPasswordStrength(input) {
        var password = input.value;
        var strength = 0;
        
        if (password.length >= 8) strength += 1;
        if (password.match(/[a-z]+/)) strength += 1;
        if (password.match(/[A-Z]+/)) strength += 1;
        if (password.match(/[0-9]+/)) strength += 1;
        if (password.match(/[$@#&!]+/)) strength += 1;
        
        var indicator = input.nextElementSibling;
        if (indicator && indicator.classList.contains('password-strength-meter')) {
            // Update the strength meter
            var strengthClass = '';
            var strengthText = '';
            
            if (password.length === 0) {
                strengthClass = 'bg-secondary';
                strengthText = 'Empty';
            } else if (strength < 2) {
                strengthClass = 'bg-danger';
                strengthText = 'Weak';
            } else if (strength < 4) {
                strengthClass = 'bg-warning';
                strengthText = 'Medium';
            } else {
                strengthClass = 'bg-success';
                strengthText = 'Strong';
            }
            
            indicator.className = 'password-strength-meter progress-bar ' + strengthClass;
            indicator.style.width = (strength * 20) + '%';
            indicator.setAttribute('aria-valuenow', strength * 20);
            indicator.textContent = strengthText;
        }
    }
    
    // Filter job listings
    var jobFilter = document.getElementById('job-filter');
    if (jobFilter) {
        jobFilter.addEventListener('submit', function(e) {
            e.preventDefault();
            filterJobs();
        });
    }
    
    function filterJobs() {
        var keyword = document.getElementById('filter-keyword').value.toLowerCase();
        var location = document.getElementById('filter-location').value.toLowerCase();
        var type = document.getElementById('filter-type').value;
        
        var jobItems = document.querySelectorAll('.job-item');
        
        jobItems.forEach(function(item) {
            var title = item.querySelector('.job-title').textContent.toLowerCase();
            var company = item.querySelector('.job-company').textContent.toLowerCase();
            var jobLocation = item.querySelector('.job-location').textContent.toLowerCase();
            var jobType = item.querySelector('.job-type').textContent;
            
            var keywordMatch = title.includes(keyword) || company.includes(keyword);
            var locationMatch = location === '' || jobLocation.includes(location);
            var typeMatch = type === '' || jobType === type;
            
            if (keywordMatch && locationMatch && typeMatch) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Application status update
    var statusButtons = document.querySelectorAll('[data-update-status]');
    
    statusButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var applicationId = this.getAttribute('data-application-id');
            var newStatus = this.getAttribute('data-update-status');
            
            updateApplicationStatus(applicationId, newStatus);
        });
    });
    
    function updateApplicationStatus(applicationId, status) {
        // This would typically be an AJAX request to the server
        console.log('Updating application ' + applicationId + ' to status: ' + status);
        
        // For demonstration, update the status badge directly
        var statusBadge = document.querySelector('.status-badge[data-application-id="' + applicationId + '"]');
        
        if (statusBadge) {
            // Remove existing status classes
            statusBadge.classList.remove('bg-secondary', 'bg-primary', 'bg-success', 'bg-danger');
            
            // Add new status class and update text
            switch (status) {
                case 'pending':
                    statusBadge.classList.add('bg-secondary');
                    statusBadge.textContent = 'Pending';
                    break;
                case 'reviewed':
                    statusBadge.classList.add('bg-info');
                    statusBadge.textContent = 'Reviewed';
                    break;
                case 'interview':
                    statusBadge.classList.add('bg-primary');
                    statusBadge.textContent = 'Interview';
                    break;
                case 'accepted':
                    statusBadge.classList.add('bg-success');
                    statusBadge.textContent = 'Accepted';
                    break;
                case 'rejected':
                    statusBadge.classList.add('bg-danger');
                    statusBadge.textContent = 'Rejected';
                    break;
            }
        }
    }
});
