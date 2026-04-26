<!-- Simple Pop-up System -->
<style>
.simple-popup {
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    max-width: 400px;
    animation: slideIn 0.3s ease;
}

.simple-popup.error {
    border-left: 4px solid #ef4444;
}

.simple-popup.success {
    border-left: 4px solid #10b981;
}

.simple-popup.info {
    border-left: 4px solid #3b82f6;
}

.simple-popup-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #6b7280;
}

.simple-popup-message {
    margin: 0;
    color: #111827;
    font-size: 14px;
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<div id="simplePopup" class="simple-popup">
    <button class="simple-popup-close" onclick="closePopup()">&times;</button>
    <p class="simple-popup-message" id="popupMessage"></p>
</div>

<script>
function showPopup(message, type = 'error') {
    const popup = document.getElementById('simplePopup');
    const messageEl = document.getElementById('popupMessage');
    
    // Set message
    messageEl.textContent = message;
    
    // Set type
    popup.className = 'simple-popup ' + type;
    
    // Show popup
    popup.style.display = 'block';
    
    // Auto close after 5 seconds
    setTimeout(closePopup, 5000);
}

function closePopup() {
    const popup = document.getElementById('simplePopup');
    popup.style.display = 'none';
}

// Check URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('error')) {
        showPopup(urlParams.get('error'), 'error');
    }
    
    if (urlParams.has('success')) {
        showPopup(urlParams.get('success'), 'success');
    }
    
    if (urlParams.has('info')) {
        showPopup(urlParams.get('info'), 'info');
    }
    
    if (urlParams.has('locked')) {
        showPopup('Account temporarily locked. Please try again in 30 minutes.', 'error');
    }
    
    if (urlParams.has('timeout')) {
        showPopup('Your session has expired. Please log in again.', 'info');
    }
});
</script>
