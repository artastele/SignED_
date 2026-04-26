<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'Initial Assessment' ?> - SignED SPED</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/assets/css/style.css">
    <style>
        .assessment-form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-container {
            background: white;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .learner-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 32px;
        }
        
        .learner-info h3 {
            margin: 0 0 8px 0;
            color: #1e40af;
            font-size: 1.125rem;
        }
        
        .learner-details {
            color: #64748b;
            font-size: 0.875rem;
        }
        
        .form-section {
            margin-bottom: 32px;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.875rem;
        }
        
        .required {
            color: #dc2626;
        }
        
        .form-textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            font-family: inherit;
            resize: vertical;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-textarea:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .char-counter {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: right;
            margin-top: 4px;
        }
        
        .char-counter.warning {
            color: #f59e0b;
        }
        
        .char-counter.error {
            color: #dc2626;
        }
        
        .form-actions {
            display: flex;
            gap: 16px;
            justify-content: flex-end;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #1e40af;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
        }
        
        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
            color: white;
            text-decoration: none;
        }
        
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-size: 0.875rem;
        }
        
        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-size: 0.875rem;
        }
        
        .breadcrumb {
            margin-bottom: 24px;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .breadcrumb a {
            color: #1e40af;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .help-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.4;
        }
        
        /* Accessibility improvements */
        .form-textarea:invalid,
        .form-input:invalid {
            border-color: #dc2626;
        }
        
        .btn:focus {
            outline: 2px solid #1e40af;
            outline-offset: 2px;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .form-container {
                border: 2px solid #000;
            }
            
            .form-textarea,
            .form-input {
                border: 2px solid #000;
            }
            
            .btn {
                border: 2px solid #000;
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .form-textarea,
            .form-input,
            .btn {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <div class="assessment-form">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= URLROOT ?>/sped/dashboard">SPED Dashboard</a> &gt; 
            <a href="<?= URLROOT ?>/assessment/list">Assessment List</a> &gt; 
            <span aria-current="page">Initial Assessment</span>
        </nav>
        
        <div class="form-container">
            <!-- Page Header -->
            <header>
                <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 8px;">
                    Initial Assessment
                </h1>
                <p style="color: #6b7280; margin-bottom: 24px;">
                    Complete the comprehensive initial assessment for the learner. All fields marked with * are required.
                </p>
            </header>
            
            <!-- Error/Success Messages -->
            <?php if (isset($data['error'])): ?>
                <div class="error-message" role="alert">
                    <strong>Error:</strong> <?= htmlspecialchars($data['error']) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($data['success'])): ?>
                <div class="success-message" role="alert">
                    <strong>Success:</strong> <?= htmlspecialchars($data['success']) ?>
                </div>
            <?php endif; ?>
            
            <!-- Learner Information -->
            <div class="learner-info">
                <h3>Learner Information</h3>
                <div class="learner-details">
                    <strong><?= htmlspecialchars($data['learner']->first_name . ' ' . $data['learner']->last_name) ?></strong><br>
                    Grade: <?= htmlspecialchars($data['learner']->grade_level) ?> | 
                    Parent: <?= htmlspecialchars($data['learner']->parent_name) ?> | 
                    Enrolled: <?= date('M j, Y', strtotime($data['learner']->created_at)) ?>
                </div>
            </div>
            
            <!-- Assessment Form -->
            <form method="POST" action="<?= URLROOT ?>/assessment/create?learner_id=<?= $data['learner']->id ?>" 
                  novalidate aria-label="Initial Assessment Form">
                
                <input type="hidden" name="learner_id" value="<?= $data['learner']->id ?>">
                
                <!-- Cognitive Ability Section -->
                <div class="form-section">
                    <h2 class="section-title">Cognitive Ability Assessment</h2>
                    <div class="form-group">
                        <label for="cognitive_ability" class="form-label">
                            Cognitive Ability Evaluation <span class="required" aria-label="required">*</span>
                        </label>
                        <textarea 
                            id="cognitive_ability" 
                            name="cognitive_ability" 
                            class="form-textarea" 
                            required 
                            minlength="10" 
                            maxlength="5000"
                            aria-describedby="cognitive_help cognitive_counter"
                            placeholder="Describe the learner's cognitive abilities, problem-solving skills, memory, attention span, and processing speed..."><?= htmlspecialchars($_POST['cognitive_ability'] ?? '') ?></textarea>
                        <div id="cognitive_help" class="help-text">
                            Assess cognitive functions including memory, attention, problem-solving, and processing speed. Minimum 10 characters required.
                        </div>
                        <div id="cognitive_counter" class="char-counter">0 / 5000 characters</div>
                    </div>
                </div>
                
                <!-- Communication Skills Section -->
                <div class="form-section">
                    <h2 class="section-title">Communication Skills Assessment</h2>
                    <div class="form-group">
                        <label for="communication_skills" class="form-label">
                            Communication Skills Evaluation <span class="required" aria-label="required">*</span>
                        </label>
                        <textarea 
                            id="communication_skills" 
                            name="communication_skills" 
                            class="form-textarea" 
                            required 
                            minlength="10" 
                            maxlength="5000"
                            aria-describedby="communication_help communication_counter"
                            placeholder="Evaluate verbal and non-verbal communication abilities, language comprehension, expression skills..."><?= htmlspecialchars($_POST['communication_skills'] ?? '') ?></textarea>
                        <div id="communication_help" class="help-text">
                            Evaluate both receptive and expressive language skills, including verbal and non-verbal communication.
                        </div>
                        <div id="communication_counter" class="char-counter">0 / 5000 characters</div>
                    </div>
                </div>
                
                <!-- Social-Emotional Development Section -->
                <div class="form-section">
                    <h2 class="section-title">Social-Emotional Development Assessment</h2>
                    <div class="form-group">
                        <label for="social_emotional_development" class="form-label">
                            Social-Emotional Development Evaluation <span class="required" aria-label="required">*</span>
                        </label>
                        <textarea 
                            id="social_emotional_development" 
                            name="social_emotional_development" 
                            class="form-textarea" 
                            required 
                            minlength="10" 
                            maxlength="5000"
                            aria-describedby="social_help social_counter"
                            placeholder="Assess social interaction skills, emotional regulation, self-awareness, empathy, and relationship building..."><?= htmlspecialchars($_POST['social_emotional_development'] ?? '') ?></textarea>
                        <div id="social_help" class="help-text">
                            Assess social skills, emotional regulation, self-awareness, and ability to form relationships.
                        </div>
                        <div id="social_counter" class="char-counter">0 / 5000 characters</div>
                    </div>
                </div>
                
                <!-- Adaptive Behavior Section -->
                <div class="form-section">
                    <h2 class="section-title">Adaptive Behavior Assessment</h2>
                    <div class="form-group">
                        <label for="adaptive_behavior" class="form-label">
                            Adaptive Behavior Evaluation <span class="required" aria-label="required">*</span>
                        </label>
                        <textarea 
                            id="adaptive_behavior" 
                            name="adaptive_behavior" 
                            class="form-textarea" 
                            required 
                            minlength="10" 
                            maxlength="5000"
                            aria-describedby="adaptive_help adaptive_counter"
                            placeholder="Evaluate daily living skills, self-care abilities, independence level, and practical life skills..."><?= htmlspecialchars($_POST['adaptive_behavior'] ?? '') ?></textarea>
                        <div id="adaptive_help" class="help-text">
                            Evaluate daily living skills, self-care, independence, and practical life skills appropriate for age.
                        </div>
                        <div id="adaptive_counter" class="char-counter">0 / 5000 characters</div>
                    </div>
                </div>
                
                <!-- Academic Performance Section -->
                <div class="form-section">
                    <h2 class="section-title">Academic Performance Assessment</h2>
                    <div class="form-group">
                        <label for="academic_performance" class="form-label">
                            Academic Performance Evaluation <span class="required" aria-label="required">*</span>
                        </label>
                        <textarea 
                            id="academic_performance" 
                            name="academic_performance" 
                            class="form-textarea" 
                            required 
                            minlength="10" 
                            maxlength="5000"
                            aria-describedby="academic_help academic_counter"
                            placeholder="Assess current academic skills in reading, writing, mathematics, and other subject areas..."><?= htmlspecialchars($_POST['academic_performance'] ?? '') ?></textarea>
                        <div id="academic_help" class="help-text">
                            Assess current academic abilities across all relevant subject areas and grade-level expectations.
                        </div>
                        <div id="academic_counter" class="char-counter">0 / 5000 characters</div>
                    </div>
                </div>
                
                <!-- Recommendations Section -->
                <div class="form-section">
                    <h2 class="section-title">Recommendations</h2>
                    <div class="form-group">
                        <label for="recommendations" class="form-label">
                            Assessment Recommendations
                        </label>
                        <textarea 
                            id="recommendations" 
                            name="recommendations" 
                            class="form-textarea" 
                            maxlength="5000"
                            aria-describedby="recommendations_help recommendations_counter"
                            placeholder="Provide recommendations for educational interventions, support services, or additional assessments..."><?= htmlspecialchars($_POST['recommendations'] ?? '') ?></textarea>
                        <div id="recommendations_help" class="help-text">
                            Optional recommendations for educational interventions, support services, or further assessments.
                        </div>
                        <div id="recommendations_counter" class="char-counter">0 / 5000 characters</div>
                    </div>
                </div>
                
                <!-- Assessment Date -->
                <div class="form-section">
                    <h2 class="section-title">Assessment Information</h2>
                    <div class="form-group">
                        <label for="assessment_date" class="form-label">
                            Assessment Date <span class="required" aria-label="required">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="assessment_date" 
                            name="assessment_date" 
                            class="form-input" 
                            required 
                            max="<?= date('Y-m-d') ?>"
                            value="<?= $_POST['assessment_date'] ?? $data['assessment_date'] ?>"
                            aria-describedby="date_help">
                        <div id="date_help" class="help-text">
                            Date when the assessment was conducted. Cannot be in the future.
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?= URLROOT ?>/assessment/list" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        Complete Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter functionality
            const textareas = document.querySelectorAll('.form-textarea');
            
            textareas.forEach(textarea => {
                const counterId = textarea.getAttribute('aria-describedby').split(' ').find(id => id.includes('counter'));
                const counter = document.getElementById(counterId);
                const maxLength = parseInt(textarea.getAttribute('maxlength')) || 5000;
                
                function updateCounter() {
                    const length = textarea.value.length;
                    counter.textContent = `${length} / ${maxLength} characters`;
                    
                    // Update counter styling based on length
                    counter.classList.remove('warning', 'error');
                    if (length > maxLength * 0.9) {
                        counter.classList.add('warning');
                    }
                    if (length >= maxLength) {
                        counter.classList.add('error');
                    }
                }
                
                textarea.addEventListener('input', updateCounter);
                updateCounter(); // Initial count
            });
            
            // Form validation
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submit-btn');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                    } else {
                        field.classList.remove('error');
                    }
                    
                    // Check minimum length for textareas
                    if (field.tagName === 'TEXTAREA' && field.value.trim().length < 10) {
                        isValid = false;
                        field.classList.add('error');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields with at least 10 characters for assessment sections.');
                    return false;
                }
                
                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving Assessment...';
            });
            
            // Auto-save functionality (optional enhancement)
            let autoSaveTimeout;
            const autoSaveFields = form.querySelectorAll('textarea, input[type="date"]');
            
            autoSaveFields.forEach(field => {
                field.addEventListener('input', function() {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(() => {
                        // Save to localStorage as backup
                        const formData = new FormData(form);
                        const data = {};
                        for (let [key, value] of formData.entries()) {
                            data[key] = value;
                        }
                        localStorage.setItem('assessment_draft_' + <?= $data['learner']->id ?>, JSON.stringify(data));
                    }, 2000);
                });
            });
            
            // Load draft data if available
            const draftData = localStorage.getItem('assessment_draft_' + <?= $data['learner']->id ?>);
            if (draftData && !form.querySelector('textarea').value) {
                const data = JSON.parse(draftData);
                Object.keys(data).forEach(key => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field && !field.value) {
                        field.value = data[key];
                        // Trigger counter update for textareas
                        if (field.tagName === 'TEXTAREA') {
                            field.dispatchEvent(new Event('input'));
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>