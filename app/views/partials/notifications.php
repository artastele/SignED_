<?php
/**
 * Notification Bell Component
 * Displays notification icon with unread count and dropdown
 * 
 * Required: Include this in your header/navigation
 */

// Get unread notification count
$unreadCount = $data['unread_notifications'] ?? 0;
?>

<div class="notification-bell">
    <button class="notification-button" onclick="toggleNotifications()" id="notificationButton">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <?php if ($unreadCount > 0): ?>
            <span class="notification-badge"><?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?></span>
        <?php endif; ?>
    </button>
    
    <div class="notification-dropdown" id="notificationDropdown" style="display: none;">
        <div class="notification-header">
            <h3>Notifications</h3>
            <?php if ($unreadCount > 0): ?>
                <button class="mark-all-read" onclick="markAllAsRead()">Mark all as read</button>
            <?php endif; ?>
        </div>
        
        <div class="notification-list" id="notificationList">
            <div class="notification-loading">
                <div class="loading-spinner-small"></div>
                <p>Loading notifications...</p>
            </div>
        </div>
        
        <div class="notification-footer">
            <a href="<?php echo URLROOT; ?>/notifications">View all notifications</a>
        </div>
    </div>
</div>

<style>
/* Notification Bell Styles */
.notification-bell {
    position: relative;
    display: inline-block;
}

.notification-button {
    position: relative;
    background: none;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;
}

.notification-button:hover {
    background: rgba(0, 0, 0, 0.05);
}

.notification-button svg {
    width: 24px;
    height: 24px;
    color: #374151;
}

.notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #ef4444;
    color: white;
    font-size: 0.625rem;
    font-weight: 600;
    padding: 0.125rem 0.375rem;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.notification-dropdown {
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    width: 380px;
    max-width: 90vw;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    animation: slideDown 0.2s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.mark-all-read {
    background: none;
    border: none;
    color: #1E40AF;
    font-size: 0.875rem;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: background 0.2s;
}

.mark-all-read:hover {
    background: #eff6ff;
}

.notification-list {
    max-height: 400px;
    overflow-y: auto;
}

.notification-loading {
    padding: 2rem;
    text-align: center;
    color: #6b7280;
}

.loading-spinner-small {
    width: 32px;
    height: 32px;
    border: 3px solid #e5e7eb;
    border-top-color: #B91C3C;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 0.5rem;
}

.notification-item {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    gap: 0.75rem;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-item.unread {
    background: #eff6ff;
}

.notification-item.unread:hover {
    background: #dbeafe;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-icon svg {
    width: 20px;
    height: 20px;
}

.notification-icon.info {
    background: #dbeafe;
    color: #1e40af;
}

.notification-icon.success {
    background: #d1fae5;
    color: #065f46;
}

.notification-icon.warning {
    background: #fef3c7;
    color: #92400e;
}

.notification-icon.error {
    background: #fee2e2;
    color: #991b1b;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.25rem 0;
    font-size: 0.9375rem;
}

.notification-message {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0 0 0.25rem 0;
    line-height: 1.4;
}

.notification-time {
    color: #9ca3af;
    font-size: 0.75rem;
}

.notification-unread-dot {
    width: 8px;
    height: 8px;
    background: #3b82f6;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 0.5rem;
}

.notification-empty {
    padding: 3rem 1.25rem;
    text-align: center;
    color: #6b7280;
}

.notification-empty svg {
    width: 48px;
    height: 48px;
    margin: 0 auto 1rem;
    opacity: 0.5;
}

.notification-footer {
    padding: 0.75rem 1.25rem;
    border-top: 1px solid #e5e7eb;
    text-align: center;
}

.notification-footer a {
    color: #1E40AF;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.notification-footer a:hover {
    text-decoration: underline;
}

/* Scrollbar styling */
.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive */
@media (max-width: 640px) {
    .notification-dropdown {
        width: 100vw;
        max-width: 100vw;
        right: -1rem;
        border-radius: 0;
    }
}
</style>

<script>
let notificationDropdownOpen = false;

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    notificationDropdownOpen = !notificationDropdownOpen;
    
    if (notificationDropdownOpen) {
        dropdown.style.display = 'block';
        loadNotifications();
    } else {
        dropdown.style.display = 'none';
    }
}

function loadNotifications() {
    const listElement = document.getElementById('notificationList');
    
    // Show loading state
    listElement.innerHTML = `
        <div class="notification-loading">
            <div class="loading-spinner-small"></div>
            <p>Loading notifications...</p>
        </div>
    `;
    
    // Fetch notifications via AJAX
    fetch('<?php echo URLROOT; ?>/notifications/get-recent')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notifications.length > 0) {
                renderNotifications(data.notifications);
            } else {
                showEmptyState();
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            listElement.innerHTML = `
                <div class="notification-empty">
                    <p>Failed to load notifications</p>
                </div>
            `;
        });
}

function renderNotifications(notifications) {
    const listElement = document.getElementById('notificationList');
    
    const html = notifications.map(notif => {
        const iconType = notif.type || 'info';
        const isUnread = !notif.is_read;
        const timeAgo = formatTimeAgo(notif.created_at);
        
        return `
            <div class="notification-item ${isUnread ? 'unread' : ''}" onclick="handleNotificationClick(${notif.id}, '${notif.link || ''}')">
                <div class="notification-icon ${iconType}">
                    ${getNotificationIcon(notif.category || 'general')}
                </div>
                <div class="notification-content">
                    <h4 class="notification-title">${escapeHtml(notif.title)}</h4>
                    <p class="notification-message">${escapeHtml(notif.message)}</p>
                    <span class="notification-time">${timeAgo}</span>
                </div>
                ${isUnread ? '<div class="notification-unread-dot"></div>' : ''}
            </div>
        `;
    }).join('');
    
    listElement.innerHTML = html;
}

function showEmptyState() {
    const listElement = document.getElementById('notificationList');
    listElement.innerHTML = `
        <div class="notification-empty">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p>No notifications yet</p>
        </div>
    `;
}

function handleNotificationClick(notificationId, link) {
    // Mark as read
    fetch('<?php echo URLROOT; ?>/notifications/mark-read/' + notificationId, {
        method: 'POST'
    }).then(() => {
        // Update badge count
        updateNotificationBadge();
        
        // Navigate to link if provided
        if (link) {
            window.location.href = link;
        }
    });
}

function markAllAsRead() {
    fetch('<?php echo URLROOT; ?>/notifications/mark-all-read', {
        method: 'POST'
    }).then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
                updateNotificationBadge();
            }
        });
}

function updateNotificationBadge() {
    fetch('<?php echo URLROOT; ?>/notifications/get-unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                } else {
                    const button = document.getElementById('notificationButton');
                    const newBadge = document.createElement('span');
                    newBadge.className = 'notification-badge';
                    newBadge.textContent = data.count > 99 ? '99+' : data.count;
                    button.appendChild(newBadge);
                }
            } else if (badge) {
                badge.remove();
            }
        });
}

function getNotificationIcon(category) {
    const icons = {
        'enrollment': '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>',
        'assessment': '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>',
        'iep': '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
        'meeting': '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
        'system': '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'general': '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>'
    };
    return icons[category] || icons['general'];
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
    return date.toLocaleDateString();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const bell = document.querySelector('.notification-bell');
    if (bell && !bell.contains(event.target) && notificationDropdownOpen) {
        toggleNotifications();
    }
});

// Poll for new notifications every 30 seconds
setInterval(updateNotificationBadge, 30000);
</script>
