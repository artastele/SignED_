<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve IEP - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .approval-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .approval-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #10B981;
        }
        
        .approval-title {
            color: #10B981;
            margin: 0 0 10px 0;
        }
        
        .learner-info {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
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
            color: #15803D;
            width: 120px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
        }
        
        .iep-summary {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .summary-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .summary-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .summary-section h4 {
            margin: 0 0 10px 0;
            color: #1E40AF;
            font-size: 16px;
        }
        
        .summary-content {
            color: #374151;
            line-height: 1.5;
            font-size: 14px;
            max-height: 100px;
            overflow: hidden;
            position: relative;
        }
        
        .summary-content.expanded {
            max-height: none;
        }
        
        .expand-btn {
            color: #1E40AF;
            cursor: pointer;
            font-size: 12px;
            text-decoration: underline;
            margin-top: 5px;
            display: inline-block;
        }
        
        .approval-form {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
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
            color: #1E40AF;
        }
        
        .signature-section {
            border: 2px dashed #BFDBFE;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #F8FAFF;
        }
        
        .signature-pad {
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            height: 120px;
            background: white;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
        }
        
        .signature-placeholder {
            color: #6B7280;
            font-style: italic;
        }
        
        .signature-canvas {
            width: 100%;
            height: 118px;
            border-radius: 3px;
        }
        
        .signature-controls {
            margin-top: 10px;
        }
        
        .clear-signature {
            background: #EF4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .clear-signature:hover {
            background: #DC2626;
        }
        
        .approval-checklist {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .checklist-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .checklist-item input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
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
        
        .btn-approve {
            background: #10B981;
            color: white;
        }
        
        .btn-approve:hover {
            background: #059669;
        }
        
        .btn-approve:disabled {
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
        
        .warning-notice {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            color: #92400E;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .approval-impact {
            background: #E0F2FE;
            border: 1px solid #7DD3FC;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .approval-impact h4 {
            margin: 0 0 10px 0;
            color: #0369A1;
        }
        
        .approval-impact ul {
            margin: 0;
            padding-left: 20px;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>IEP Approval</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/list">IEP List</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="approval-container">
            <?php if (isset($error)): ?>
                <div class="error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="approval-header">
                <h2 class="approval-title">IEP Approval Review</h2>
                <p>Please review the IEP document carefully before providing your approval.</p>
            </div>

            <div class="learner-info">
                <h3 style="margin-top: 0; color: #15803D;">Learner Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name:</div>
                        <div class="info-value"><?= htmlspecialchars($iep->first_name . ' ' . $iep->last_name) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth:</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($iep->date_of_birth)) ?></div>
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
                        <div class="info-label">IEP Duration:</div>
                        <div class="info-value">
                            <?= date('M j, Y', strtotime($iep->start_date)) ?> - 
                            <?= date('M j, Y', strtotime($iep->end_date)) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Created:</div>
                        <div class="info-value"><?= date('M j, Y \a\t g:i A', strtotime($iep->created_at)) ?></div>
                    </div>
                </div>
            </div>

            <div class="approval-impact">
                <h4>Impact of Approval</h4>
                <ul>
                    <li>The IEP will become the official education plan for this learner</li>
                    <li>Special education services and accommodations will be implemented</li>
                    <li>Progress monitoring and reporting will begin according to the specified schedule</li>
                    <li>All stakeholders (teachers, parents, guidance) will be notified of the approval</li>
                </ul>
            </div>

            <div class="iep-summary">
                <h3 style="margin-top: 0; color: #1E40AF;">IEP Content Summary</h3>
                
                <div class="summary-section">
                    <h4>Present Level of Performance</h4>
                    <div class="summary-content" id="present-level">
                        <?= htmlspecialchars(substr($iep->present_level_performance, 0, 200)) ?>
                        <?php if (strlen($iep->present_level_performance) > 200): ?>...<?php endif; ?>
                    </div>
                    <?php if (strlen($iep->present_level_performance) > 200): ?>
                        <span class="expand-btn" onclick="toggleExpand('present-level', this)">Show more</span>
                    <?php endif; ?>
                </div>
                
                <div class="summary-section">
                    <h4>Annual Goals</h4>
                    <div class="summary-content" id="annual-goals">
                        <?= htmlspecialchars(substr($iep->annual_goals, 0, 200)) ?>
                        <?php if (strlen($iep->annual_goals) > 200): ?>...<?php endif; ?>
                    </div>
                    <?php if (strlen($iep->annual_goals) > 200): ?>
                        <span class="expand-btn" onclick="toggleExpand('annual-goals', this)">Show more</span>
                    <?php endif; ?>
                </div>
                
                <div class="summary-section">
                    <h4>Special Education Services</h4>
                    <div class="summary-content" id="services">
                        <?= htmlspecialchars(substr($iep->special_education_services, 0, 200)) ?>
                        <?php if (strlen($iep->special_education_services) > 200): ?>...<?php endif; ?>
                    </div>
                    <?php if (strlen($iep->special_education_services) > 200): ?>
                        <span class="expand-btn" onclick="toggleExpand('services', this)">Show more</span>
                    <?php endif; ?>
                </div>
                
                <div class="summary-section">
                    <h4>Accommodations and Modifications</h4>
                    <div class="summary-content" id="accommodations">
                        <?= htmlspecialchars(substr($iep->accommodations, 0, 200)) ?>
                        <?php if (strlen($iep->accommodations) > 200): ?>...<?php endif; ?>
                    </div>
                    <?php if (strlen($iep->accommodations) > 200): ?>
                        <span class="expand-btn" onclick="toggleExpand('accommodations', this)">Show more</span>
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="/iep/view/<?= $iep->id ?>" class="btn btn-secondary" target="_blank">
                        View Complete IEP Document
                    </a>
                </div>
            </div>

            <div class="approval-checklist">
                <h4 style="margin-top: 0; color: #92400E;">Pre-Approval Checklist</h4>
                <p style="font-size: 14px; margin-bottom: 15px;">Please confirm the following before approving:</p>
                
                <div class="checklist-item">
                    <input type="checkbox" id="check1" required>
                    <label for="check1">I have reviewed the learner's assessment results and present level of performance</label>
                </div>
                
                <div class="checklist-item">
                    <input type="checkbox" id="check2" required>
                    <label for="check2">The annual goals are appropriate, measurable, and achievable</label>
                </div>
                
                <div class="checklist-item">
                    <input type="checkbox" id="check3" required>
                    <label for="check3">The special education services and accommodations are suitable for the learner's needs</label>
                </div>
                
                <div class="checklist-item">
                    <input type="checkbox" id="check4" required>
                    <label for="check4">The progress measurement methods are clearly defined and appropriate</label>
                </div>
                
                <div class="checklist-item">
                    <input type="checkbox" id="check5" required>
                    <label for="check5">The IEP duration and timeline are appropriate</label>
                </div>
            </div>

            <div class="warning-notice">
                <strong>Important:</strong> By approving this IEP, you are authorizing the implementation of all specified services, accommodations, and goals. This approval cannot be easily reversed once submitted.
            </div>

            <form method="POST" id="approvalForm">
                <div class="approval-form">
                    <h3 style="margin-top: 0; color: #1E40AF;">Principal Approval</h3>
                    
                    <div class="form-group">
                        <label for="digital_signature">Digital Signature <span style="color: #B91C1C;">*</span></label>
                        <div class="signature-section">
                            <p style="margin: 0 0 10px 0; color: #6B7280; font-size: 14px;">
                                Please provide your digital signature to approve this IEP.
                            </p>
                            
                            <div class="signature-pad" id="signaturePad">
                                <div class="signature-placeholder">Click here to sign</div>
                                <canvas class="signature-canvas" id="signatureCanvas" style="display: none;"></canvas>
                            </div>
                            
                            <div class="signature-controls" style="display: none;" id="signatureControls">
                                <button type="button" class="clear-signature" onclick="clearSignature()">
                                    Clear Signature
                                </button>
                            </div>
                            
                            <input type="hidden" name="digital_signature" id="signatureData" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-approve" id="approveBtn" disabled>
                        Approve IEP
                    </button>
                    <a href="/iep/reject/<?= $iep->id ?>" class="btn btn-secondary" style="margin-left: 15px;">
                        Request Revision
                    </a>
                    <a href="/iep/list" class="btn btn-secondary" style="margin-left: 15px;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let signaturePad = null;
        let canvas = null;
        let ctx = null;
        let isDrawing = false;
        let hasSignature = false;

        function toggleExpand(elementId, button) {
            const element = document.getElementById(elementId);
            const fullContent = {
                'present-level': <?= json_encode($iep->present_level_performance) ?>,
                'annual-goals': <?= json_encode($iep->annual_goals) ?>,
                'services': <?= json_encode($iep->special_education_services) ?>,
                'accommodations': <?= json_encode($iep->accommodations) ?>
            };
            
            if (element.classList.contains('expanded')) {
                element.classList.remove('expanded');
                element.innerHTML = fullContent[elementId].substring(0, 200) + '...';
                button.textContent = 'Show more';
            } else {
                element.classList.add('expanded');
                element.innerHTML = fullContent[elementId];
                button.textContent = 'Show less';
            }
        }

        function initializeSignaturePad() {
            canvas = document.getElementById('signatureCanvas');
            ctx = canvas.getContext('2d');
            
            // Set canvas size
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
            
            ctx.strokeStyle = '#1F2937';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            // Mouse events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);
            
            // Touch events
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouch);
            canvas.addEventListener('touchend', stopDrawing);
        }

        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        function draw(e) {
            if (!isDrawing) return;
            
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ctx.lineTo(x, y);
            ctx.stroke();
            
            hasSignature = true;
            document.getElementById('signatureData').value = canvas.toDataURL();
            checkFormCompletion();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                            e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasSignature = false;
            document.getElementById('signatureData').value = '';
            checkFormCompletion();
        }

        function checkFormCompletion() {
            const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const hasValidSignature = hasSignature && document.getElementById('signatureData').value;
            
            document.getElementById('approveBtn').disabled = !(allChecked && hasValidSignature);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize signature pad when clicked
            document.getElementById('signaturePad').addEventListener('click', function() {
                if (canvas.style.display === 'none') {
                    canvas.style.display = 'block';
                    document.querySelector('.signature-placeholder').style.display = 'none';
                    document.getElementById('signatureControls').style.display = 'block';
                    initializeSignaturePad();
                }
            });
            
            // Add event listeners to checkboxes
            const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', checkFormCompletion);
            });
            
            // Form submission validation
            document.getElementById('approvalForm').addEventListener('submit', function(e) {
                if (!hasSignature) {
                    e.preventDefault();
                    alert('Please provide your digital signature before approving.');
                    return;
                }
                
                const checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                
                if (!allChecked) {
                    e.preventDefault();
                    alert('Please complete all checklist items before approving.');
                    return;
                }
                
                if (!confirm('Are you sure you want to approve this IEP? This action will authorize the implementation of all specified services and accommodations.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>