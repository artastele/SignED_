<!-- 
    Reusable Modal Component
    Usage: Include this file in your views and trigger with JavaScript
-->

<!-- Success Modal -->
<div id="successModal" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-success">
        <div class="modal-header">
            <div class="modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <button class="modal-close" onclick="closeModal('successModal')">&times;</button>
        </div>
        <div class="modal-body">
            <h3 class="modal-title">Success!</h3>
            <p class="modal-message" id="successMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeModal('successModal')">OK</button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-error">
        <div class="modal-header">
            <div class="modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <button class="modal-close" onclick="closeModal('errorModal')">&times;</button>
        </div>
        <div class="modal-body">
            <h3 class="modal-title">Error</h3>
            <p class="modal-message" id="errorMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('errorModal')">Close</button>
        </div>
    </div>
</div>

<!-- Warning/Confirmation Modal -->
<div id="confirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-warning">
        <div class="modal-header">
            <div class="modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <button class="modal-close" onclick="closeModal('confirmModal')">&times;</button>
        </div>
        <div class="modal-body">
            <h3 class="modal-title">Confirm Action</h3>
            <p class="modal-message" id="confirmMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn btn-primary" id="confirmButton" onclick="confirmAction()">Confirm</button>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div id="infoModal" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-info">
        <div class="modal-header">
            <div class="modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <button class="modal-close" onclick="closeModal('infoModal')">&times;</button>
        </div>
        <div class="modal-body">
            <h3 class="modal-title">Information</h3>
            <p class="modal-message" id="infoMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeModal('infoModal')">OK</button>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-loading">
        <div class="modal-body" style="text-align: center;">
            <div class="loading-spinner"></div>
            <p class="modal-message" id="loadingMessage">Please wait...</p>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

.modal-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp 0.3s ease;
}

.modal-header {
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-bottom: 1px solid #e5e7eb;
}

.modal-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.modal-icon svg {
    width: 28px;
    height: 28px;
    stroke-width: 2.5;
}

.modal-success .modal-icon {
    background: #d1fae5;
    color: #065f46;
}

.modal-error .modal-icon {
    background: #fee2e2;
    color: #991b1b;
}

.modal-warning .modal-icon {
    background: #fef3c7;
    color: #92400e;
}

.modal-info .modal-icon {
    background: #dbeafe;
    color: #1e40af;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #111827;
}

.modal-body {
    padding: 1.5rem;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.5rem 0;
}

.modal-message {
    font-size: 1rem;
    color: #6b7280;
    line-height: 1.6;
    margin: 0;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

.modal-footer .btn {
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 6px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-footer .btn-primary {
    background: linear-gradient(135deg, #B91C3C, #1E40AF);
    color: white;
}

.modal-footer .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(185, 28, 60, 0.3);
}

.modal-footer .btn-secondary {
    background: #6b7280;
    color: white;
}

.modal-footer .btn-secondary:hover {
    background: #4b5563;
}

/* Loading Spinner */
.loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e5e7eb;
    border-top-color: #B91C3C;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 1rem;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */
@media (max-width: 640px) {
    .modal-container {
        width: 95%;
        margin: 1rem;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .modal-footer .btn {
        width: 100%;
    }
}
</style>

<script>
// Modal JavaScript Functions
let confirmCallback = null;

function showModal(type, message, title = null) {
    const modalId = type + 'Modal';
    const modal = document.getElementById(modalId);
    const messageElement = document.getElementById(type + 'Message');
    
    if (modal && messageElement) {
        messageElement.textContent = message;
        
        // Update title if provided
        if (title) {
            const titleElement = modal.querySelector('.modal-title');
            if (titleElement) {
                titleElement.textContent = title;
            }
        }
        
        modal.style.display = 'flex';
        
        // Auto-close success and info modals after 3 seconds
        if (type === 'success' || type === 'info') {
            setTimeout(() => {
                closeModal(modalId);
            }, 3000);
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Reset confirm callback
    if (modalId === 'confirmModal') {
        confirmCallback = null;
    }
}

function showSuccess(message, title = 'Success!') {
    showModal('success', message, title);
}

function showError(message, title = 'Error') {
    showModal('error', message, title);
}

function showInfo(message, title = 'Information') {
    showModal('info', message, title);
}

function showLoading(message = 'Please wait...') {
    const modal = document.getElementById('loadingModal');
    const messageElement = document.getElementById('loadingMessage');
    
    if (modal && messageElement) {
        messageElement.textContent = message;
        modal.style.display = 'flex';
    }
}

function hideLoading() {
    closeModal('loadingModal');
}

function showConfirm(message, callback, title = 'Confirm Action') {
    const modal = document.getElementById('confirmModal');
    const messageElement = document.getElementById('confirmMessage');
    const titleElement = modal.querySelector('.modal-title');
    
    if (modal && messageElement) {
        messageElement.textContent = message;
        if (titleElement) {
            titleElement.textContent = title;
        }
        modal.style.display = 'flex';
        confirmCallback = callback;
    }
}

function confirmAction() {
    if (confirmCallback && typeof confirmCallback === 'function') {
        confirmCallback();
    }
    closeModal('confirmModal');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        const modalId = event.target.id;
        if (modalId !== 'loadingModal' && modalId !== 'confirmModal') {
            closeModal(modalId);
        }
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            if (modal.style.display === 'flex' && modal.id !== 'loadingModal') {
                closeModal(modal.id);
            }
        });
    }
});

// Check for URL parameters and show appropriate modal
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('success')) {
        showSuccess(urlParams.get('success'));
    }
    
    if (urlParams.has('error')) {
        showError(urlParams.get('error'));
    }
    
    if (urlParams.has('info')) {
        showInfo(urlParams.get('info'));
    }
});
</script>
