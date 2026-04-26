<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Status - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        .status-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .enrollments-grid {
            display: grid;
            gap: 1.5rem;
        }
        
        .enrollment-card {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 1.5rem;
            background: white;
            transition: box-shadow 0.2s;
        }
        
        .enrollment-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .enrollment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .student-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
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
        
        .enrollment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.875rem;
            color: #6B7280;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-weight: 500;
            color: #374151;
        }
        
        .document-progress {
            margin: 1rem 0;
        }
        
        .progress-bar {
            background-color: #E5E7EB;
            border-radius: 9999px;
            height: 8px;
            margin: 0.5rem 0;
        }
        
        .progress-fill {
            background-color: #10B981;
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            font-size: 0.875rem;
            color: #6B7280;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
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
        
        .btn-success {
            background-color: #10B981;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6B7280;
        }
        
        .empty-state svg {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1rem;
            opacity: 0.5;
        }
        
        .rejection-reason {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 4px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .rejection-reason h4 {
            color: #B91C1C;
            margin-bottom: 0.5rem;
        }
        
        .rejection-reason p {
            color: #991B1B;
            margin: 0;
        }
        
        .timeline {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #E5E7EB;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6B7280;
        }
        
        .timeline-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10B981;
        }
    </style>
</head>
<body>
    <div class="status-container">
        <div class="header-section">
            <div>
                <h1>My Enrollment Applications</h1>
                <p>Track the status of your SPED enrollment submissions</p>
            </div>
            <div>
                <a href="<?php echo URLROOT; ?>/enrollment/submit" class="btn btn-primary">New Enrollment</a>
                <a href="<?php echo URLROOT; ?>/parent/dashboard" class="btn btn-secondary" style="margin-left: 0.5rem;">Back to Dashboard</a>
            </div>
        </div>
        
        <?php if (empty($enrollments)): ?>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3>No Enrollment Applications</h3>
                <p>You haven't submitted any SPED enrollment applications yet.</p>
                <a href="<?php echo URLROOT; ?>/enrollment/submit" class="btn btn-primary" style="margin-top: 1rem;">Start New Enrollment</a>
            </div>
        <?php else: ?>
            <div class="enrollments-grid">
                <?php foreach ($enrollments as $enrollment): ?>
                    <div class="enrollment-card">
                        <div class="enrollment-header">
                            <div class="student-name">
                                <?php echo htmlspecialchars($enrollment->learner_first_name . ' ' . $enrollment->learner_last_name); ?>
                            </div>
                            <div class="status-badge status-<?php echo $enrollment->status; ?>">
                                <?php echo ucwords(str_replace('_', ' ', $enrollment->status)); ?>
                            </div>
                        </div>
                        
                        <div class="enrollment-details">
                            <div class="detail-item">
                                <div class="detail-label">Grade Level</div>
                                <div class="detail-value"><?php echo htmlspecialchars($enrollment->learner_grade); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Date of Birth</div>
                                <div class="detail-value"><?php echo date('M j, Y', strtotime($enrollment->learner_dob)); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Submitted</div>
                                <div class="detail-value"><?php echo date('M j, Y', strtotime($enrollment->created_at)); ?></div>
                            </div>
                            <?php if ($enrollment->verified_at): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Processed</div>
                                    <div class="detail-value"><?php echo date('M j, Y', strtotime($enrollment->verified_at)); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($enrollment->status === 'pending_documents' || $enrollment->status === 'pending_verification'): ?>
                            <div class="document-progress">
                                <?php
                                $documentCount = $enrollment->document_count ?? 0;
                                $totalRequired = 4;
                                $progressPercent = ($documentCount / $totalRequired) * 100;
                                ?>
                                <div class="progress-text">
                                    Documents: <?php echo $documentCount; ?>/<?php echo $totalRequired; ?> uploaded
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $progressPercent; ?>%"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($enrollment->status === 'rejected' && $enrollment->rejection_reason): ?>
                            <div class="rejection-reason">
                                <h4>Rejection Reason</h4>
                                <p><?php echo htmlspecialchars($enrollment->rejection_reason); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <span>Application submitted on <?php echo date('M j, Y g:i A', strtotime($enrollment->created_at)); ?></span>
                            </div>
                            
                            <?php if ($enrollment->status === 'pending_verification'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <span>All documents uploaded - pending verification</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($enrollment->verified_at): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <span>
                                        <?php echo $enrollment->status === 'approved' ? 'Approved' : 'Rejected'; ?> 
                                        on <?php echo date('M j, Y g:i A', strtotime($enrollment->verified_at)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="actions">
                            <?php if ($enrollment->status === 'pending_documents'): ?>
                                <a href="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $enrollment->id; ?>" 
                                   class="btn btn-primary">Upload Documents</a>
                            <?php elseif ($enrollment->status === 'pending_verification'): ?>
                                <span class="btn btn-secondary" style="opacity: 0.7; cursor: not-allowed;">
                                    Awaiting Verification
                                </span>
                            <?php elseif ($enrollment->status === 'approved'): ?>
                                <span class="btn btn-success" style="opacity: 0.7; cursor: not-allowed;">
                                    Enrollment Approved
                                </span>
                            <?php elseif ($enrollment->status === 'rejected'): ?>
                                <a href="<?php echo URLROOT; ?>/enrollment/submit" class="btn btn-primary">
                                    Submit New Application
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 2rem; padding: 1rem; background-color: #F3F4F6; border-radius: 4px;">
            <h4>Status Definitions:</h4>
            <ul style="margin: 0.5rem 0 0 1rem; color: #6B7280;">
                <li><strong>Pending Documents:</strong> Upload all required documents (PSA, PWD ID, Medical Records, BEEF)</li>
                <li><strong>Pending Verification:</strong> Documents submitted and awaiting SPED staff review</li>
                <li><strong>Approved:</strong> Enrollment approved - you will be contacted for next steps</li>
                <li><strong>Rejected:</strong> Enrollment rejected - see reason above and resubmit if needed</li>
            </ul>
        </div>
    </div>
</body>
</html>