<?php
/**
 * Test Routing Flow
 * Shows what happens when URL is processed
 */

echo "<h1>Routing Flow Test</h1>";
echo "<hr>";

echo "<h2>1. Current Request Info</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NONE') . "\n";
echo "\$_GET['url']: " . ($_GET['url'] ?? 'NOT SET') . "\n";
echo "</pre>";

echo "<hr>";
echo "<h2>2. Simulate App.php Routing</h2>";

// Simulate what App.php does
if (isset($_GET['url'])) {
    $url = rtrim($_GET['url'], '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $urlArray = explode('/', $url);
    
    echo "<p>URL parameter: <code>" . $_GET['url'] . "</code></p>";
    echo "<p>URL array:</p>";
    echo "<pre>";
    print_r($urlArray);
    echo "</pre>";
    
    if (isset($urlArray[0])) {
        $controllerName = ucfirst($urlArray[0]) . 'Controller';
        echo "<p>Controller would be: <code>$controllerName</code></p>";
        
        if (file_exists('../app/controllers/' . $controllerName . '.php')) {
            echo "<p style='color: green;'>✅ Controller file exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Controller file NOT found</p>";
        }
    }
    
    if (isset($urlArray[1])) {
        $method = $urlArray[1];
        echo "<p>Method would be: <code>$method</code></p>";
    } else {
        echo "<p>Method would be: <code>index</code> (default)</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No URL parameter - would use default AuthController::login()</p>";
}

echo "<hr>";
echo "<h2>3. Test Links</h2>";
echo "<p>Click these links and see what URL parameters are passed:</p>";
echo "<ul>";
echo "<li><a href='/SignED_/assessment'>Test: /SignED_/assessment</a></li>";
echo "<li><a href='/SignED_/parent/dashboard'>Test: /SignED_/parent/dashboard</a></li>";
echo "<li><a href='/SignED_/auth/login'>Test: /SignED_/auth/login</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>4. .htaccess Check</h2>";

if (file_exists('.htaccess')) {
    echo "<p style='color: green;'>✅ public/.htaccess exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ public/.htaccess NOT FOUND</p>";
}

if (file_exists('../.htaccess')) {
    echo "<p style='color: green;'>✅ root .htaccess exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('../.htaccess')) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ root .htaccess NOT FOUND</p>";
}

echo "<hr>";
echo "<h2>5. Direct Test</h2>";
echo "<p>Try accessing directly through index.php:</p>";
echo "<p><a href='/SignED_/public/index.php?url=assessment'>Direct: index.php?url=assessment</a></p>";
?>
