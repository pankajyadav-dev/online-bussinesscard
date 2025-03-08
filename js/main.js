/**
 * Main JavaScript file for Business Card Creator
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100');
    if (flashMessages.length > 0) {
        setTimeout(function() {
            flashMessages.forEach(function(message) {
                message.style.opacity = '0';
                message.style.transition = 'opacity 1s ease';
                setTimeout(function() {
                    message.style.display = 'none';
                }, 1000);
            });
        }, 5000);
    }

    // Add card hover effects to all cards
    const cards = document.querySelectorAll('.border.rounded-lg.overflow-hidden.shadow-sm');
    cards.forEach(function(card) {
        card.classList.add('card-hover-effect');
    });

    // Copy to clipboard functionality
    const copyButtons = document.querySelectorAll('[data-copy]');
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-copy');
            
            if (textToCopy) {
                const tempInput = document.createElement('input');
                tempInput.value = textToCopy;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                // Show feedback
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            }
        });
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            let hasError = false;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    hasError = true;
                    
                    // Add error message if not already present
                    const errorMsg = field.parentNode.querySelector('.error-message');
                    if (!errorMsg) {
                        const msg = document.createElement('p');
                        msg.classList.add('text-red-500', 'text-sm', 'mt-1', 'error-message');
                        msg.textContent = 'This field is required';
                        field.parentNode.appendChild(msg);
                    }
                } else {
                    field.classList.remove('border-red-500');
                    const errorMsg = field.parentNode.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (hasError) {
                event.preventDefault();
            }
        });
    });

    // Remove red border when user starts typing
    const formInputs = document.querySelectorAll('input, textarea');
    formInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-red-500');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });

    // Handle loading state for form submissions
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(function(button) {
        const form = button.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="loader"></span> Processing...';
                
                // Store original text to restore if form submission fails
                button.setAttribute('data-original-text', originalText);
                
                // Enable button after 10 seconds (failsafe)
                setTimeout(function() {
                    if (button.disabled) {
                        button.disabled = false;
                        button.innerHTML = button.getAttribute('data-original-text');
                    }
                }, 10000);
            });
        }
    });
}); 