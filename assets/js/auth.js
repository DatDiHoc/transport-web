document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.php-email-form');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const loadingDiv = this.querySelector('.loading');
            const errorDiv = this.querySelector('.error-message');
            const successDiv = this.querySelector('.sent-message');
            
            try {
                submitButton.disabled = true;
                if (loadingDiv) loadingDiv.style.display = 'block';
                if (errorDiv) errorDiv.style.display = 'none';
                if (successDiv) successDiv.style.display = 'none';
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (successDiv) {
                        successDiv.textContent = data.message;
                        successDiv.style.display = 'block';
                    }
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                if (errorDiv) {
                    errorDiv.textContent = error.message;
                    errorDiv.style.display = 'block';
                }
            } finally {
                submitButton.disabled = false;
                if (loadingDiv) loadingDiv.style.display = 'none';
            }
        });
    }
}); 