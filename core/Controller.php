<?php

class Controller
{
    protected $db;

    public function __construct()
    {
        // Initialize database connection using existing Database class
        require_once '../config/database.php';
        $database = new Database();
        $this->db = $database->connect();
    }
    public function view($view, $data = [])
    {
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist.');
        }
    }

    public function model($model)
    {
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            return new $model();
        } else {
            die('Model does not exist.');
        }
    }

    // 🔒 check login
    public function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    // 🔒 alias for requireLogin (for consistency)
    public function requireAuth()
    {
        $this->requireLogin();
    }

    // 🔄 redirect to URL
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    // 🔒 check role
    public function requireRole($role)
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
            die('Access denied.');
        }
    }

    // 🔒 check SPED roles (multiple roles allowed)
    public function requireSpedRole($allowedRoles = [])
    {
        $this->requireLogin();
        $this->checkSessionTimeout();

        if (!isset($_SESSION['role'])) {
            die('Access denied: No role assigned.');
        }

        if (!in_array($_SESSION['role'], $allowedRoles)) {
            die('Access denied: Insufficient permissions.');
        }
    }

    // 🔒 check if user has any of the specified roles
    public function requireAnyRole($allowedRoles = [])
    {
        $this->requireLogin();
        $this->checkSessionTimeout();

        if (!isset($_SESSION['role'])) {
            die('Access denied: No role assigned.');
        }

        if (!in_array($_SESSION['role'], $allowedRoles)) {
            die('Access denied: Insufficient permissions.');
        }
    }

    // 🔒 check admin role
    public function requireAdmin()
    {
        $this->requireSpedRole(['admin']);
    }

    // 🔒 check SPED teacher role
    public function requireSpedTeacher()
    {
        $this->requireSpedRole(['sped_teacher']);
    }

    // 🔒 check guidance role
    public function requireGuidance()
    {
        $this->requireSpedRole(['guidance']);
    }

    // 🔒 check principal role
    public function requirePrincipal()
    {
        $this->requireSpedRole(['principal']);
    }

    // 🔒 check parent role
    public function requireParent()
    {
        $this->requireSpedRole(['parent']);
    }

    // 🔒 check learner role
    public function requireLearner()
    {
        $this->requireSpedRole(['learner']);
    }

    // 🔒 check SPED staff roles (teacher, guidance, principal)
    public function requireSpedStaff()
    {
        $this->requireSpedRole(['sped_teacher', 'guidance', 'principal', 'admin']);
    }

    // 🔒 check session timeout (15 minutes inactivity)
    public function checkSessionTimeout()
    {
        if (!isset($_SESSION['user_id'])) {
            return; // Not logged in, no timeout to check
        }

        $timeout = 15 * 60; // 15 minutes in seconds

        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                // Session expired
                session_unset();
                session_destroy();
                header('Location: ' . URLROOT . '/auth/login?timeout=1');
                exit;
            }
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
    }

    // 🔒 get current user ID
    public function getCurrentUserId()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    // 🔒 get current user role
    public function getCurrentUserRole()
    {
        return isset($_SESSION['role']) ? $_SESSION['role'] : null;
    }

    // 🔒 check if current user has specific role
    public function hasRole($role)
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    // 🔒 check if current user has any of the specified roles
    public function hasAnyRole($roles)
    {
        return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
    }

    // 🔒 get user's IP address
    public function getUserIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    // 🔒 get user agent
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    // 🔒 log audit event
    public function logAudit($actionType, $entityType = null, $entityId = null, $oldValue = null, $newValue = null)
    {
        $auditLog = $this->model('AuditLog');
        
        return $auditLog->log(
            $this->getCurrentUserId(),
            $actionType,
            $entityType,
            $entityId,
            $oldValue,
            $newValue,
            $this->getUserIpAddress(),
            $this->getUserAgent()
        );
    }
}