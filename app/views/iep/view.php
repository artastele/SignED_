<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View IEP - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .iep-container {
            max-width: 900px;
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
        
        .iep-title {
            color: #1E40AF;
            margin: 0 0 10px 0;
        }
        
        .iep-status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .status-pending_approval {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .status-approved {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .status-rejected {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .status-active {
            background: #E0E7FF;
            color: #3730A3;
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
        
        .iep-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
        }
        
        .iep-section h3 {
            margin: 0 0 15px 0;
            color: #1E40AF;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 8px;
        }
        
        .iep-content {
            line-height: 1.6;
            color: #374151;
            white-space: pre-wrap;
        }
        
        .approval-info {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .approval-info h4 {
            margin: 0 0 10px 0;
            color: #1E40AF;
        }
        
        .rejection-info {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .rejection-info h4 {
            margin: 0 0 10px 0;
            color: #B91C1C;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
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
        
        .btn-approve {
            background: #10B981;
            color: white;
        }
        
        .btn-approve:hover {
            background: #059669;
        }
        
        .btn-reject {
            background: #EF4444;
            color: white;
        }
        
        .btn-reject:hover {
            background: #DC2626;
        }
        
        .actions-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }
        
        .print-btn {
            background: #F59E0B;
            color: white;
        }
        
        .print-btn:hover {
            background: #D97706;
        }
        
        .duration-info {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .duration-info h4 {
            margin: 0 0 10px 0;
            color: #15803D;
        }
        
        .metadata {
            background: #F9FAFB;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #6B7280;
        }
        
        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .metadata-item {
            display: flex;
        }
        
        .metadata-label {
            font-weight: bold;
            width: 100px;
            flex-shrink: 0;
        }
        
        .metadata-value {
            flex: 1;
        }
        
        @media print {
            .header, .actions-section {
                display: none;
            }
            
            .iep-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>IEP Document</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/list">IEP List</a>
                <a href="/iep/meetings">Meetings</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="iep-container">
            <div class="iep-header">
                <h2 class="iep-title">Individualized Education Plan (IEP)</h2>
                <p>Learner: <strong><?= htmlspecialchars($iep->first_name . ' ' . $iep->last_name) ?></strong></p>
                <div class="iep-status status-<?= $iep->status ?>">
                    <?= ucfirst(str_replace('_', ' ', $iep->status)) ?>
                </div>
            </div>

            <div class="learner-info">
                <h3 style="margin-top: 0; color: #1E40AF;">Learner Information</h3>
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
                </div>
            </div>

            <div class="duration-info">
                <h4>IEP Duration</h4>
                <p>
                    <strong>Start Date:</strong> <?= date('F j, Y', strtotime($iep->start_date)) ?><br>
                    <strong>End Date:</strong> <?= date('F j, Y', strtotime($iep->end_date)) ?><br>
                    <strong>Duration:</strong> 
                    <?php
                    $start = new DateTime($iep->start_date);
                    $end = new DateTime($iep->end_date);
                    $interval = $start->diff($end);
                    echo $interval->days . ' days (' . round($interval->days / 30) . ' months)';
                    ?>
                </p>
            </div>

            <?php if ($iep->status === 'approved' && !empty($iep->approved_by_name)): ?>
                <div class="approval-info">
                    <h4>Approval Information</h4>
                    <p>
                        <strong>Approved by:</strong> <?= htmlspecialchars($iep->approved_by_name) ?><br>
                        <strong>Approval Date:</strong> <?= date('F j, Y \a\t g:i A', strtotime($iep->approved_at)) ?>
                    </p>
                    <?php if (!empty($iep->digital_signature)): ?>
                        <p><strong>Digital Signature:</strong> ✓ Verified</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($iep->status === 'rejected' && !empty($iep->rejection_reason)): ?>
                <div class="rejection-info">
                    <h4>Revision Required</h4>
                    <p><strong>Reason:</strong> <?= htmlspecialchars($iep->rejection_reason) ?></p>
                </div>
            <?php endif; ?>

            <div class="metadata">
                <div class="metadata-grid">
                    <div class="metadata-item">
                        <div class="metadata-label">Created:</div>
                        <div class="metadata-value"><?= date('F j, Y \a\t g:i A', strtotime($iep->created_at)) ?></div>
                    </div>
                    <div class="metadata-item">
                        <div class="metadata-label">Last Updated:</div>
                        <div class="metadata-value"><?= date('F j, Y \a\t g:i A', strtotime($iep->updated_at)) ?></div>
                    </div>
                    <div class="metadata-item">
                        <div class="metadata-label">IEP ID:</div>
                        <div class="metadata-value">#<?= str_pad($iep->id, 6, '0', STR_PAD_LEFT) ?></div>
                    </div>
                </div>
            </div>

            <div class="iep-section">
                <h3>Present Level of Academic Achievement and Functional Performance</h3>
                <div class="iep-content"><?= htmlspecialchars($iep->present_level_performance) ?></div>
            </div>

            <div class="iep-section">
                <h3>Annual Goals</h3>
                <div class="iep-content"><?= htmlspecialchars($iep->annual_goals) ?></div>
            </div>

            <div class="iep-section">
                <h3>Short-Term Objectives</h3>
                <div class="iep-content"><?= htmlspecialchars($iep->short_term_objectives) ?></div>
            </div>

            <div class="iep-section">
                <h3>Special Education and Related Services</h3>
                <div class="iep-content"><?= htmlspecialchars($iep->special_education_services) ?></div>
            </div>

            <div class="iep-section">
                <h3>Accommodations and Modifications</h3>
                <div class="iep-content"><?= htmlspecialchars($iep->accommodations) ?></div>
            </div>

            <div class="iep-section">
                <h3>Progress Measurement</h3>
                <div class="iep-content"><?= htmlspecialchars($iep->progress_measurement) ?></div>
            </div>

            <div class="actions-section">
                <?php if ($_SESSION['role'] === 'principal' && $iep->status === 'pending_approval'): ?>
                    <a href="/iep/approve/<?= $iep->id ?>" class="btn btn-approve">
                        Approve IEP
                    </a>
                    <a href="/iep/reject/<?= $iep->id ?>" class="btn btn-reject">
                        Request Revision
                    </a>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'sped_teacher'): ?>
                    <?php if ($iep->status === 'draft'): ?>
                        <a href="/iep/edit/<?= $iep->id ?>" class="btn btn-primary">
                            Continue Editing
                        </a>
                        <a href="/iep/submit/<?= $iep->id ?>" class="btn btn-secondary">
                            Submit for Approval
                        </a>
                    <?php elseif ($iep->status === 'rejected'): ?>
                        <a href="/iep/edit/<?= $iep->id ?>" class="btn btn-primary">
                            Revise IEP
                        </a>
                    <?php elseif ($iep->status === 'approved'): ?>
                        <a href="/iep/activate/<?= $iep->id ?>" class="btn btn-primary">
                            Activate IEP
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <button onclick="window.print()" class="btn print-btn">
                    Print IEP
                </button>

                <a href="/iep/list" class="btn btn-secondary">
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <script>
        // Add print styles and functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Format content for better readability
            const contentElements = document.querySelectorAll('.iep-content');
            contentElements.forEach(element => {
                // Convert line breaks to proper paragraphs for better display
                const content = element.textContent;
                const paragraphs = content.split('\n\n');
                if (paragraphs.length > 1) {
                    element.innerHTML = paragraphs
                        .filter(p => p.trim())
                        .map(p => `<p style="margin-bottom: 10px;">${p.trim()}</p>`)
                        .join('');
                }
            });
        });
    </script>
</body>
</html>