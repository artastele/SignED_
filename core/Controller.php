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
        $viewPath = '../app/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            $this->handleViewNotFound($view);
        }
    }

    public function model($model)
    {
        $modelPath = '../app/models/' . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            $this->handleModelNotFound($model);
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
            $this->handleAccessDenied('Insufficient permissions for this action.');
        }
    }

    // 🔒 check SPED roles (multiple roles allowed)
    public function requireSpedRole($allowedRoles = [])
    {
        $this->requireLogin();
        $this->checkSessionTimeout();

        if (!isset($_SESSION['role'])) {
            $this->handleAccessDenied('No role assigned to your account.');
        }

        if (!in_array($_SESSION['role'], $allowedRoles)) {
            $this->handleAccessDenied('Insufficient permissions for this action.');
        }
    }

    // 🔒 check if user has any of the specified roles
    public function requireAnyRole($allowedRoles = [])
    {
        $this->requireLogin();
        $this->checkSessionTimeout();

        if (!isset($_SESSION['role'])) {
            $this->handleAccessDenied('No role assigned to your account.');
        }

        if (!in_array($_SESSION['role'], $allowedRoles)) {
            $this->handleAccessDenied('Insufficient permissions for this action.');
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

    /**
     * Handle view not found error
     */
    protected function handleViewNotFound($view)
    {
        http_response_code(500);
        $this->logError("View not found: $view");
        
        if (APP_ENV === 'production') {
            $this->showErrorPage('Page Not Found', 'The requested page could not be found.');
        } else {
            die("View does not exist: $view");
        }
    }

    /**
     * Handle model not found error
     */
    protected function handleModelNotFound($model)
    {
        http_response_code(500);
        $this->logError("Model not found: $model");
        
        if (APP_ENV === 'production') {
            $this->showErrorPage('System Error', 'A system error occurred. Please try again later.');
        } else {
            die("Model does not exist: $model");
        }
    }

    /**
     * Handle access denied error
     */
    protected function handleAccessDenied($message = 'Access denied')
    {
        http_response_code(403);
        $this->logError("Access denied: $message - User: " . $this->getCurrentUserId());
        
        $this->showErrorPage('Access Denied', $message);
    }

    /**
     * Log error to file
     */
    protected function logError($message)
    {
        $logDir = dirname(__DIR__) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $userId = $this->getCurrentUserId() ?? 'guest';
        $ip = $this->getUserIpAddress();
        
        $logMessage = "[$timestamp] User: $userId | IP: $ip | Error: $message\n";
        error_log($logMessage, 3, $logFile);
    }

    /**
     * Show user-friendly error page
     */
    protected function showErrorPage($title, $message)
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($title); ?> - SignED</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #a01422 0%, #1e4072 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 20px;
                    padding: 40px;
                    max-width: 500px;
                    text-align: center;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                }
                .error-icon {
                    font-size: 64px;
                    margin-bottom: 20px;
                }
                .error-403 { color: #f59e0b; }
                .error-404 { color: #3b82f6; }
                .error-500 { color: #ef4444; }
                h1 {
                    color: #1e293b;
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                p {
                    color: #64748b;
                    line-height: 1.6;
                    margin-bottom: 20px;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 30px;
                    background: linear-gradient(135deg, #a01422 0%, #8a1119 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 10px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    margin: 5px;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(160, 20, 34, 0.4);
                }
                .btn-secondary {
                    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon error-<?php echo http_response_code(); ?>">
                    <?php if (http_response_code() == 403): ?>
                        🔒
                    <?php elseif (http_response_code() == 404): ?>
                        🔍
                    <?php else: ?>
                        ⚠️
                    <?php endif; ?>
                </div>
                <h1><?php echo htmlspecialchars($title); ?></h1>
                <p><?php echo htmlspecialchars($message); ?></p>
                <div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
                        <a href="<?php echo URLROOT; ?>/<?php echo $_SESSION['role'] ?? 'user'; ?>/dashboard" class="btn">Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo URLROOT; ?>/auth/login" class="btn">Return to Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
