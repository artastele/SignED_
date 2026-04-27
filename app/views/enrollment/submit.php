<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPED Enrollment Submission - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        .enrollment-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1E40AF;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background-color: #1E40AF;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1D4ED8;
        }
        
        .btn-secondary {
            background-color: #6B7280;
            color: white;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
        }
        
        .alert-success {
            background-color: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #166534;
        }
        
        .existing-enrollments {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .enrollment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .enrollment-item:last-child {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-pending_documents {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-pending_verification {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        .status-approved {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-rejected {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .required {
            color: #B91C1C;
        }
        
        .help-text {
            font-size: 0.875rem;
            color: #6B7280;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="enrollment-container">
        <h1>SPED Enrollment Submission</h1>
        <p>Submit your child's information to begin the Special Education enrollment process.</p>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($existing_enrollments)): ?>
            <div class="existing-enrollments">
                <h3>Your Existing Enrollments</h3>
                <?php foreach ($existing_enrollments as $enrollment): ?>
                    <div class="enrollment-item">
                        <div>
                            <strong><?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?></strong>
                            <br>
                            <small>Grade <?php echo htmlspecialchars($enrollment->learner_grade); ?> • Submitted <?php echo date('M j, Y', strtotime($enrollment->created_at)); ?></small>
                        </div>
                        <div>
                            <span class="status-badge status-<?php echo $enrollment->status; ?>">
                                <?php echo ucwords(str_replace('_', ' ', $enrollment->status)); ?>
                            </span>
                            <?php if ($enrollment->status === 'pending_documents'): ?>
                                <a href="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $enrollment->id; ?>" class="btn btn-primary" style="margin-left: 0.5rem; padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                    Upload Documents
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo URLROOT; ?>/enrollment/submit">
            <?php include '../app/views/partials/csrf_token.php'; ?>
            
            <div class="form-group">
                <label for="first_name">Child's First Name <span class="required">*</span></label>
                <input type="text" id="first_name" name="first_name" required 
                       value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name">Child's Last Name <span class="required">*</span></label>
                <input type="text" id="last_name" name="last_name" required 
                       value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                <input type="date" id="date_of_birth" name="date_of_birth" required 
                       value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : ''; ?>">
                <div class="help-text">Child must be between 3 and 21 years old</div>
            </div>
            
            <div class="form-group">
                <label for="grade_level">Grade Level <span class="required">*</span></label>
                <select id="grade_level" name="grade_level" required>
                    <option value="">Select Grade Level</option>
                    <option value="Pre-K" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === 'Pre-K') ? 'selected' : ''; ?>>Pre-K</option>
                    <option value="Kindergarten" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === 'Kindergarten') ? 'selected' : ''; ?>>Kindergarten</option>
                    <option value="1st Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '1st Grade') ? 'selected' : ''; ?>>1st Grade</option>
                    <option value="2nd Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '2nd Grade') ? 'selected' : ''; ?>>2nd Grade</option>
                    <option value="3rd Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '3rd Grade') ? 'selected' : ''; ?>>3rd Grade</option>
                    <option value="4th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '4th Grade') ? 'selected' : ''; ?>>4th Grade</option>
                    <option value="5th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '5th Grade') ? 'selected' : ''; ?>>5th Grade</option>
                    <option value="6th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '6th Grade') ? 'selected' : ''; ?>>6th Grade</option>
                    <option value="7th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '7th Grade') ? 'selected' : ''; ?>>7th Grade</option>
                    <option value="8th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '8th Grade') ? 'selected' : ''; ?>>8th Grade</option>
                    <option value="9th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '9th Grade') ? 'selected' : ''; ?>>9th Grade</option>
                    <option value="10th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '10th Grade') ? 'selected' : ''; ?>>10th Grade</option>
                    <option value="11th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '11th Grade') ? 'selected' : ''; ?>>11th Grade</option>
                    <option value="12th Grade" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] === '12th Grade') ? 'selected' : ''; ?>>12th Grade</option>
                </select>
            </div>
            
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Submit Enrollment</button>
                <a href="<?php echo URLROOT; ?>/parent/dashboard" class="btn btn-secondary" style="margin-left: 1rem;">Cancel</a>
            </div>
        </form>
        
        <div style="margin-top: 2rem; padding: 1rem; background-color: #F3F4F6; border-radius: 4px;">
            <h4>Next Steps:</h4>
            <ol>
                <li>Submit your child's basic information (this form)</li>
                <li>Upload required documents (PSA, PWD ID, Medical Records, BEEF)</li>
                <li>Wait for document verification by SPED staff</li>
                <li>Receive approval notification and proceed to assessment</li>
            </ol>
        </div>
    </div>
</body>
</html>