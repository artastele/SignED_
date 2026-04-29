<?php
session_start();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .info { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #eee; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Session Debug Information</h1>
    
    <div class="info">
        <h2>Session Status</h2>
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? '<span class="success">ACTIVE</span>' : '<span class="error">INACTIVE</span>'; ?></p>
    </div>
    
    <div class="info">
        <h2>Session Data</h2>
        <?php if (empty($_SESSION)): ?>
            <p class="error">No session data found!</p>
        <?php else: ?>
            <pre><?php print_r($_SESSION); ?></pre>
        <?php endif; ?>
    </div>
    
    <div class="info">
        <h2>Session Configuration</h2>
        <pre><?php
        echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "\n";
        echo "session.cookie_path: " . ini_get('session.cookie_path') . "\n";
        echo "session.cookie_domain: " . ini_get('session.cookie_domain') . "\n";
        echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";
        echo "session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "\n";
        echo "session.save_path: " . ini_get('session.save_path') . "\n";
        ?></pre>
    </div>
    
    <div class="info">
        <h2>Cookie Information</h2>
        <?php if (empty($_COOKIE)): ?>
            <p class="error">No cookies found!</p>
        <?php else: ?>
            <pre><?php print_r($_COOKIE); ?></pre>
        <?php endif; ?>
    </div>
    
    <div class="info">
        <h2>Test Session</h2>
        <p>
            <a href="?action=set">Set Test Session Data</a> | 
            <a href="?action=clear">Clear Session</a> | 
            <a href="session_debug.php">Refresh</a>
        </p>
        
        <?php
        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'set') {
                $_SESSION['test_data'] = 'Session is working! Time: ' . date('Y-m-d H:i:s');
                echo '<p class="success">Test session data set!</p>';
            } elseif ($_GET['action'] === 'clear') {
                session_unset();
                session_destroy();
                echo '<p class="success">Session cleared!</p>';
            }
        }
        ?>
    </div>
    
    <div class="info">
        <h2>Quick Links</h2>
        <p>
            <a href="../public/index.php">Home</a> | 
            <a href="../public/index.php/auth/login">Login</a> | 
            <a href="../public/index.php/parent/dashboard">Parent Dashboard</a> | 
            <a href="../public/index.php/assessment">Assessment</a>
        </p>
    </div>
</body>
</html>
