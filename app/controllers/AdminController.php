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

        $data = [
            'role' => $_SESSION['role'],
            'current_page' => 'settings',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'System Settings - SignED SPED'
        ];

        $this->view('admin/settings', $data);
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
     * System announcements
     */
    public function announcements()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $data = [
            'role' => $_SESSION['role'],
            'current_page' => 'announcements',
            'user_name' => $_SESSION['fullname'] ?? 'Admin',
            'page_title' => 'Announcements - SignED SPED'
        ];

        $this->view('admin/announcements', $data);
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