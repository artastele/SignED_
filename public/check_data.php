<?php
/**
 * Check Database Data
 * Verifies parent, learner, and assessment data
 */

// Use SAME session settings as index.php
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
require_once '../config/database.php';

$db = new Database();
$conn = $db->connect();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        .section { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Database Data Check</h1>
    
    <div class="section">
        <h2>Current Session</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="success">✅ Logged in as: <?php echo $_SESSION['fullname']; ?></p>
            <p>User ID: <?php echo $_SESSION['user_id']; ?></p>
            <p>Role: <?php echo $_SESSION['role']; ?></p>
        <?php else: ?>
            <p class="error">❌ NOT LOGGED IN</p>
            <p><a href="<?php echo URLROOT; ?>/auth/login">Login here</a></p>
            <?php exit; ?>
        <?php endif; ?>
    </div>
    
    <?php
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['role'];
    ?>
    
    <div class="section">
        <h2>1. User Account</h2>
        <?php
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($user) {
            echo "<table>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            echo "<tr><td>ID</td><td>{$user->id}</td></tr>";
            echo "<tr><td>Full Name</td><td>{$user->fullname}</td></tr>";
            echo "<tr><td>Email</td><td>{$user->email}</td></tr>";
            echo "<tr><td>Role</td><td>{$user->role}</td></tr>";
            echo "<tr><td>Verified</td><td>" . ($user->is_verified ? 'Yes' : 'No') . "</td></tr>";
            echo "</table>";
        } else {
            echo "<p class='error'>❌ User not found in database!</p>";
        }
        ?>
    </div>
    
    <?php if ($userRole === 'parent'): ?>
    
    <div class="section">
        <h2>2. Enrollments</h2>
        <?php
        $stmt = $conn->prepare("SELECT * FROM enrollments WHERE parent_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $enrollments = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if ($enrollments && count($enrollments) > 0) {
            echo "<p class='success'>✅ Found " . count($enrollments) . " enrollment(s)</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Learner Name</th><th>Status</th><th>Created</th></tr>";
            foreach ($enrollments as $enrollment) {
                echo "<tr>";
                echo "<td>{$enrollment->id}</td>";
                echo "<td>{$enrollment->learner_first_name} {$enrollment->learner_last_name}</td>";
                echo "<td>{$enrollment->status}</td>";
                echo "<td>" . date('M j, Y', strtotime($enrollment->created_at)) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ No enrollments found for this parent</p>";
            echo "<p>You need to complete enrollment first.</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Learners (Enrolled Students)</h2>
        <?php
        $stmt = $conn->prepare("SELECT * FROM learners WHERE parent_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $learners = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if ($learners && count($learners) > 0) {
            echo "<p class='success'>✅ Found " . count($learners) . " learner(s)</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Grade</th><th>Status</th><th>LRN</th><th>Created</th></tr>";
            foreach ($learners as $learner) {
                echo "<tr>";
                echo "<td>{$learner->id}</td>";
                echo "<td>{$learner->first_name} {$learner->last_name}</td>";
                echo "<td>" . ($learner->grade_level ?? 'N/A') . "</td>";
                echo "<td>" . ($learner->status ?? 'N/A') . "</td>";
                echo "<td>" . ($learner->lrn ?? 'Not assigned') . "</td>";
                echo "<td>" . date('M j, Y', strtotime($learner->created_at)) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ No learners found for this parent</p>";
            echo "<p>Learners are created after enrollment is approved.</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Assessments</h2>
        <?php
        if ($learners && count($learners) > 0) {
            $learnerIds = array_map(function($l) { return $l->id; }, $learners);
            $placeholders = str_repeat('?,', count($learnerIds) - 1) . '?';
            
            $stmt = $conn->prepare("SELECT * FROM assessments WHERE learner_id IN ($placeholders) ORDER BY created_at DESC");
            $stmt->execute($learnerIds);
            $assessments = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            if ($assessments && count($assessments) > 0) {
                echo "<p class='success'>✅ Found " . count($assessments) . " assessment(s)</p>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Learner ID</th><th>Status</th><th>Created</th><th>Submitted</th></tr>";
                foreach ($assessments as $assessment) {
                    echo "<tr>";
                    echo "<td>{$assessment->id}</td>";
                    echo "<td>{$assessment->learner_id}</td>";
                    echo "<td>{$assessment->status}</td>";
                    echo "<td>" . date('M j, Y', strtotime($assessment->created_at)) . "</td>";
                    echo "<td>" . ($assessment->parent_submitted_at ? date('M j, Y', strtotime($assessment->parent_submitted_at)) : 'Not submitted') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>ℹ️ No assessments found yet</p>";
                echo "<p>Assessments are created when SPED teacher unlocks them after enrollment approval.</p>";
            }
        } else {
            echo "<p>⚠️ Cannot check assessments - no learners found</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Diagnosis</h2>
        <?php
        $issues = [];
        $canAccessAssessment = true;
        
        if (!isset($_SESSION['user_id'])) {
            $issues[] = "Not logged in";
            $canAccessAssessment = false;
        }
        
        if ($userRole !== 'parent') {
            $issues[] = "Not a parent (Role: $userRole)";
            $canAccessAssessment = false;
        }
        
        if (!$learners || count($learners) === 0) {
            $issues[] = "No learners found - enrollment not approved yet";
            $canAccessAssessment = false;
        }
        
        if (empty($issues)) {
            echo "<p class='success'>✅ All checks passed! You should be able to access the assessment page.</p>";
            echo "<p><a href='" . URLROOT . "/assessment' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Go to Assessment Page</a></p>";
        } else {
            echo "<p class='error'>❌ Found " . count($issues) . " issue(s):</p>";
            echo "<ul>";
            foreach ($issues as $issue) {
                echo "<li>$issue</li>";
            }
            echo "</ul>";
            
            if (!$learners || count($learners) === 0) {
                echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
                echo "<h3 style='margin-top: 0;'>⚠️ No Learners Found</h3>";
                echo "<p>This means your enrollment has not been approved yet by the SPED teacher.</p>";
                echo "<p><strong>What to do:</strong></p>";
                echo "<ol>";
                echo "<li>Wait for SPED teacher to approve your enrollment</li>";
                echo "<li>Once approved, a learner account will be created</li>";
                echo "<li>Then you can access the assessment page</li>";
                echo "</ol>";
                echo "<p><strong>OR</strong> you can try enrolling a new student with a different parent account to test the flow.</p>";
                echo "</div>";
            }
        }
        ?>
    </div>
    
    <?php endif; ?>
    
    <hr>
    <p><a href="<?php echo URLROOT; ?>/parent/dashboard">Back to Dashboard</a></p>
    
</body>
</html>
