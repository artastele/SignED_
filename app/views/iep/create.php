<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create IEP - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .iep-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .iep-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1E40AF;
        }
        
        .learner-info {
            background: #F3F4F6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            display: flex;
        }
        
        .info-label {
            font-weight: bold;
            color: #1E40AF;
            width: 120px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
        }
        
        .assessment-summary {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        
        .assessment-summary h4 {
            margin: 0 0 10px 0;
            color: #1E40AF;
        }
        
        .assessment-summary p {
            margin: 5px 0;
            font-size: 14px;
            color: #374151;
        }
        
        .form-section {
            margin-bottom: 40px;
            padding: 25px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
        }
        
        .form-section h3 {
            margin: 0 0 20px 0;
            color: #1E40AF;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #374151;
        }
        
        .form-group textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }
        
        .form-group input[type="date"] {
            width: 200px;
            padding: 10px;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .date-group {
            display: flex;
            gap: 30px;
            align-items: end;
        }
        
        .date-group .form-group {
            margin-bottom: 0;
        }
        
        .help-text {
            font-size: 12px;
            color: #6B7280;
            margin-top: 5px;
            font-style: italic;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #1E40AF;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1D4ED8;
        }
        
        .btn-secondary {
            background: #6B7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4B5563;
        }
        
        .btn-draft {
            background: #F59E0B;
            color: white;
        }
        
        .btn-draft:hover {
            background: #D97706;
        }
        
        .error {
            background: #FEE2E2;
            color: #B91C1C;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .required {
            color: #B91C1C;
        }
        
        .form-actions {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }
        
        .prepopulated-notice {
            background: #D1FAE5;
            border: 1px solid #A7F3D0;
            color: #065F46;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .character-count {
            font-size: 11px;
            color: #6B7280;
            text-align: right;
            margin-top: 5px;
        }
        
        .progress-indicator {
            background: #F3F4F6;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
        }
        
        .step.completed .step-number {
            background: #10B981;
            color: white;
        }
        
        .step.current .step-number {
            background: #1E40AF;
            color: white;
        }
        
        .step.pending .step-number {
            background: #E5E7EB;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Create IEP Document</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/list">IEP List</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="iep-container">
            <?php if (isset($error)): ?>
                <div class="error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="iep-header">
                <h2>Individualized Education Plan (IEP)</h2>
                <p>Learner: <strong><?= htmlspecialchars($learner->first_name . ' ' . $learner->last_name) ?></strong></p>
            </div>

            <div class="progress-indicator">
                <div class="progress-steps">
                    <div class="step completed">
                        <div class="step-number">✓</div>
                        <span>Assessment Complete</span>
                    </div>
                    <div class="step completed">
                        <div class="step-number">✓</div>
                        <span>Meeting Held</span>
                    </div>
                    <div class="step current">
                        <div class="step-number">3</div>
                        <span>IEP Creation</span>
                    </div>
                    <div class="step pending">
                        <div class="step-number">4</div>
                        <span>Principal Approval</span>
                    </div>
                </div>
            </div>

            <div class="learner-info">
                <h3 style="margin-top: 0; color: #1E40AF;">Learner Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name:</div>
                        <div class="info-value"><?= htmlspecialchars($learner->first_name . ' ' . $learner->last_name) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth:</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($learner->date_of_birth)) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Grade Level:</div>
                        <div class="info-value"><?= htmlspecialchars($learner->grade_level) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Parent/Guardian:</div>
                        <div class="info-value"><?= htmlspecialchars($learner->parent_name) ?></div>
                    </div>
                </div>
            </div>

            <?php if ($assessment): ?>
                <div class="assessment-summary">
                    <h4>Assessment Summary</h4>
                    <p><strong>Assessment Date:</strong> <?= date('F j, Y', strtotime($assessment->assessment_date)) ?></p>
                    <p><strong>Assessed by:</strong> <?= htmlspecialchars($assessment->assessor_name ?? 'N/A') ?></p>
                    <p style="margin-top: 10px; font-style: italic;">
                        The fields below have been pre-populated with assessment data. Please review and modify as needed based on the IEP meeting discussions.
                    </p>
                </div>
            <?php endif; ?>

            <form method="POST" id="iepForm">
                <div class="form-section">
                    <h3>Present Level of Academic Achievement and Functional Performance</h3>
                    <?php if (isset($prepopulated['present_level_performance'])): ?>
                        <div class="prepopulated-notice">
                            ✓ This section has been pre-populated with assessment data. Please review and modify as needed.
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="present_level_performance">
                            Describe the learner's current academic achievement, functional performance, and how the disability affects involvement in the general education curriculum <span class="required">*</span>
                        </label>
                        <textarea name="present_level_performance" 
                                  id="present_level_performance" 
                                  required
                                  placeholder="Include strengths, needs, and current performance levels across all relevant domains..."><?= htmlspecialchars($form_data['present_level_performance'] ?? $prepopulated['present_level_performance'] ?? '') ?></textarea>
                        <div class="character-count" id="present_level_count">0 characters</div>
                        <div class="help-text">
                            Include cognitive abilities, communication skills, social-emotional development, adaptive behavior, and academic performance.
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Annual Goals</h3>
                    <div class="form-group">
                        <label for="annual_goals">
                            Measurable annual goals designed to meet the learner's needs and enable progress in the general education curriculum <span class="required">*</span>
                        </label>
                        <textarea name="annual_goals" 
                                  id="annual_goals" 
                                  required
                                  placeholder="List specific, measurable goals for the academic year..."><?= htmlspecialchars($form_data['annual_goals'] ?? $prepopulated['annual_goals'] ?? '') ?></textarea>
                        <div class="character-count" id="annual_goals_count">0 characters</div>
                        <div class="help-text">
                            Goals should be specific, measurable, achievable, relevant, and time-bound (SMART goals).
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Short-Term Objectives</h3>
                    <div class="form-group">
                        <label for="short_term_objectives">
                            Short-term instructional objectives or benchmarks that support the annual goals <span class="required">*</span>
                        </label>
                        <textarea name="short_term_objectives" 
                                  id="short_term_objectives" 
                                  required
                                  placeholder="Break down annual goals into smaller, achievable steps..."><?= htmlspecialchars($form_data['short_term_objectives'] ?? '') ?></textarea>
                        <div class="character-count" id="short_term_objectives_count">0 characters</div>
                        <div class="help-text">
                            Objectives should be stepping stones toward achieving the annual goals.
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Special Education and Related Services</h3>
                    <div class="form-group">
                        <label for="special_education_services">
                            Special education and related services to be provided <span class="required">*</span>
                        </label>
                        <textarea name="special_education_services" 
                                  id="special_education_services" 
                                  required
                                  placeholder="Describe the special education services, related services, and supplementary aids..."><?= htmlspecialchars($form_data['special_education_services'] ?? '') ?></textarea>
                        <div class="character-count" id="special_education_services_count">0 characters</div>
                        <div class="help-text">
                            Include frequency, location, duration, and personnel responsible for each service.
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Accommodations and Modifications</h3>
                    <div class="form-group">
                        <label for="accommodations">
                            Program modifications or supports for school personnel <span class="required">*</span>
                        </label>
                        <textarea name="accommodations" 
                                  id="accommodations" 
                                  required
                                  placeholder="List accommodations and modifications needed for the learner to access the curriculum..."><?= htmlspecialchars($form_data['accommodations'] ?? '') ?></textarea>
                        <div class="character-count" id="accommodations_count">0 characters</div>
                        <div class="help-text">
                            Include classroom accommodations, testing modifications, and assistive technology needs.
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Progress Measurement</h3>
                    <div class="form-group">
                        <label for="progress_measurement">
                            How progress toward annual goals will be measured and reported <span class="required">*</span>
                        </label>
                        <textarea name="progress_measurement" 
                                  id="progress_measurement" 
                                  required
                                  placeholder="Describe methods for measuring and reporting progress..."><?= htmlspecialchars($form_data['progress_measurement'] ?? '') ?></textarea>
                        <div class="character-count" id="progress_measurement_count">0 characters</div>
                        <div class="help-text">
                            Include assessment methods, frequency of progress reports, and criteria for success.
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>IEP Duration</h3>
                    <div class="date-group">
                        <div class="form-group">
                            <label for="start_date">Start Date <span class="required">*</span></label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date"
                                   min="<?= date('Y-m-d') ?>"
                                   value="<?= htmlspecialchars($form_data['start_date'] ?? $prepopulated['start_date'] ?? '') ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">End Date <span class="required">*</span></label>
                            <input type="date" 
                                   name="end_date" 
                                   id="end_date"
                                   value="<?= htmlspecialchars($form_data['end_date'] ?? $prepopulated['end_date'] ?? '') ?>"
                                   required>
                        </div>
                        
                        <div style="color: #6B7280; font-size: 14px; align-self: center;">
                            IEP typically covers one academic year
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="create" class="btn btn-primary">
                        Create IEP
                    </button>
                    <button type="submit" name="action" value="draft" class="btn btn-draft" style="margin-left: 15px;">
                        Save as Draft
                    </button>
                    <a href="/iep/list" class="btn btn-secondary" style="margin-left: 15px;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counting for textareas
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                const countElement = document.getElementById(textarea.id + '_count');
                if (countElement) {
                    function updateCount() {
                        countElement.textContent = textarea.value.length + ' characters';
                    }
                    
                    textarea.addEventListener('input', updateCount);
                    updateCount(); // Initial count
                }
            });
            
            // Date validation
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            startDateInput.addEventListener('change', function() {
                if (this.value) {
                    // Set minimum end date to start date + 1 day
                    const startDate = new Date(this.value);
                    startDate.setDate(startDate.getDate() + 1);
                    endDateInput.min = startDate.toISOString().split('T')[0];
                    
                    // If end date is before start date, clear it
                    if (endDateInput.value && new Date(endDateInput.value) <= new Date(this.value)) {
                        endDateInput.value = '';
                    }
                }
            });
            
            // Form validation
            document.getElementById('iepForm').addEventListener('submit', function(e) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                
                if (endDate <= startDate) {
                    e.preventDefault();
                    alert('End date must be after start date.');
                    return;
                }
                
                // Check if all required fields are filled
                const requiredFields = this.querySelectorAll('[required]');
                let allFilled = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        allFilled = false;
                        field.style.borderColor = '#EF4444';
                    } else {
                        field.style.borderColor = '#D1D5DB';
                    }
                });
                
                if (!allFilled) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Confirm submission
                const action = e.submitter.value;
                const message = action === 'draft' 
                    ? 'Save this IEP as a draft? You can continue editing it later.'
                    : 'Create this IEP and submit it for principal approval? This action cannot be undone.';
                
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
            
            // Auto-resize textareas
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
                
                // Initial resize
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            });
        });
    </script>
</body>
</html>