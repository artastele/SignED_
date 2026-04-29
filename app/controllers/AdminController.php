<?php

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        // Get system statistics
        $userModel = $this->model('User');
        $learnerModel = $this->model('Learner');
        $enrollmentModel = $this->model('Enrollment');
        $assessmentModel = $this->model('Assessment');
        $iepModel = $this->model('Iep');

        $data = [
            'role' => $_SESSION['role'],
            'current_page' => 'dashboard',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'Admin Dashboard - SignED SPED',
            
            // System Statistics
            'total_users' => $userModel->getTotalCount(),
            'total_learners' => $learnerModel->getTotalCount(),
            'pending_enrollments' => $enrollmentModel->getPendingVerificationCount(),
            'pending_assessments' => $assessmentModel->getPendingCount(),
            'pending_iep_approvals' => $iepModel->getPendingApprovalCount(),
            'active_ieps' => $iepModel->getActiveCount(),
            
            // User breakdown by role
            'users_by_role' => $userModel->getUserCountByRole(),
            
            // Recent activity
            'recent_users' => $userModel->getRecentUsers(5),
            'recent_enrollments' => $enrollmentModel->getRecent(5),
            
            // System health
            'system_status' => $this->getSystemStatus()
        ];

        $this->view('admin/dashboard', $data);
    }

    public function users()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $userModel = $this->model('User');
        $users = $userModel->getAllUsers();

        $data = [
            'users' => $users,
            'role' => $_SESSION['role'],
            'current_page' => 'users',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'User Management - SignED SPED'
        ];

        $this->view('admin/users', $data);
    }

    public function updateRole($id = null)
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $role = $_POST['role'];

            $allowedRoles = ['admin', 'teacher', 'parent', 'sped_teacher', 'guidance', 'principal', 'learner'];

            if (!in_array($role, $allowedRoles)) {
                die('Invalid role.');
            }

            $userModel = $this->model('User');

            if ($userModel->updateUserRole($id, $role)) {
                header('Location: ' . URLROOT . '/admin/users?success=role_updated');
                exit;
            } else {
                die('Failed to update role.');
            }
        } else {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
    }

    public function deleteUser($id = null)
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($id === null) {
            die('User ID is required.');
        }

        $userModel = $this->model('User');

        if ($userModel->deleteUser($id)) {
            header('Location: ' . URLROOT . '/admin/users?success=user_deleted');
            exit;
        } else {
            die('Failed to delete user.');
        }
    }

    /**
     * System settings page
     */
    public function settings()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $settingsModel = $this->model('SystemSettings');
        $allSettings = $settingsModel->getAll();

        $data = [
            'role' => $_SESSION['role'],
            'current_page' => 'settings',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'System Settings - SignED SPED',
            'settings' => $allSettings
        ];

        $this->view('admin/settings', $data);
    }

    /**
     * Update system settings
     */
    public function updateSettings()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/settings');
            exit;
        }

        $settingsModel = $this->model('SystemSettings');
        $auditLog = $this->model('AuditLog');
        
        $settings = [
            'system_name' => ['value' => $_POST['system_name'] ?? 'SignED SPED System', 'type' => 'string'],
            'system_email' => ['value' => $_POST['system_email'] ?? 'admin@signed.edu', 'type' => 'string'],
            'timezone' => ['value' => $_POST['timezone'] ?? 'Asia/Manila', 'type' => 'string'],
            'session_timeout' => ['value' => $_POST['session_timeout'] ?? 30, 'type' => 'number'],
            'max_login_attempts' => ['value' => $_POST['max_login_attempts'] ?? 5, 'type' => 'number'],
            'require_email_verification' => ['value' => isset($_POST['require_email_verification']) ? 1 : 0, 'type' => 'boolean'],
            'enable_audit_logging' => ['value' => isset($_POST['enable_audit_logging']) ? 1 : 0, 'type' => 'boolean'],
            'smtp_host' => ['value' => $_POST['smtp_host'] ?? 'smtp.gmail.com', 'type' => 'string'],
            'smtp_port' => ['value' => $_POST['smtp_port'] ?? 587, 'type' => 'number'],
            'smtp_from_email' => ['value' => $_POST['smtp_from_email'] ?? 'noreply@signed.edu', 'type' => 'string']
        ];

        $result = $settingsModel->updateBatch($settings, $_SESSION['user_id']);

        if ($result) {
            $auditLog->logAction(
                $_SESSION['user_id'],
                'settings_update',
                'System settings updated'
            );

            header('Location: ' . URLROOT . '/admin/settings?success=Settings updated successfully');
        } else {
            header('Location: ' . URLROOT . '/admin/settings?error=Failed to update settings');
        }
        exit;
    }

    /**
     * Audit logs page
     */
    public function logs()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $auditLogModel = $this->model('AuditLog');
        $logs = $auditLogModel->getRecent(50);

        $data = [
            'logs' => $logs,
            'role' => $_SESSION['role'],
            'current_page' => 'logs',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'Audit Logs - SignED SPED'
        ];

        $this->view('admin/logs', $data);
    }

    /**
     * Login attempts logs page
     */
    public function loginAttempts()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        // Get login attempts from cache files
        $loginAttempts = $this->getLoginAttemptsFromCache();

        // Get login/logout logs from audit_logs table
        $auditLogModel = $this->model('AuditLog');
        $filters = ['action_type' => 'login'];
        $loginLogs = $auditLogModel->query($filters, 100, 0);

        $data = [
            'login_attempts' => $loginAttempts,
            'login_logs' => $loginLogs['data'] ?? [],
            'role' => $_SESSION['role'],
            'current_page' => 'login_attempts',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'Login Attempts - SignED SPED'
        ];

        $this->view('admin/login_attempts', $data);
    }

    /**
     * Get login attempts from cache files
     */
    private function getLoginAttemptsFromCache()
    {
        $attempts = [];
        $cacheDir = '../cache/';

        if (!is_dir($cacheDir)) {
            return $attempts;
        }

        $files = glob($cacheDir . 'login_attempts_*.txt');

        foreach ($files as $file) {
            $data = file_get_contents($file);
            if ($data) {
                list($count, $lastAttempt) = explode('|', $data);
                
                // Extract email hash from filename
                $filename = basename($file);
                $emailHash = str_replace(['login_attempts_', '.txt'], '', $filename);
                
                // Calculate lockout status
                $isLocked = false;
                $lockoutRemaining = 0;
                
                if ($count >= 5) {
                    $lockoutEnd = $lastAttempt + 1800; // 30 minutes
                    if (time() < $lockoutEnd) {
                        $isLocked = true;
                        $lockoutRemaining = $lockoutEnd - time();
                    }
                }

                $attempts[] = [
                    'email_hash' => $emailHash,
                    'attempt_count' => $count,
                    'last_attempt' => date('Y-m-d H:i:s', $lastAttempt),
                    'last_attempt_timestamp' => $lastAttempt,
                    'is_locked' => $isLocked,
                    'lockout_remaining' => $lockoutRemaining,
                    'file_path' => $file
                ];
            }
        }

        // Sort by last attempt (most recent first)
        usort($attempts, function($a, $b) {
            return $b['last_attempt_timestamp'] - $a['last_attempt_timestamp'];
        });

        return $attempts;
    }

    /**
     * Clear login attempts for a specific email hash
     */
    public function clearLoginAttempts()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/loginAttempts');
            exit;
        }

        $emailHash = $_POST['email_hash'] ?? null;

        if (!$emailHash) {
            header('Location: ' . URLROOT . '/admin/loginAttempts?error=Invalid email hash');
            exit;
        }

        $cacheFile = '../cache/login_attempts_' . $emailHash . '.txt';

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            
            // Log the action
            $auditLog = $this->model('AuditLog');
            $auditLog->logAction(
                $_SESSION['user_id'],
                'clear_login_attempts',
                'security',
                null,
                'Cleared login attempts for hash: ' . $emailHash
            );

            header('Location: ' . URLROOT . '/admin/loginAttempts?success=Login attempts cleared successfully');
        } else {
            header('Location: ' . URLROOT . '/admin/loginAttempts?error=Login attempts file not found');
        }
        exit;
    }

    /**
     * Admin activity logs page
     */
    public function adminActivity()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $auditLogModel = $this->model('AuditLog');
        
        // Get all admin users
        $userModel = $this->model('User');
        $sql = "SELECT id, fullname, email FROM users WHERE role = 'admin'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $adminUsers = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Get admin activity logs
        $adminIds = array_column((array)$adminUsers, 'id');
        
        $sql = "SELECT al.*, u.fullname, u.email, u.role
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.user_id IN (" . implode(',', array_map('intval', $adminIds)) . ")
                ORDER BY al.created_at DESC
                LIMIT 100";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $adminLogs = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = [
            'admin_users' => $adminUsers,
            'admin_logs' => $adminLogs,
            'role' => $_SESSION['role'],
            'current_page' => 'admin_activity',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'Admin Activity - SignED SPED'
        ];

        $this->view('admin/admin_activity', $data);
    }

    /**
     * System announcements
     */
    public function announcements()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $announcementModel = $this->model('Announcement');
        $announcements = $announcementModel->getAll();

        $data = [
            'role' => $_SESSION['role'],
            'current_page' => 'announcements',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'Announcements - SignED SPED',
            'announcements' => $announcements
        ];

        $this->view('admin/announcements', $data);
    }

    /**
     * Create announcement
     */
    public function createAnnouncement()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/announcements');
            exit;
        }

        $announcementModel = $this->model('Announcement');
        $auditLog = $this->model('AuditLog');
        $notificationService = $this->model('NotificationService');

        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'priority' => $_POST['priority'] ?? 'normal',
            'target_audience' => $_POST['target_audience'] ?? 'all',
            'created_by' => $_SESSION['user_id'],
            'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null
        ];

        $result = $announcementModel->create($data);

        if ($result) {
            // Log action
            $auditLog->logAction(
                $_SESSION['user_id'],
                'announcement_create',
                'announcement',
                $result,
                'Created announcement: ' . $data['title']
            );

            // Send notifications to target users
            $this->sendAnnouncementNotifications($result, $data);

            header('Location: ' . URLROOT . '/admin/announcements?success=Announcement created successfully');
        } else {
            header('Location: ' . URLROOT . '/admin/announcements?error=Failed to create announcement');
        }
        exit;
    }

    /**
     * Send notifications for new announcement
     */
    private function sendAnnouncementNotifications($announcementId, $announcementData)
    {
        try {
            $userModel = $this->model('User');
            $notificationService = $this->model('NotificationService');
            
            // Get target users based on audience
            $targetUsers = $this->getTargetUsers($announcementData['target_audience']);
            
            // Create notification message
            $notificationTitle = 'New Announcement: ' . $announcementData['title'];
            $notificationMessage = substr($announcementData['content'], 0, 100) . '...';
            $notificationLink = URLROOT . '/announcements/view/' . $announcementId;
            
            // Priority icon
            $priorityIcon = [
                'urgent' => '🚨',
                'high' => '⚠️',
                'normal' => 'ℹ️',
                'low' => '📢'
            ];
            $icon = $priorityIcon[$announcementData['priority']] ?? 'ℹ️';
            
            // Send notification to each target user
            foreach ($targetUsers as $user) {
                $notificationService->create([
                    'user_id' => $user->id,
                    'type' => 'announcement',
                    'title' => $icon . ' ' . $notificationTitle,
                    'message' => $notificationMessage,
                    'link' => $notificationLink,
                    'priority' => $announcementData['priority']
                ]);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error sending announcement notifications: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get target users based on audience
     */
    private function getTargetUsers($targetAudience)
    {
        $userModel = $this->model('User');
        
        // Map target audience to roles
        $roleMapping = [
            'all' => ['admin', 'teacher', 'parent', 'sped_teacher', 'guidance', 'principal', 'learner'],
            'parents' => ['parent'],
            'teachers' => ['teacher'],
            'sped_staff' => ['sped_teacher', 'guidance', 'principal'],
            'learners' => ['learner'],
            'admins' => ['admin']
        ];
        
        $roles = $roleMapping[$targetAudience] ?? ['admin', 'teacher', 'parent', 'sped_teacher', 'guidance', 'principal', 'learner'];
        
        // Get users with target roles
        $sql = "SELECT id, email, fullname, role 
                FROM users 
                WHERE role IN ('" . implode("','", $roles) . "') 
                AND is_verified = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update announcement
     */
    public function updateAnnouncement()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/announcements');
            exit;
        }

        $announcementModel = $this->model('Announcement');
        $auditLog = $this->model('AuditLog');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            header('Location: ' . URLROOT . '/admin/announcements?error=Invalid announcement ID');
            exit;
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'priority' => $_POST['priority'] ?? 'normal',
            'target_audience' => $_POST['target_audience'] ?? 'all',
            'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null
        ];

        $result = $announcementModel->update($id, $data);

        if ($result) {
            $auditLog->logAction(
                $_SESSION['user_id'],
                'announcement_update',
                'Updated announcement ID: ' . $id
            );

            header('Location: ' . URLROOT . '/admin/announcements?success=Announcement updated successfully');
        } else {
            header('Location: ' . URLROOT . '/admin/announcements?error=Failed to update announcement');
        }
        exit;
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement($id = null)
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if (!$id) {
            header('Location: ' . URLROOT . '/admin/announcements?error=Invalid announcement ID');
            exit;
        }

        $announcementModel = $this->model('Announcement');
        $auditLog = $this->model('AuditLog');

        $result = $announcementModel->delete($id);

        if ($result) {
            $auditLog->logAction(
                $_SESSION['user_id'],
                'announcement_delete',
                'Deleted announcement ID: ' . $id
            );

            header('Location: ' . URLROOT . '/admin/announcements?success=Announcement deleted successfully');
        } else {
            header('Location: ' . URLROOT . '/admin/announcements?error=Failed to delete announcement');
        }
        exit;
    }

    /**
     * Toggle announcement active status
     */
    public function toggleAnnouncement($id = null)
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if (!$id) {
            header('Location: ' . URLROOT . '/admin/announcements?error=Invalid announcement ID');
            exit;
        }

        $announcementModel = $this->model('Announcement');
        $result = $announcementModel->toggleActive($id);

        if ($result) {
            header('Location: ' . URLROOT . '/admin/announcements?success=Announcement status updated');
        } else {
            header('Location: ' . URLROOT . '/admin/announcements?error=Failed to update status');
        }
        exit;
    }

    /**
     * Get system status
     */
    private function getSystemStatus()
    {
        return [
            'database' => $this->checkDatabaseConnection(),
            'storage' => $this->checkStorageSpace(),
            'php_version' => phpversion(),
            'server_time' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection()
    {
        try {
            $this->db->query("SELECT 1");
            return 'Connected';
        } catch (Exception $e) {
            return 'Error';
        }
    }

    /**
     * Check storage space
     */
    private function checkStorageSpace()
    {
        $free = disk_free_space(".");
        $total = disk_total_space(".");
        $used = $total - $free;
        $percent = round(($used / $total) * 100, 2);
        
        return [
            'used' => $this->formatBytes($used),
            'total' => $this->formatBytes($total),
            'percent' => $percent
        ];
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}