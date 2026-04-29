<?php
/**
 * FINAL COMPREHENSIVE TEST
 * Tests everything before accessing assessment
 */

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
    <title>Final Test - Assessment Access</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .test-box { background: #f8f9fa; padding: 20px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .test-box.pass { border-left-color: #28a745; }
        .test-box.fail { border-left-color: #dc3545; }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        .status-icon { font-size: 24px; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Final Assessment Access Test</h1>
        <p style="color: #666; font-size: 14px;">This test verifies all requirements before accessing the assessment page.</p>
        
        <?php
        $allTestsPassed = true;
        $testResults = [];
        
        // Test 1: Session Check
        $test1Pass = isset($_SESSION['user_id']);
        $testResults[] = [
            'name' => 'Session Active',
            'pass' => $test1Pass,
            'message' => $test1Pass ? 'User is logged in' : 'User is NOT logged in'
        ];
        if (!$test1Pass) $allTestsPassed = false;
        
        // Test 2: Parent Role Check
        $test2Pass = isset($_SESSION['role']) && $_SESSION['role'] === 'parent';
        $testResults[] = [
            'name' => 'Parent Role',
            'pass' => $test2Pass,
            'message' => $test2Pass ? 'User is a parent' : 'User is NOT a parent (Role: ' . ($_SESSION['role'] ?? 'NONE') . ')'
        ];
        if (!$test2Pass) $allTestsPassed = false;
        
        // Test 3: Configuration Check
        $test3Pass = (URLROOT === 'http://localhost/SignED_');
        $testResults[] = [
            'name' => 'URLROOT Config',
            'pass' => $test3Pass,
            'message' => $test3Pass ? 'URLROOT is correct' : 'URLROOT is wrong: ' . URLROOT
        ];
        if (!$test3Pass) $allTestsPassed = false;
        
        // Test 4: Session Path Check
        $sessionPath = session_get_cookie_params()['path'];
        $test4Pass = ($sessionPath === '/SignED_/');
        $testResults[] = [
            'name' => 'Session Cookie Path',
            'pass' => $test4Pass,
            'message' => $test4Pass ? 'Session path is correct' : 'Session path is wrong: ' . $sessionPath
        ];
        if (!$test4Pass) $allTestsPassed = false;
        
        // Test 5: .htaccess Files
        $test5Pass = file_exists('../.htaccess') && file_exists('.htaccess');
        $testResults[] = [
            'name' => '.htaccess Files',
            'pass' => $test5Pass,
            'message' => $test5Pass ? 'Both .htaccess files exist' : 'Missing .htaccess files'
        ];
        if (!$test5Pass) $allTestsPassed = false;
        
        // Display Results
        foreach ($testResults as $result) {
            $class = $result['pass'] ? 'pass' : 'fail';
            $icon = $result['pass'] ? '✅' : '❌';
            echo "<div class='test-box {$class}'>";
            echo "<span class='status-icon'>{$icon}</span>";
            echo "<strong>{$result['name']}:</strong> ";
            echo $result['pass'] ? "<span class='success'>{$result['message']}</span>" : "<span class='error'>{$result['message']}</span>";
            echo "</div>";
        }
        ?>
        
        <hr style="margin: 30px 0;">
        
        <?php if ($allTestsPassed): ?>
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 5px; text-align: center;">
                <h2 style="margin: 0 0 15px 0;">🎉 All Tests Passed!</h2>
                <p style="margin: 0 0 20px 0;">You should be able to access the assessment page now.</p>
                <a href="<?php echo URLROOT; ?>/assessment" class="btn btn-success">
                    🚀 Go to Assessment Page
                </a>
            </div>
        <?php else: ?>
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 5px; text-align: center;">
                <h2 style="margin: 0 0 15px 0;">⚠️ Some Tests Failed</h2>
                <p style="margin: 0;">Please fix the issues above before accessing the assessment page.</p>
            </div>
        <?php endif; ?>
        
        <hr style="margin: 30px 0;">
        
        <h2>📊 Session Information</h2>
        <table>
            <tr>
                <th>Key</th>
                <th>Value</th>
            </tr>
            <?php if (isset($_SESSION['user_id'])): ?>
                <tr><td>User ID</td><td><?php echo $_SESSION['user_id']; ?></td></tr>
                <tr><td>Full Name</td><td><?php echo $_SESSION['fullname'] ?? 'N/A'; ?></td></tr>
                <tr><td>Email</td><td><?php echo $_SESSION['email'] ?? 'N/A'; ?></td></tr>
                <tr><td>Role</td><td><?php echo $_SESSION['role'] ?? 'N/A'; ?></td></tr>
                <tr><td>Session ID</td><td><?php echo session_id(); ?></td></tr>
            <?php else: ?>
                <tr><td colspan="2" style="text-align: center; color: #dc3545;">No session data - Please login</td></tr>
            <?php endif; ?>
        </table>
        
        <h2>🔧 Configuration</h2>
        <table>
            <tr><td>URLROOT</td><td><?php echo URLROOT; ?></td></tr>
            <tr><td>ASSETS</td><td><?php echo defined('ASSETS') ? ASSETS : 'NOT DEFINED'; ?></td></tr>
            <tr><td>Session Path</td><td><?php echo session_get_cookie_params()['path']; ?></td></tr>
            <tr><td>Session Lifetime</td><td><?php echo session_get_cookie_params()['lifetime']; ?> seconds</td></tr>
        </table>
        
        <hr style="margin: 30px 0;">
        
        <h2>🔗 Quick Links</h2>
        <div style="text-align: center;">
            <a href="<?php echo URLROOT; ?>/auth/login" class="btn">Login</a>
            <a href="<?php echo URLROOT; ?>/parent/dashboard" class="btn">Parent Dashboard</a>
            <a href="<?php echo URLROOT; ?>/assessment" class="btn btn-success">Assessment Page</a>
            <a href="<?php echo URLROOT; ?>/auth/logout" class="btn" style="background: #dc3545;">Logout</a>
        </div>
        
        <hr style="margin: 30px 0;">
        
        <h2>📝 Summary of Changes Made</h2>
        <ol style="line-height: 2;">
            <li><strong>Fixed URLROOT:</strong> Changed from <code>/SignED_/public</code> to <code>/SignED_/</code></li>
            <li><strong>Fixed Session Path:</strong> Changed from <code>/SignED_/public/</code> to <code>/SignED_/</code></li>
            <li><strong>Added ASSETS constant:</strong> Points to <code>/SignED_/public/assets</code></li>
            <li><strong>Updated .htaccess:</strong> Proper routing to public folder</li>
            <li><strong>Removed custom requireParent():</strong> Now uses base Controller method</li>
            <li><strong>Fixed header/footer:</strong> Uses ASSETS for CSS/JS files</li>
        </ol>
        
    </div>
</body>
</html>
