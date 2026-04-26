<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'Assessment Results' ?> - SignED SPED</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/assets/css/style.css">
    <style>
        .assessment-view {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .assessment-container {
            background: white;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .assessment-header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 24px;
            margin-bottom: 32px;
        }
        
        .learner-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 32px;
        }
        
        .learner-info h3 {
            margin: 0 0 12px 0;
            color: #1e40af;
            font-size: 1.25rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }
        
        .info-item {
            font-size: 0.875rem;
        }
        
        .info-label {
            font-weight: 500;
            color: #374151;
        }
        
        .info-value {
            color: #6b7280;
        }
        
        .assessment-section {
            margin-bottom: 32px;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-icon {
            font-size: 1.125rem;
        }
        
        .assessment-content {
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            font-size: 0.875rem;
            line-height: 1.6;
            color: #374151;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .assessment-content:empty::before {
            content: "No assessment data recorded.";
            color: #9ca3af;
            font-style: italic;
        }
        
        .assessment-meta {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 20px;
            margin-top: 32px;
        }
        
        .meta-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #0c4a6e;
            margin-bottom: 12px;
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
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
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .page-description {
            color: #6b7280;
            margin-bottom: 24px;
        }
        
        .readonly-badge {
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 12px;
        }
        
        .action-buttons {
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
            color: white;
            text-decoration: none;
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
        
        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-size: 0.875rem;
        }
        
        .print-button {
            background: #059669;
            color: white;
        }
        
        .print-button:hover {
            background: #047857;
            color: white;
            text-decoration: none;
        }
        
        /* Print styles */
        @media print {
            .breadcrumb,
            .action-buttons,
            .print-button {
                display: none !important;
            }
            
            .assessment-container {
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .page-title::after {
                content: " - CONFIDENTIAL DOCUMENT";
                font-size: 0.75rem;
                color: #dc2626;
            }
        }
        
        /* Accessibility improvements */
        .btn:focus {
            outline: 2px solid #1e40af;
            outline-offset: 2px;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .assessment-container {
                border: 2px solid #000;
            }
            
            .assessment-content {
                border: 2px solid #000;
            }
            
            .btn {
                border: 2px solid #000;
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .btn {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <div class="assessment-view">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= URLROOT ?>/sped/dashboard">SPED Dashboard</a> &gt; 
            <a href="<?= URLROOT ?>/assessment/list">Assessment List</a> &gt; 
            <span aria-current="page">Assessment Results</span>
        </nav>
        
        <div class="assessment-container">
            <!-- Success Message -->
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" role="alert">
                    <strong>Success:</strong> <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>
            
            <!-- Assessment Header -->
            <header class="assessment-header">
                <h1 class="page-title">
                    Initial Assessment Results
                    <?php if ($data['readonly']): ?>
                        <span class="readonly-badge" aria-label="Read-only document">Read-Only</span>
                    <?php endif; ?>
                </h1>
                <p class="page-description">
                    Comprehensive initial assessment completed for special education services evaluation.
                </p>
            </header>
            
            <!-- Learner Information -->
            <div class="learner-info">
                <h3>Learner Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name:</div>
                        <div class="info-value"><?= htmlspecialchars($data['assessment']->first_name . ' ' . $data['assessment']->last_name) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Assessment Date:</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($data['assessment']->assessment_date)) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Assessed By:</div>
                        <div class="info-value"><?= htmlspecialchars($data['assessment']->assessor_name) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Assessment ID:</div>
                        <div class="info-value">#<?= $data['assessment']->id ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Assessment Sections -->
            <main role="main">
                <!-- Cognitive Ability -->
                <section class="assessment-section">
                    <h2 class="section-title">
                        <span class="section-icon" aria-hidden="true">🧠</span>
                        Cognitive Ability Assessment
                    </h2>
                    <div class="assessment-content" aria-label="Cognitive ability assessment results">
                        <?= htmlspecialchars($data['assessment']->cognitive_ability) ?>
                    </div>
                </section>
                
                <!-- Communication Skills -->
                <section class="assessment-section">
                    <h2 class="section-title">
                        <span class="section-icon" aria-hidden="true">💬</span>
                        Communication Skills Assessment
                    </h2>
                    <div class="assessment-content" aria-label="Communication skills assessment results">
                        <?= htmlspecialchars($data['assessment']->communication_skills) ?>
                    </div>
                </section>
                
                <!-- Social-Emotional Development -->
                <section class="assessment-section">
                    <h2 class="section-title">
                        <span class="section-icon" aria-hidden="true">🤝</span>
                        Social-Emotional Development Assessment
                    </h2>
                    <div class="assessment-content" aria-label="Social-emotional development assessment results">
                        <?= htmlspecialchars($data['assessment']->social_emotional_development) ?>
                    </div>
                </section>
                
                <!-- Adaptive Behavior -->
                <section class="assessment-section">
                    <h2 class="section-title">
                        <span class="section-icon" aria-hidden="true">🎯</span>
                        Adaptive Behavior Assessment
                    </h2>
                    <div class="assessment-content" aria-label="Adaptive behavior assessment results">
                        <?= htmlspecialchars($data['assessment']->adaptive_behavior) ?>
                    </div>
                </section>
                
                <!-- Academic Performance -->
                <section class="assessment-section">
                    <h2 class="section-title">
                        <span class="section-icon" aria-hidden="true">📚</span>
                        Academic Performance Assessment
                    </h2>
                    <div class="assessment-content" aria-label="Academic performance assessment results">
                        <?= htmlspecialchars($data['assessment']->academic_performance) ?>
                    </div>
                </section>
                
                <!-- Recommendations -->
                <?php if (!empty($data['assessment']->recommendations)): ?>
                <section class="assessment-section">
                    <h2 class="section-title">
                        <span class="section-icon" aria-hidden="true">💡</span>
                        Assessment Recommendations
                    </h2>
                    <div class="assessment-content" aria-label="Assessment recommendations">
                        <?= htmlspecialchars($data['assessment']->recommendations) ?>
                    </div>
                </section>
                <?php endif; ?>
            </main>
            
            <!-- Assessment Metadata -->
            <div class="assessment-meta">
                <h3 class="meta-title">Assessment Information</h3>
                <div class="meta-grid">
                    <div class="info-item">
                        <div class="info-label">Created:</div>
                        <div class="info-value"><?= date('F j, Y \a\t g:i A', strtotime($data['assessment']->created_at)) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Updated:</div>
                        <div class="info-value"><?= date('F j, Y \a\t g:i A', strtotime($data['assessment']->updated_at)) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Document Status:</div>
                        <div class="info-value">Completed & Encrypted</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Classification:</div>
                        <div class="info-value">Restricted - SPED Assessment</div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?= URLROOT ?>/assessment/list" class="btn btn-secondary">
                    Back to Assessment List
                </a>
                
                <button onclick="window.print()" class="btn print-button">
                    Print Assessment
                </button>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'sped_teacher'): ?>
                    <a href="<?= URLROOT ?>/iep/create?learner_id=<?= $data['assessment']->learner_id ?>" class="btn btn-primary">
                        Create IEP
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add watermark for restricted document viewing
            const watermark = document.createElement('div');
            watermark.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 6rem;
                color: rgba(220, 38, 38, 0.1);
                font-weight: bold;
                pointer-events: none;
                z-index: -1;
                user-select: none;
            `;
            watermark.textContent = 'CONFIDENTIAL';
            document.body.appendChild(watermark);
            
            // Disable right-click context menu for security
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
            
            // Disable text selection for security
            document.addEventListener('selectstart', function(e) {
                if (e.target.closest('.assessment-content')) {
                    e.preventDefault();
                }
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+P for print
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
                
                // Escape to go back
                if (e.key === 'Escape') {
                    window.location.href = '<?= URLROOT ?>/assessment/list';
                }
            });
            
            // Focus management for accessibility
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.setAttribute('tabindex', '-1');
                mainContent.focus();
            }
        });
    </script>
</body>
</html>