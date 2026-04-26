<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPED System Setup & Testing</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .button { display: inline-block; padding: 12px 24px; margin: 10px 5px; background-color: #1E40AF; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; font-size: 16px; }
        .button:hover { background-color: #1E3A8A; }
        .button.secondary { background-color: #6B7280; }
        .button.secondary:hover { background-color: #4B5563; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .status.success { background-color: #D1FAE5; border: 1px solid #10B981; color: #065F46; }
        .status.warning { background-color: #FEF3C7; border: 1px solid #F59E0B; color: #92400E; }
        .status.error { background-color: #FEE2E2; border: 1px solid #EF4444; color: #991B1B; }
        .test-output { background-color: #F9FAFB; border: 1px solid #D1D5DB; padding: 15px; margin: 10px 0; border-radius: 5px; max-height: 500px; overflow-y: auto; }
        h1 { color: #1F2937; border-bottom: 2px solid #1E40AF; padding-bottom: 10px; }
        h2 { color: #374151; margin-top: 30px; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #1E40AF; background-color: #F8FAFC; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 SPED Workflow Integration - Setup & Testing</h1>
        
        <div class="status success">
            <strong>Welcome!</strong> This tool will help you set up and test the SPED (Special Education) workflow integration system.
        </div>

        <h2>📋 Setup Steps</h2>
        
        <div class="step">
            <h3>Step 1: Database Setup</h3>
            <p>Set up the SPED database schema with all required tables and relationships.</p>
            <a href="?action=setup_database" class="button">🗄️ Setup Database</a>
            <p><small>This will create 11 new tables and modify the users table to support SPED roles.</small></p>
        </div>

        <div class="step">
            <h3>Step 2: Run Infrastructure Tests</h3>
            <p>Test all core components including models, authentication, and security systems.</p>
            <a href="?action=run_tests" class="button">🧪 Run All Tests</a>
            <p><small>Comprehensive testing of database, models, authentication, and security components.</small></p>
        </div>

        <div class="step">
            <h3>Step 3: Verify System Status</h3>
            <p>Quick verification of the current system status and component availability.</p>
            <a href="?action=system_status" class="button secondary">📊 Check System Status</a>
            <p><small>Shows which components are installed and working correctly.</small></p>
        </div>

        <?php
        if (isset($_GET['action'])) {
            echo "<h2>🔧 Execution Results</h2>";
            echo "<div class='test-output'>";
            
            switch ($_GET['action']) {
                case 'setup_database':
                    echo "<h3>Database Setup</h3>";
                    include '../setup_sped_database.php';
                    break;
                    
                case 'run_tests':
                    echo "<h3>Running All Tests</h3>";
                    include '../run_all_tests.php';
                    break;
                    
                case 'system_status':
                    echo "<h3>System Status Check</h3>";
                    include '../test_checkpoint.php';
                    break;
                    
                default:
                    echo "<p>Unknown action.</p>";
            }
            
            echo "</div>";
        }
        ?>

        <h2>📚 System Information</h2>
        
        <div class="step">
            <h3>SPED Workflow Components</h3>
            <ul>
                <li><strong>Database Schema:</strong> 11 new tables for learners, enrollments, assessments, IEPs, etc.</li>
                <li><strong>User Roles:</strong> sped_teacher, guidance, principal, learner (in addition to existing roles)</li>
                <li><strong>Security:</strong> AES-256 encryption, audit logging, role-based access control</li>
                <li><strong>Models:</strong> Complete CRUD operations for all SPED entities</li>
                <li><strong>Authentication:</strong> Enhanced with password policies and session management</li>
            </ul>
        </div>

        <div class="step">
            <h3>Next Steps After Setup</h3>
            <ol>
                <li>Implement SPED workflow controllers (EnrollmentController, AssessmentController, etc.)</li>
                <li>Create user interfaces for each role's dashboard and workflows</li>
                <li>Set up email notification system integration</li>
                <li>Implement file upload and document management interfaces</li>
                <li>Add comprehensive testing and validation</li>
            </ol>
        </div>

        <div class="status warning">
            <strong>Note:</strong> Make sure your MySQL server is running and the database credentials in <code>config/database.php</code> are correct before running the setup.
        </div>

        <p style="text-align: center; margin-top: 40px; color: #6B7280;">
            <em>SPED Workflow Integration System - Checkpoint 4 Verification</em>
        </p>
    </div>
</body>
</html>