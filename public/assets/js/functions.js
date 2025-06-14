function ajaxRequest(url, method = 'GET', data = null, headers = {}) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: method,
            data: data,
            headers: headers,
            dataType: 'json', // Automatically expect JSON response
            success: function (response) {
                resolve(response); // Resolve the promise with the response
            },
            error: function (xhr, status, error) {
                reject({
                    status: xhr.status,
                    statusText: xhr.statusText,
                    response: xhr.responseJSON || xhr.responseText,
                    error: error
                }); // Reject the promise with error details
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

/** Format a number or input value as a price string with thousand separators.
 * @param {number|HTMLInputElement} input - The number to format or an input element.
 * @returns {string} - The formatted price string.
 */
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

/**
 * Prevent form submission on Enter key and trigger a specific button click.
 * 
 * @param {string} formSelector - The selector for the form (e.g., '#formCategory').
 * @param {string} buttonSelector - The selector for the button to trigger (e.g., '#btnSaveCategory').
 */
function triggerBtnOnEnter(formSelector, buttonSelector) {
    $(formSelector).on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(buttonSelector).click();
        }
    });
};


/**
 * Attach a keyup search listener to any input and call a dynamic fetch function.
 * 
 * @param {string} inputSelector - The selector for the search input (e.g., '#searchCategory')
 * @param {Function} fetchFunction - The function to call with the endpoint and search value
 * @param {string} endpoint - The API URL (e.g., '/api/settings/categories')
 */
function actionSearch(inputSelector, fetchFunction, endpoint) {
    $(inputSelector).on('keyup', function () {
        const searchQuery = $(this).val();
        fetchFunction(endpoint, searchQuery);
    });
};

function pagination(paginateLink, fetchFunction, searchSelector) {
    // Handle pagination link clicks
    $(paginateLink).on('click', '.page-link', function (e) {
        e.preventDefault();
        const url = $(this).data('url');
        if (url) {
            const searchQuery = $(searchSelector).val();
            fetchFunction(url, searchQuery);
        }
    });
}

function actionResetModal(modalId, formId, buttonClass) {
    $(modalId).on('show.bs.modal', async function (e) {

        const triggerButton = $(e.relatedTarget);
        if (triggerButton.hasClass(buttonClass)) {
            $(formId)[0].reset();
            $(`${formId} input[name="id"]`).val('');
        } else {
            try {
                const id = $(`${formId} input[name="id"]`).val('');
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        }

    });
}
