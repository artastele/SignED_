<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request IEP Revision - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .rejection-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .rejection-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #EF4444;
        }
        
        .rejection-title {
            color: #EF4444;
            margin: 0 0 10px 0;
        }
        
        .learner-info {
            background: #FEF2F2;
            border: 1px solid #FECACA;
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
            color: #B91C1C;
            width: 120px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
        }
        
        .rejection-form {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #92400E;
        }
        
        .form-group textarea {
            width: 100%;
            min-height: 150px;
            padding: 12px;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #F59E0B;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
        
        .character-count {
            font-size: 12px;
            color: #6B7280;
            text-align: right;
            margin-top: 5px;
        }
        
        .rejection-guidelines {
            background: #F0F9FF;
            border: 1px solid #BAE6FD;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .rejection-guidelines h4 {
            margin: 0 0 15px 0;
            color: #0369A1;
        }
        
        .rejection-guidelines ul {
            margin: 0;
            padding-left: 20px;
            color: #374151;
        }
        
        .rejection-guidelines li {
            margin-bottom: 8px;
        }
        
        .impact-notice {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            color: #92400E;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .impact-notice h4 {
            margin: 0 0 10px 0;
            color: #92400E;
        }
        
        .impact-notice ul {
            margin: 0;
            padding-left: 20px;
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
        
        .btn-reject {
            background: #EF4444;
            color: white;
        }
        
        .btn-reject:hover {
            background: #DC2626;
        }
        
        .btn-reject:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: #6B7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4B5563;
        }
        
        .error {
            background: #FEE2E2;
            color: #B91C1C;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }
        
        .required {
            color: #B91C1C;
        }
        
        .quick-reasons {
            margin-bottom: 15px;
        }
        
        .quick-reasons h5 {
            margin: 0 0 10px 0;
            color: #92400E;
            font-size: 14px;
        }
        
        .reason-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .reason-btn {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            color: #92400E;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .reason-btn:hover {
            background: #FDE68A;
        }
        
        .reason-btn.selected {
            background: #F59E0B;
            color: white;
        }
        
        .iep-preview {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .preview-section {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .preview-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .preview-section h5 {
            margin: 0 0 8px 0;
            color: #1E40AF;
            font-size: 14px;
        }
        
        .preview-content {
            color: #6B7280;
            font-size: 13px;
            line-height: 1.4;
            max-height: 60px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Request IEP Revision</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/list">IEP List</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="rejection-container">
            <?php if (isset($error)): ?>
                <div class="error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="rejection-header">
                <h2 class="rejection-title">Request IEP Revision</h2>
                <p>Please provide specific feedback to help improve the IEP document.</p>
            </div>

            <div class="learner-info">
                <h3 style="margin-top: 0; color: #B91C1C;">IEP Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Learner:</div>
                        <div class="info-value"><?= htmlspecialchars($iep->first_name . ' ' . $iep->last_name) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Grade Level:</div>
                        <div class="info-value"><?= htmlspecialchars($iep->grade_level) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Created by:</div>
                        <div class="info-value"><?= htmlspecialchars($iep->created_by_name) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Created:</div>
                        <div class="info-value"><?= date('M j, Y \a\t g:i A', strtotime($iep->created_at)) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">IEP Duration:</div>
                        <div class="info-value">
                            <?= date('M j, Y', strtotime($iep->start_date)) ?> - 
                            <?= date('M j, Y', strtotime($iep->end_date)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="iep-preview">
                <h4 style="margin-top: 0; color: #1E40AF;">IEP Content Overview</h4>
                
                <div class="preview-section">
                    <h5>Present Level of Performance</h5>
                    <div class="preview-content">
                        <?= htmlspecialchars(substr($iep->present_level_performance, 0, 150)) ?>...
                    </div>
                </div>
                
                <div class="preview-section">
                    <h5>Annual Goals</h5>
                    <div class="preview-content">
                        <?= htmlspecialchars(substr($iep->annual_goals, 0, 150)) ?>...
                    </div>
                </div>
                
                <div class="preview-section">
                    <h5>Special Education Services</h5>
                    <div class="preview-content">
                        <?= htmlspecialchars(substr($iep->special_education_services, 0, 150)) ?>...
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 15px;">
                    <a href="/iep/view/<?= $iep->id ?>" class="btn btn-secondary" target="_blank" style="padding: 6px 12px; font-size: 12px;">
                        View Complete IEP
                    </a>
                </div>
            </div>

            <div class="rejection-guidelines">
                <h4>Guidelines for Constructive Feedback</h4>
                <ul>
                    <li>Be specific about which sections need improvement</li>
                    <li>Provide clear, actionable suggestions for revision</li>
                    <li>Focus on educational appropriateness and compliance requirements</li>
                    <li>Consider the learner's individual needs and circumstances</li>
                    <li>Reference relevant assessment data or meeting discussions</li>
                    <li>Suggest alternative approaches where applicable</li>
                </ul>
            </div>

            <div class="impact-notice">
                <h4>Impact of Requesting Revision</h4>
                <ul>
                    <li>The IEP will be returned to the SPED teacher for revision</li>
                    <li>Implementation of services will be delayed until approval</li>
                    <li>The SPED teacher will be notified via email with your feedback</li>
                    <li>A revised IEP must be resubmitted for your approval</li>
                </ul>
            </div>

            <form method="POST" id="rejectionForm">
                <div class="rejection-form">
                    <h3 style="margin-top: 0; color: #92400E;">Revision Request Details</h3>
                    
                    <div class="form-group">
                        <div class="quick-reasons">
                            <h5>Common Revision Areas (click to add to feedback):</h5>
                            <div class="reason-buttons">
                                <button type="button" class="reason-btn" data-reason="Goals need to be more specific and measurable">
                                    Goals Specificity
                                </button>
                                <button type="button" class="reason-btn" data-reason="Services and accommodations need clarification">
                                    Services Clarity
                                </button>
                                <button type="button" class="reason-btn" data-reason="Progress measurement methods need improvement">
                                    Progress Measurement
                                </button>
                                <button type="button" class="reason-btn" data-reason="Present level of performance needs more detail">
                                    Performance Details
                                </button>
                                <button type="button" class="reason-btn" data-reason="Timeline and duration need adjustment">
                                    Timeline Issues
                                </button>
                                <button type="button" class="reason-btn" data-reason="Assessment data integration needs improvement">
                                    Assessment Integration
                                </button>
                            </div>
                        </div>
                        
                        <label for="rejection_reason">
                            Detailed Feedback and Revision Requirements <span class="required">*</span>
                        </label>
                        <textarea name="rejection_reason" 
                                  id="rejection_reason" 
                                  required
                                  placeholder="Please provide specific, constructive feedback about what needs to be revised in this IEP. Include section-specific comments and suggestions for improvement..."><?= htmlspecialchars($_POST['rejection_reason'] ?? '') ?></textarea>
                        <div class="character-count" id="reasonCount">0 characters (minimum 50 required)</div>
                        <div style="font-size: 12px; color: #6B7280; margin-top: 8px; font-style: italic;">
                            Your feedback will be sent to the SPED teacher to guide their revision process.
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-reject" id="rejectBtn" disabled>
                        Request Revision
                    </button>
                    <a href="/iep/approve/<?= $iep->id ?>" class="btn btn-secondary" style="margin-left: 15px;">
                        Approve Instead
                    </a>
                    <a href="/iep/list" class="btn btn-secondary" style="margin-left: 15px;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('rejection_reason');
            const countElement = document.getElementById('reasonCount');
            const rejectBtn = document.getElementById('rejectBtn');
            const reasonButtons = document.querySelectorAll('.reason-btn');
            
            // Character counting and validation
            function updateCount() {
                const length = textarea.value.length;
                countElement.textContent = length + ' characters' + (length < 50 ? ' (minimum 50 required)' : '');
                
                // Enable/disable submit button based on minimum length
                rejectBtn.disabled = length < 50;
                
                if (length < 50) {
                    countElement.style.color = '#EF4444';
                } else {
                    countElement.style.color = '#10B981';
                }
            }
            
            textarea.addEventListener('input', updateCount);
            updateCount(); // Initial count
            
            // Quick reason buttons
            reasonButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const reason = this.dataset.reason;
                    const currentValue = textarea.value;
                    
                    // Toggle button selection
                    this.classList.toggle('selected');
                    
                    if (this.classList.contains('selected')) {
                        // Add reason to textarea
                        const newValue = currentValue ? currentValue + '\n\n• ' + reason : '• ' + reason;
                        textarea.value = newValue;
                    } else {
                        // Remove reason from textarea
                        const reasonLine = '• ' + reason;
                        const newValue = currentValue.replace(new RegExp('\\n\\n?' + reasonLine.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), '')
                                                    .replace(new RegExp('^' + reasonLine.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), '')
                                                    .trim();
                        textarea.value = newValue;
                    }
                    
                    updateCount();
                });
            });
            
            // Auto-resize textarea
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Form submission validation
            document.getElementById('rejectionForm').addEventListener('submit', function(e) {
                const reason = textarea.value.trim();
                
                if (reason.length < 50) {
                    e.preventDefault();
                    alert('Please provide at least 50 characters of detailed feedback.');
                    textarea.focus();
                    return;
                }
                
                if (!confirm('Are you sure you want to request revision of this IEP? The SPED teacher will be notified and will need to make changes before resubmitting for approval.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>