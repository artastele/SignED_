<?php
// SPED Database Setup Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>SPED Database Setup Script</h1>\n";

try {
    // Include database configuration
    require_once 'config/database.php';
    
    $db = new Database();
    $pdo = $db->connect();
    
    echo "<h2>1. Database Connection</h2>\n";
    echo "✓ Connected to database successfully<br>\n";
    
    // Read and execute the SPED database update script
    echo "<h2>2. Executing SPED Database Schema</h2>\n";
    
    $sqlFile = 'database_sped_update.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new Exception("Failed to read SQL file: $sqlFile");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^(--|\/\*|\s*$)/', $stmt);
        }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Extract table name for reporting
            if (preg_match('/CREATE TABLE\s+(\w+)/i', $statement, $matches)) {
                echo "✓ Created table: {$matches[1]}<br>\n";
            } elseif (preg_match('/ALTER TABLE\s+(\w+)/i', $statement, $matches)) {
                echo "✓ Modified table: {$matches[1]}<br>\n";
            } elseif (preg_match('/INSERT.*INTO\s+(\w+)/i', $statement, $matches)) {
                echo "✓ Inserted data into: {$matches[1]}<br>\n";
            } else {
                echo "✓ Executed SQL statement<br>\n";
            }
            
        } catch (PDOException $e) {
            $errorCount++;
            echo "✗ Error executing statement: " . $e->getMessage() . "<br>\n";
            echo "Statement: " . substr($statement, 0, 100) . "...<br>\n";
        }
    }
    
    echo "<p><strong>Summary:</strong> $successCount statements executed successfully, $errorCount errors</p>\n";
    
    // Verify tables were created
    echo "<h2>3. Verifying SPED Tables</h2>\n";
    
    $spedTables = [
        'learners', 'enrollments', 'enrollment_documents', 'assessments',
        'iep_meetings', 'iep_meeting_participants', 'ieps', 'learning_materials',
        'learner_submissions', 'audit_logs', 'error_logs'
    ];
    
    $existingTables = [];
    foreach ($spedTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists<br>\n";
            $existingTables[] = $table;
        } else {
            echo "✗ Table '$table' missing<br>\n";
        }
    }
    
    // Check users table for SPED roles
    echo "<h2>4. Verifying Users Table SPED Roles</h2>\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    $roleColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($roleColumn && strpos($roleColumn['Type'], 'sped_teacher') !== false) {
        echo "✓ Users table supports SPED roles<br>\n";
        echo "Available roles: " . $roleColumn['Type'] . "<br>\n";
    } else {
        echo "✗ Users table missing SPED roles<br>\n";
    }
    
    // Create storage directories
    echo "<h2>5. Creating Storage Directories</h2>\n";
    
    $directories = [
        'storage',
        'storage/documents',
        'storage/cache',
        'cache'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "✓ Created directory: $dir<br>\n";
            } else {
                echo "✗ Failed to create directory: $dir<br>\n";
            }
        } else {
            echo "✓ Directory exists: $dir<br>\n";
        }
    }
    
    // Create encryption key if it doesn't exist
    echo "<h2>6. Setting up Encryption</h2>\n";
    
    $keyFile = 'config/encryption.key';
    if (!file_exists($keyFile)) {
        $key = random_bytes(32);
        if (file_put_contents($keyFile, $key)) {
            chmod($keyFile, 0600);
            echo "✓ Created encryption key file<br>\n";
        } else {
            echo "✗ Failed to create encryption key file<br>\n";
        }
    } else {
        echo "✓ Encryption key file exists<br>\n";
    }
    
    echo "<h2>Database Setup Complete!</h2>\n";
    echo "<p style='color: green; font-weight: bold;'>✓ SPED database schema has been successfully set up</p>\n";
    echo "<p>Tables created: " . count($existingTables) . "/" . count($spedTables) . "</p>\n";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Setup Failed</h2>\n";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database configuration and try again.</p>\n";
}

echo "<p><em>Setup completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>