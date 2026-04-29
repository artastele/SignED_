<?php
/**
 * Test URL Routing
 * Verifies that .htaccess is working correctly
 */

echo "<h1>URL Routing Test</h1>";
echo "<hr>";

echo "<h2>Current Request Info:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NONE') . "\n";
echo "\$_GET['url']: " . ($_GET['url'] ?? 'NOT SET') . "\n";
echo "</pre>";

echo "<hr>";
echo "<h2>Test These URLs:</h2>";
echo "<ol>";
echo "<li><a href='/SignED_/assessment'>Test: /SignED_/assessment</a> - Should route to AssessmentController::index()</li>";
echo "<li><a href='/SignED_/parent/dashboard'>Test: /SignED_/parent/dashboard</a> - Should route to ParentController::dashboard()</li>";
echo "<li><a href='/SignED_/auth/login'>Test: /SignED_/auth/login</a> - Should route to AuthController::login()</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Expected Behavior:</h2>";
echo "<p>When you click a link above:</p>";
echo "<ul>";
echo "<li>The URL should stay clean (e.g., /SignED_/assessment)</li>";
echo "<li>It should NOT show /public/ in the URL</li>";
echo "<li>It should NOT show index.php in the URL</li>";
echo "<li>It should route to the correct controller</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>.htaccess Check:</h2>";

if (file_exists('../.htaccess')) {
    echo "<p style='color: green;'>✅ Root .htaccess exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('../.htaccess')) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Root .htaccess NOT FOUND</p>";
}

if (file_exists('.htaccess')) {
    echo "<p style='color: green;'>✅ Public .htaccess exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Public .htaccess NOT FOUND</p>";
}

echo "<hr>";
echo "<h2>Apache mod_rewrite Check:</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✅ mod_rewrite is enabled</p>";
    } else {
        echo "<p style='color: red;'>❌ mod_rewrite is NOT enabled</p>";
        echo "<p>You need to enable mod_rewrite in Apache!</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Cannot check if mod_rewrite is enabled (function not available)</p>";
}
?>
