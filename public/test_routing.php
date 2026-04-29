<?php
/**
 * Routing and Session Test
 * Tests if the routing and session are working correctly
 */

// Start session with correct path
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/SignED_/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once '../config/config.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Routing & Session Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3; }
        .test-section { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>🔍 Routing & Session Diagnostic Test</h1>
    
    <div class="test-section">
        <h2>1. Configuration Check</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>URLROOT</td>
                <td><?php echo URLROOT; ?></td>
                <td><?php echo (URLROOT === 'http://localhost/SignED_') ? '<span class="success">✅ CORRECT</span>' : '<span class="error">❌ WRONG (should be http://localhost/SignED_)</span>'; ?></td>
            </tr>
            <tr>
                <td>Session Path</td>
                <td><?php echo session_get_cookie_params()['path']; ?></td>
                <td><?php echo (session_get_cookie_params()['path'] === '/SignED_/') ? '<span class="success">✅ CORRECT</span>' : '<span class="error">❌ WRONG (should be /SignED_/)</span>'; ?></td>
            </tr>
            <tr>
                <td>Session ID</td>
                <td><?php echo session_id(); ?></td>
                <td><?php echo session_id() ? '<span class="success">✅ Active</span>' : '<span class="error">❌ Not Active</span>'; ?></td>
            </tr>
        </table>
    </div>
    
    <div class="test-section">
        <h2>2. Session Data Check</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="success">✅ User is LOGGED IN</p>
            <table>
                <tr>
                    <th>Session Key</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>user_id</td>
                    <td><?php echo $_SESSION['user_id']; ?></td>
                </tr>
                <tr>
                    <td>role</td>
                    <td><?php echo $_SESSION['role'] ?? 'NOT SET'; ?></td>
                </tr>
                <tr>
                    <td>fullname</td>
                    <td><?php echo $_SESSION['fullname'] ?? 'NOT SET'; ?></td>
                </tr>
                <tr>
                    <td>email</td>
                    <td><?php echo $_SESSION['email'] ?? 'NOT SET'; ?></td>
                </tr>
            </table>
            
            <?php if ($_SESSION['role'] === 'parent'): ?>
                <p class="success">✅ User is a PARENT - Can access assessment</p>
            <?php else: ?>
                <p class="error">❌ User is NOT a parent (Role: <?php echo $_SESSION['role']; ?>)</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="error">❌ User is NOT logged in</p>
            <p>Please <a href="<?php echo URLROOT; ?>/auth/login">login here</a></p>
        <?php endif; ?>
    </div>
    
    <div class="test-section">
        <h2>3. URL Generation Test</h2>
        <table>
            <tr>
                <th>Link</th>
                <th>Generated URL</th>
                <th>Test</th>
            </tr>
            <tr>
                <td>Assessment Index</td>
                <td><?php echo URLROOT . '/assessment'; ?></td>
                <td><a href="<?php echo URLROOT; ?>/assessment" target="_blank">Test Link</a></td>
            </tr>
            <tr>
                <td>Parent Dashboard</td>
                <td><?php echo URLROOT . '/parent/dashboard'; ?></td>
                <td><a href="<?php echo URLROOT; ?>/parent/dashboard" target="_blank">Test Link</a></td>
            </tr>
            <tr>
                <td>Login</td>
                <td><?php echo URLROOT . '/auth/login'; ?></td>
                <td><a href="<?php echo URLROOT; ?>/auth/login" target="_blank">Test Link</a></td>
            </tr>
        </table>
    </div>
    
    <div class="test-section">
        <h2>4. Expected vs Actual URLs</h2>
        <div class="info">
            <strong>Expected URL Structure:</strong><br>
            - Base: http://localhost/SignED_/<br>
            - Assessment: http://localhost/SignED_/assessment<br>
            - Dashboard: http://localhost/SignED_/parent/dashboard<br><br>
            
            <strong>Session Cookie Path:</strong> /SignED_/<br>
            <strong>This means:</strong> Session will work for ALL URLs starting with /SignED_/
        </div>
    </div>
    
    <div class="test-section">
        <h2>5. Troubleshooting Steps</h2>
        <?php
        $issues = [];
        
        if (URLROOT !== 'http://localhost/SignED_') {
            $issues[] = "URLROOT is incorrect. Update config/config.php";
        }
        
        if (session_get_cookie_params()['path'] !== '/SignED_/') {
            $issues[] = "Session path is incorrect. Update public/index.php";
        }
        
        if (!isset($_SESSION['user_id'])) {
            $issues[] = "Not logged in. Please login first.";
        } elseif ($_SESSION['role'] !== 'parent') {
            $issues[] = "Not logged in as parent. Current role: " . $_SESSION['role'];
        }
        
        if (empty($issues)): ?>
            <p class="success">✅ Everything looks good! Assessment page should work now.</p>
            <p><a href="<?php echo URLROOT; ?>/assessment" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Go to Assessment Page</a></p>
        <?php else: ?>
            <p class="error">❌ Found <?php echo count($issues); ?> issue(s):</p>
            <ol>
                <?php foreach ($issues as $issue): ?>
                    <li><?php echo $issue; ?></li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
    
    <div class="test-section">
        <h2>6. Quick Actions</h2>
        <p>
            <a href="<?php echo URLROOT; ?>/auth/logout">Logout</a> | 
            <a href="<?php echo URLROOT; ?>/auth/login">Login</a> | 
            <a href="<?php echo URLROOT; ?>/parent/dashboard">Parent Dashboard</a> | 
            <a href="<?php echo URLROOT; ?>/assessment">Assessment</a>
        </p>
    </div>
    
</body>
</html>
