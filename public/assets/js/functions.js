
    function ajaxRequest(url, method = 'GET', data = null, headers = {}) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: url,
                method: method,
                data: data,
                headers: headers,
                dataType: 'json',  // Automatically expect JSON response
                success: function(response) {
                    resolve(response);  // Resolve the promise with the response
                },
                error: function(xhr, status, error) {
                    reject({
                        status: xhr.status,
                        statusText: xhr.statusText,
                        response: xhr.responseJSON || xhr.responseText,
                        error: error
                    });  // Reject the promise with error details
                }
            });
        });
    }

    function showToast(message, type = 'success') {
        // Remove existing toasts to avoid duplicates
        const existingToasts = document.querySelectorAll('.dynamic-toast');
        existingToasts.forEach(toast => toast.remove());
    
        // Toast types: 'success', 'danger', 'warning', 'info'
        const toastColors = {
            success: 'text-bg-success',
            danger: 'text-bg-danger',
            warning: 'text-bg-warning',
            info: 'text-bg-info',
        };
    
        // Create toast container if not already present
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
    
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center border-0 dynamic-toast ${toastColors[type] || 'text-bg-success'}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
    
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
    
        // Append toast to the container
        toastContainer.appendChild(toast);
    
        // Initialize and show the toast
        const bootstrapToast = new bootstrap.Toast(toast);
        bootstrapToast.show();
    
        // Optional: Automatically remove toast from DOM after it hides
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
    
    function formatPrice(input) {
        // Check if input is a DOM element or a direct number
        let value = typeof input === 'object' ? input.value : input.toString();
    
        // Remove non-numeric characters
        value = value.replace(/\D/g, '');
    
        // Add thousand separator (.)
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
        // If input is a DOM element, update its value
        if (typeof input === 'object') {
            input.value = value;
        }
    
        return value; // Return the formatted value
    }
    