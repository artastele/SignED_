<?php
/**
 * Debug Assessment Page
 * Shows what happens when accessing /assessment
 */

// Start session
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
require_once '../core/Model.php';
require_once '../app/models/Learner.php';
require_once '../app/models/Assessment.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Assessment Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .section { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Assessment Page Debug</h1>
    
    <div class="section">
        <h2>1. Session Check</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="success">✅ Logged in as: <?php echo $_SESSION['fullname']; ?></p>
            <p>Role: <?php echo $_SESSION['role']; ?></p>
            <p>User ID: <?php echo $_SESSION['user_id']; ?></p>
        <?php else: ?>
            <p class="error">❌ NOT LOGGED IN</p>
            <p><a href="<?php echo URLROOT; ?>/auth/login">Login here</a></p>
            <?php exit; ?>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>2. Parent Check</h2>
        <?php if ($_SESSION['role'] === 'parent'): ?>
            <p class="success">✅ User is a PARENT</p>
        <?php else: ?>
            <p class="error">❌ User is NOT a parent (Role: <?php echo $_SESSION['role']; ?>)</p>
            <?php exit; ?>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>3. Database Connection</h2>
        <?php
        try {
            $db = new Database();
            $conn = $db->getConnection();
            echo '<p class="success">✅ Database connected</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Database error: ' . $e->getMessage() . '</p>';
            exit;
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Learner Data</h2>
        <?php
        $learnerModel = new Learner();
        $parentId = $_SESSION['user_id'];
        $learners = $learnerModel->getByParent($parentId);
        
        if ($learners && count($learners) > 0): ?>
            <p class="success">✅ Found <?php echo count($learners); ?> learner(s)</p>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($learners as $learner): ?>
                <tr>
                    <td><?php echo $learner->id; ?></td>
                    <td><?php echo htmlspecialchars($learner->first_name . ' ' . $learner->last_name); ?></td>
                    <td><?php echo htmlspecialchars($learner->grade_level ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($learner->status ?? 'N/A'); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="error">❌ No learners found for this parent</p>
            <p>This parent needs to complete enrollment first.</p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>5. Assessment Data</h2>
        <?php
        $assessmentModel = new Assessment();
        $assessments = [];
        
        if ($learners && count($learners) > 0) {
            foreach ($learners as $learner) {
                $assessment = $assessmentModel->getByLearnerId($learner->id);
                if ($assessment) {
                    $assessments[] = $assessment;
                }
            }
            
            if (count($assessments) > 0): ?>
                <p class="success">✅ Found <?php echo count($assessments); ?> assessment(s)</p>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Learner ID</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                    <?php foreach ($assessments as $assessment): ?>
                    <tr>
                        <td><?php echo $assessment->id; ?></td>
                        <td><?php echo $assessment->learner_id; ?></td>
                        <td><?php echo htmlspecialchars($assessment->status); ?></td>
                        <td><?php echo date('M j, Y', strtotime($assessment->created_at)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>ℹ️ No assessments found yet</p>
            <?php endif;
        }
        ?>
    </div>
    
    <div class="section">
        <h2>6. What Should Happen</h2>
        <p>When you go to <code><?php echo URLROOT; ?>/assessment</code>, you should see:</p>
        <ul>
            <li>A list of your enrolled children</li>
            <li>Assessment status for each child (locked, unlocked, draft, submitted)</li>
            <li>Buttons to start or continue assessments</li>
        </ul>
        
        <p><strong>Current Data Summary:</strong></p>
        <ul>
            <li>Learners: <?php echo isset($learners) ? count($learners) : 0; ?></li>
            <li>Assessments: <?php echo count($assessments); ?></li>
        </ul>
    </div>
    
    <div class="section">
        <h2>7. Test Links</h2>
        <p>
            <a href="<?php echo URLROOT; ?>/assessment" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Go to Assessment Page
            </a>
        </p>
        <p>
            <a href="<?php echo URLROOT; ?>/parent/dashboard">Back to Dashboard</a>
        </p>
    </div>
    
    <div class="section">
        <h2>8. Expected vs Actual</h2>
        <p><strong>Expected URL:</strong> <code>http://localhost/SignED_/assessment</code></p>
        <p><strong>Expected View:</strong> <code>app/views/assessment/index.php</code></p>
        <p><strong>Expected Controller:</strong> <code>AssessmentController::index()</code></p>
    </div>
    
</body>
</html>
