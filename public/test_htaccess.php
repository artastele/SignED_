<?php
/**
 * Test if .htaccess is working
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>.htaccess Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>.htaccess Configuration Test</h1>
    <hr>
    
    <h2>1. Check if mod_rewrite is enabled</h2>
    <?php
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            echo "<p class='success'>✅ mod_rewrite is ENABLED</p>";
        } else {
            echo "<p class='error'>❌ mod_rewrite is NOT ENABLED</p>";
            echo "<p><strong>Solution:</strong> Enable mod_rewrite in Apache:</p>";
            echo "<ol>";
            echo "<li>Open <code>C:\\xampp\\apache\\conf\\httpd.conf</code></li>";
            echo "<li>Find and uncomment: <code>LoadModule rewrite_module modules/mod_rewrite.so</code></li>";
            echo "<li>Restart Apache</li>";
            echo "</ol>";
        }
    } else {
        echo "<p class='warning'>⚠️ Cannot check (apache_get_modules not available)</p>";
    }
    ?>
    
    <h2>2. Check .htaccess files</h2>
    <?php
    $rootHtaccess = '../.htaccess';
    $publicHtaccess = '.htaccess';
    
    if (file_exists($rootHtaccess)) {
        echo "<p class='success'>✅ Root .htaccess exists</p>";
        echo "<pre>" . htmlspecialchars(file_get_contents($rootHtaccess)) . "</pre>";
    } else {
        echo "<p class='error'>❌ Root .htaccess NOT FOUND</p>";
    }
    
    if (file_exists($publicHtaccess)) {
        echo "<p class='success'>✅ Public .htaccess exists</p>";
        echo "<pre>" . htmlspecialchars(file_get_contents($publicHtaccess)) . "</pre>";
    } else {
        echo "<p class='error'>❌ Public .htaccess NOT FOUND</p>";
    }
    ?>
    
    <h2>3. Check AllowOverride setting</h2>
    <p>For .htaccess to work, Apache must have <code>AllowOverride All</code> set.</p>
    <p><strong>To check/fix:</strong></p>
    <ol>
        <li>Open <code>C:\xampp\apache\conf\httpd.conf</code></li>
        <li>Find the <code>&lt;Directory&gt;</code> section for your htdocs</li>
        <li>Make sure it has: <code>AllowOverride All</code> (not <code>AllowOverride None</code>)</li>
        <li>Restart Apache</li>
    </ol>
    
    <h2>4. Test URL Rewriting</h2>
    <p>Current request info:</p>
    <pre>
REQUEST_URI: <?php echo $_SERVER['REQUEST_URI']; ?>

SCRIPT_NAME: <?php echo $_SERVER['SCRIPT_NAME']; ?>

$_GET['url']: <?php echo isset($_GET['url']) ? $_GET['url'] : 'NOT SET'; ?>
    </pre>
    
    <p><strong>Test these URLs:</strong></p>
    <ul>
        <li><a href="/SignED_/public/index.php?url=assessment">Direct: /SignED_/public/index.php?url=assessment</a> (Should work)</li>
        <li><a href="/SignED_/assessment">Rewrite: /SignED_/assessment</a> (Should be rewritten to above)</li>
    </ul>
    
    <h2>5. Quick Fix Instructions</h2>
    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px;">
        <h3>If mod_rewrite is disabled:</h3>
        <ol>
            <li>Open XAMPP Control Panel</li>
            <li>Stop Apache</li>
            <li>Click "Config" → "httpd.conf"</li>
            <li>Find this line: <code>#LoadModule rewrite_module modules/mod_rewrite.so</code></li>
            <li>Remove the <code>#</code> to uncomment it</li>
            <li>Find <code>&lt;Directory "C:/xampp/htdocs"&gt;</code></li>
            <li>Change <code>AllowOverride None</code> to <code>AllowOverride All</code></li>
            <li>Save and restart Apache</li>
        </ol>
    </div>
    
    <hr>
    <p><a href="/SignED_/parent/dashboard">Back to Dashboard</a></p>
    
</body>
</html>
