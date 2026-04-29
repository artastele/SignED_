<?php
/**
 * Fixed Sidebar Navigation Component
 * Consistent sidebar for all user roles
 * 
 * Required variables:
 * - $data['role'] - Current user role
 * - $data['user_name'] - Current user name
 * - $data['current_page'] - Current page identifier (optional)
 */

// Get current page from URL if not provided
$currentPage = $data['current_page'] ?? basename($_SERVER['PHP_SELF'], '.php');

// Define navigation items based on role
$navigationItems = [];

switch ($data['role']) {
    case 'parent':
        $navigationItems = [
            [
                'title' => 'Dashboard',
                'url' => URLROOT . '/parent/dashboard',
                'icon' => 'home',
                'page' => 'dashboard'
            ],
            [
                'title' => 'Enroll Child',
                'url' => URLROOT . '/enrollment/submit',
                'icon' => 'user-plus',
                'page' => 'enroll'
            ],
            [
                'title' => 'Assessment',
                'url' => URLROOT . '/assessment',
                'icon' => 'clipboard',
                'page' => 'assessment'
            ],
            [
                'title' => 'Manage Requirements',
                'url' => URLROOT . '/enrollment/status',
                'icon' => 'file-text',
                'page' => 'requirements',
                'badge' => $data['pending_documents'] ?? 0
            ],
            [
                'title' => 'My Children',
                'url' => URLROOT . '/parent/children',
                'icon' => 'users',
                'page' => 'children'
            ],
            [
                'title' => 'Notifications',
                'url' => URLROOT . '/notifications',
                'icon' => 'bell',
                'page' => 'notifications',
                'badge' => $data['unread_notifications'] ?? 0
            ],
            [
                'title' => 'Profile',
                'url' => URLROOT . '/user/profile',
                'icon' => 'user',
                'page' => 'profile'
            ]
        ];
        break;
        
    case 'sped_teacher':
        $navigationItems = [
            [
                'title' => 'Dashboard',
                'url' => URLROOT . '/sped/dashboard',
                'icon' => 'home',
                'page' => 'dashboard'
            ],
            [
                'title' => 'Verify Enrollments',
                'url' => URLROOT . '/enrollment/verify',
                'icon' => 'check-circle',
                'page' => 'verify',
                'badge' => $data['pending_verifications'] ?? 0
            ],
            [
                'title' => 'Student Records',
                'url' => URLROOT . '/learner/records',
                'icon' => 'folder',
                'page' => 'records'
            ],
            [
                'title' => 'IEP Management',
                'url' => URLROOT . '/iep/list',
                'icon' => 'file-text',
                'page' => 'iep'
            ],
            [
                'title' => 'Learning Materials',
                'url' => URLROOT . '/materials/manage',
                'icon' => 'book',
                'page' => 'materials'
            ],
            [
                'title' => 'Profile',
                'url' => URLROOT . '/user/profile',
                'icon' => 'user',
                'page' => 'profile'
            ]
        ];
        break;
        
    case 'guidance':
        $navigationItems = [
            [
                'title' => 'Dashboard',
                'url' => URLROOT . '/sped/dashboard',
                'icon' => 'home',
                'page' => 'dashboard'
            ],
            [
                'title' => 'IEP Meetings',
                'url' => URLROOT . '/iep/meetings',
                'icon' => 'calendar',
                'page' => 'meetings',
                'badge' => $data['upcoming_meetings'] ?? 0
            ],
            [
                'title' => 'Student Records',
                'url' => URLROOT . '/learner/records',
                'icon' => 'folder',
                'page' => 'records'
            ],
            [
                'title' => 'Notifications',
                'url' => URLROOT . '/notifications',
                'icon' => 'bell',
                'page' => 'notifications',
                'badge' => $data['unread_notifications'] ?? 0
            ],
            [
                'title' => 'Profile',
                'url' => URLROOT . '/user/profile',
                'icon' => 'user',
                'page' => 'profile'
            ]
        ];
        break;
        
    case 'principal':
        $navigationItems = [
            [
                'title' => 'Dashboard',
                'url' => URLROOT . '/sped/dashboard',
                'icon' => 'home',
                'page' => 'dashboard'
            ],
            [
                'title' => 'IEP Approvals',
                'url' => URLROOT . '/iep/approvals',
                'icon' => 'check-square',
                'page' => 'approvals',
                'badge' => $data['pending_approvals'] ?? 0
            ],
            [
                'title' => 'Reports',
                'url' => URLROOT . '/reports',
                'icon' => 'bar-chart',
                'page' => 'reports'
            ],
            [
                'title' => 'Notifications',
                'url' => URLROOT . '/notifications',
                'icon' => 'bell',
                'page' => 'notifications',
                'badge' => $data['unread_notifications'] ?? 0
            ],
            [
                'title' => 'Profile',
                'url' => URLROOT . '/user/profile',
                'icon' => 'user',
                'page' => 'profile'
            ]
        ];
        break;
        
    case 'learner':
        $navigationItems = [
            [
                'title' => 'Dashboard',
                'url' => URLROOT . '/learner/dashboard',
                'icon' => 'home',
                'page' => 'dashboard'
            ],
            [
                'title' => 'My Materials',
                'url' => URLROOT . '/learner/materials',
                'icon' => 'book',
                'page' => 'materials',
                'badge' => $data['new_materials'] ?? 0
            ],
            [
                'title' => 'Submit Work',
                'url' => URLROOT . '/learner/submit',
                'icon' => 'upload',
                'page' => 'submit'
            ],
            [
                'title' => 'My Progress',
                'url' => URLROOT . '/learner/progress',
                'icon' => 'trending-up',
                'page' => 'progress'
            ],
            [
                'title' => 'Notifications',
                'url' => URLROOT . '/notifications',
                'icon' => 'bell',
                'page' => 'notifications',
                'badge' => $data['unread_notifications'] ?? 0
            ],
            [
                'title' => 'Profile',
                'url' => URLROOT . '/user/profile',
                'icon' => 'user',
                'page' => 'profile'
            ]
        ];
        break;
        
    case 'admin':
        $navigationItems = [
            [
                'title' => 'Dashboard',
                'url' => URLROOT . '/admin/dashboard',
                'icon' => 'home',
                'page' => 'dashboard'
            ],
            [
                'title' => 'User Management',
                'url' => URLROOT . '/admin/users',
                'icon' => 'users',
                'page' => 'users'
            ],
            [
                'title' => 'Announcements',
                'url' => URLROOT . '/admin/announcements',
                'icon' => 'megaphone',
                'page' => 'announcements'
            ],
            [
                'title' => 'System Settings',
                'url' => URLROOT . '/admin/settings',
                'icon' => 'settings',
                'page' => 'settings'
            ],
            [
                'title' => 'Audit Logs',
                'url' => URLROOT . '/admin/logs',
                'icon' => 'file-text',
                'page' => 'logs'
            ],
            [
                'title' => 'Login Attempts',
                'url' => URLROOT . '/admin/loginAttempts',
                'icon' => 'shield-lock',
                'page' => 'login_attempts'
            ],
            [
                'title' => 'Admin Activity',
                'url' => URLROOT . '/admin/adminActivity',
                'icon' => 'person-badge',
                'page' => 'admin_activity'
            ],
            [
                'title' => 'Profile',
                'url' => URLROOT . '/user/profile',
                'icon' => 'user',
                'page' => 'profile'
            ]
        ];
        break;
}

// Icon SVG paths
$icons = [
    'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'user-plus' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
    'file-text' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    'bell' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
    'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    'check-circle' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'clipboard' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    'folder' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
    'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    'check-square' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'bar-chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'upload' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',
    'trending-up' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    'megaphone' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
    'settings' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'shield-lock' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
    'person-badge' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c2.21 0 4 1.343 4 3v1H6v-1c0-1.657 1.79-3 4-3z'
];
?>

<aside class="sidebar-fixed">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="<?php echo URLROOT; ?>/assets/images/signed-logo.png" alt="SignED Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div class="sidebar-logo-text" style="display: none;">
                <h2>SignED</h2>
                <p>SPED System</p>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <?php foreach ($navigationItems as $item): ?>
            <a href="<?php echo $item['url']; ?>" 
               class="sidebar-nav-item <?php echo ($currentPage === $item['page']) ? 'active' : ''; ?>">
                <div class="sidebar-nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $icons[$item['icon']] ?? $icons['home']; ?>" />
                    </svg>
                </div>
                <span class="sidebar-nav-text"><?php echo htmlspecialchars($item['title']); ?></span>
                <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                    <span class="sidebar-nav-badge"><?php echo $item['badge']; ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <?php echo strtoupper(substr($data['user_name'], 0, 1)); ?>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo htmlspecialchars($data['user_name']); ?></div>
                <div class="sidebar-user-role"><?php echo ucfirst(str_replace('_', ' ', $data['role'])); ?></div>
            </div>
        </div>
        <a href="<?php echo URLROOT; ?>/auth/logout" class="sidebar-logout">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
</aside>

<style>
/* Fixed Sidebar Styles */
.sidebar-fixed {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 280px;
    background: white;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    z-index: 1000;
    overflow-y: auto;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-logo img {
    max-width: 100%;
    height: auto;
    max-height: 60px;
}

.sidebar-logo-text h2 {
    font-size: 1.5rem;
    font-weight: bold;
    background: linear-gradient(135deg, #B91C3C, #1E40AF);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.sidebar-logo-text p {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
}

.sidebar-nav-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    text-decoration: none;
    color: #374151;
    border-radius: 8px;
    transition: all 0.2s;
    position: relative;
}

.sidebar-nav-item:hover {
    background: #f3f4f6;
    color: #B91C3C;
}

.sidebar-nav-item.active {
    background: linear-gradient(135deg, #B91C3C, #1E40AF);
    color: white;
}

.sidebar-nav-icon {
    width: 24px;
    height: 24px;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.sidebar-nav-icon svg {
    width: 100%;
    height: 100%;
}

.sidebar-nav-text {
    flex: 1;
    font-weight: 500;
}

.sidebar-nav-badge {
    background: #ef4444;
    color: white;
    font-size: 0.75rem;
    padding: 0.125rem 0.5rem;
    border-radius: 10px;
    min-width: 1.25rem;
    text-align: center;
    font-weight: 600;
}

.sidebar-nav-item.active .sidebar-nav-badge {
    background: rgba(255, 255, 255, 0.3);
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
}

.sidebar-user {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}

.sidebar-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #B91C3C, #1E40AF);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.125rem;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.sidebar-user-info {
    flex: 1;
    min-width: 0;
}

.sidebar-user-name {
    font-weight: 600;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-user-role {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: capitalize;
}

.sidebar-logout {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: #6b7280;
    border-radius: 8px;
    transition: all 0.2s;
}

.sidebar-logout:hover {
    background: #fee2e2;
    color: #991b1b;
}

.sidebar-logout svg {
    width: 20px;
    height: 20px;
    margin-right: 0.75rem;
}

/* Main content adjustment when sidebar is present */
.main-content-with-sidebar {
    margin-left: 280px;
    min-height: 100vh;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar-fixed {
        transform: translateX(-100%);
        transition: transform 0.3s;
    }
    
    .sidebar-fixed.mobile-open {
        transform: translateX(0);
    }
    
    .main-content-with-sidebar {
        margin-left: 0;
    }
    
    /* Mobile menu toggle button */
    .mobile-menu-toggle {
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: white;
        border: none;
        padding: 0.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }
}

/* Scrollbar styling for sidebar */
.sidebar-fixed::-webkit-scrollbar {
    width: 6px;
}

.sidebar-fixed::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.sidebar-fixed::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.sidebar-fixed::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    // Create mobile menu toggle button if on mobile
    if (window.innerWidth <= 768) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'mobile-menu-toggle';
        toggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        `;
        document.body.appendChild(toggleBtn);
        
        const sidebar = document.querySelector('.sidebar-fixed');
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }
});
</script>
