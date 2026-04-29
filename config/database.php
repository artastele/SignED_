<?php

// Load environment variables
require_once __DIR__ . '/env.php';

try {
    Env::load();
} catch (Exception $e) {
    // If .env doesn't exist, use defaults
}

class Database
{
    private $host;
    private $dbName;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->host = Env::get('DB_HOST', 'localhost');
        $this->dbName = Env::get('DB_NAME', 'signed_system');
        $this->username = Env::get('DB_USER', 'root');
        $this->password = Env::get('DB_PASS', '');
    }

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbName,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log error securely
            $this->logDatabaseError($e);
            
            // Show user-friendly error
            if (APP_ENV === 'production') {
                $this->showDatabaseError();
            } else {
                // In development, show detailed error
                die("Database Connection Failed: " . $e->getMessage());
            }
        }

        return $this->conn;
    }

    /**
     * Log database error securely
     */
    private function logDatabaseError($exception)
    {
        $logDir = dirname(__DIR__) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/database_errors.log';
        $timestamp = date('Y-m-d H:i:s');
        $message = "[$timestamp] Database Error: " . $exception->getMessage() . "\n";
        $message .= "Stack Trace: " . $exception->getTraceAsString() . "\n\n";

        error_log($message, 3, $logFile);
    }

    /**
     * Show user-friendly database error page
     */
    private function showDatabaseError()
    {
        http_response_code(503);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Service Unavailable - SignED</title>
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
                    color: #a01422;
                    margin-bottom: 20px;
                }
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
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(160, 20, 34, 0.4);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h1>Service Temporarily Unavailable</h1>
                <p>We're experiencing technical difficulties connecting to our database. Our team has been notified and is working to resolve the issue.</p>
                <p>Please try again in a few minutes.</p>
                <a href="<?php echo URLROOT; ?>/auth/login" class="btn">Return to Login</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
