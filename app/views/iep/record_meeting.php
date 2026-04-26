<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Meeting Completion - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .meeting-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .meeting-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1E40AF;
        }
        
        .meeting-info {
            background: #F3F4F6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            display: flex;
        }
        
        .info-label {
            font-weight: bold;
            color: #1E40AF;
            width: 100px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #1E40AF;
        }
        
        .form-group textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }
        
        .signatures-section {
            background: #F9FAFB;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #E5E7EB;
        }
        
        .signature-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }
        
        .signature-item:last-child {
            margin-bottom: 0;
        }
        
        .signature-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .participant-name {
            font-weight: bold;
            color: #1F2937;
        }
        
        .participant-role {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            background: #F3F4F6;
            padding: 2px 8px;
            border-radius: 12px;
        }
        
        .signature-pad {
            border: 2px dashed #D1D5DB;
            border-radius: 4px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color 0.2s;
            position: relative;
            background: #FAFAFA;
        }
        
        .signature-pad:hover {
            border-color: #1E40AF;
            background: #F8FAFF;
        }
        
        .signature-pad.signed {
            border-color: #10B981;
            background: #ECFDF5;
        }
        
        .signature-placeholder {
            color: #6B7280;
            font-style: italic;
        }
        
        .signature-canvas {
            width: 100%;
            height: 96px;
            border-radius: 2px;
        }
        
        .signature-controls {
            position: absolute;
            top: 5px;
            right: 5px;
            display: none;
        }
        
        .signature-pad.signed .signature-controls {
            display: block;
        }
        
        .clear-signature {
            background: #EF4444;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            cursor: pointer;
        }
        
        .clear-signature:hover {
            background: #DC2626;
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
        
        .btn-primary:disabled {
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
        
        .required {
            color: #B91C1C;
        }
        
        .form-actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }
        
        .completion-status {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #10B981;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Record Meeting Completion</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/meetings">Meetings</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="meeting-container">
            <?php if (isset($error)): ?>
                <div class="error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="meeting-header">
                <h2>IEP Meeting Completion</h2>
                <p>Learner: <strong><?= htmlspecialchars($meeting->first_name . ' ' . $meeting->last_name) ?></strong></p>
            </div>

            <div class="completion-status">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <span><strong>Meeting Status:</strong> All participants have confirmed attendance</span>
                </div>
            </div>

            <div class="meeting-info">
                <h3 style="margin-top: 0; color: #1E40AF;">Meeting Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Date & Time:</div>
                        <div class="info-value">
                            <?= date('l, F j, Y \a\t g:i A', strtotime($meeting->meeting_date)) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Location:</div>
                        <div class="info-value"><?= htmlspecialchars($meeting->location) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Scheduled by:</div>
                        <div class="info-value"><?= htmlspecialchars($meeting->scheduled_by_name) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Participants:</div>
                        <div class="info-value"><?= count($participants) ?> confirmed</div>
                    </div>
                </div>
            </div>

            <form method="POST" id="meetingForm">
                <div class="form-group">
                    <label for="meeting_notes">Meeting Notes <span class="required">*</span></label>
                    <textarea name="meeting_notes" 
                              id="meeting_notes" 
                              placeholder="Record key discussion points, decisions made, and action items from the meeting..."
                              required><?= htmlspecialchars($_POST['meeting_notes'] ?? '') ?></textarea>
                    <small style="color: #6B7280; font-size: 12px;">
                        Include discussion of learner's needs, goals established, services agreed upon, and any concerns raised.
                    </small>
                </div>

                <div class="signatures-section">
                    <h3 style="margin-top: 0; color: #1E40AF;">Participant Signatures</h3>
                    <p style="color: #6B7280; margin-bottom: 20px;">
                        All participants must provide their digital signature to confirm attendance and agreement with meeting outcomes.
                    </p>

                    <?php foreach ($participants as $participant): ?>
                        <div class="signature-item">
                            <div class="signature-header">
                                <div>
                                    <div class="participant-name"><?= htmlspecialchars($participant->fullname) ?></div>
                                    <div class="participant-role"><?= htmlspecialchars($participant->role) ?></div>
                                </div>
                            </div>
                            
                            <div class="signature-pad" data-participant="<?= $participant->user_id ?>">
                                <div class="signature-placeholder">Click to sign</div>
                                <canvas class="signature-canvas" style="display: none;"></canvas>
                                <div class="signature-controls">
                                    <button type="button" class="clear-signature">Clear</button>
                                </div>
                                <input type="hidden" 
                                       name="signatures[<?= $participant->user_id ?>]" 
                                       class="signature-data">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        Record Meeting Completion
                    </button>
                    <a href="/iep/meetings" class="btn btn-secondary" style="margin-left: 15px;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Simple signature pad implementation
        class SignaturePad {
            constructor(canvas, onSignature) {
                this.canvas = canvas;
                this.ctx = canvas.getContext('2d');
                this.onSignature = onSignature;
                this.isDrawing = false;
                this.hasSignature = false;
                
                this.setupCanvas();
                this.bindEvents();
            }
            
            setupCanvas() {
                const rect = this.canvas.getBoundingClientRect();
                this.canvas.width = rect.width;
                this.canvas.height = rect.height;
                
                this.ctx.strokeStyle = '#1F2937';
                this.ctx.lineWidth = 2;
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
            }
            
            bindEvents() {
                this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                this.canvas.addEventListener('mouseout', () => this.stopDrawing());
                
                // Touch events for mobile
                this.canvas.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const mouseEvent = new MouseEvent('mousedown', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    });
                    this.canvas.dispatchEvent(mouseEvent);
                });
                
                this.canvas.addEventListener('touchmove', (e) => {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const mouseEvent = new MouseEvent('mousemove', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    });
                    this.canvas.dispatchEvent(mouseEvent);
                });
                
                this.canvas.addEventListener('touchend', (e) => {
                    e.preventDefault();
                    const mouseEvent = new MouseEvent('mouseup', {});
                    this.canvas.dispatchEvent(mouseEvent);
                });
            }
            
            startDrawing(e) {
                this.isDrawing = true;
                const rect = this.canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                this.ctx.beginPath();
                this.ctx.moveTo(x, y);
            }
            
            draw(e) {
                if (!this.isDrawing) return;
                
                const rect = this.canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                this.ctx.lineTo(x, y);
                this.ctx.stroke();
                
                this.hasSignature = true;
                this.onSignature(this.canvas.toDataURL());
            }
            
            stopDrawing() {
                this.isDrawing = false;
            }
            
            clear() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.hasSignature = false;
                this.onSignature('');
            }
        }

        // Initialize signature pads
        document.addEventListener('DOMContentLoaded', function() {
            const signaturePads = {};
            
            document.querySelectorAll('.signature-pad').forEach(pad => {
                const participantId = pad.dataset.participant;
                const canvas = pad.querySelector('.signature-canvas');
                const placeholder = pad.querySelector('.signature-placeholder');
                const hiddenInput = pad.querySelector('.signature-data');
                const clearBtn = pad.querySelector('.clear-signature');
                
                pad.addEventListener('click', function() {
                    if (canvas.style.display === 'none') {
                        canvas.style.display = 'block';
                        placeholder.style.display = 'none';
                        
                        signaturePads[participantId] = new SignaturePad(canvas, function(dataUrl) {
                            hiddenInput.value = dataUrl;
                            if (dataUrl) {
                                pad.classList.add('signed');
                            } else {
                                pad.classList.remove('signed');
                            }
                            checkFormCompletion();
                        });
                    }
                });
                
                clearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    signaturePads[participantId].clear();
                    pad.classList.remove('signed');
                    checkFormCompletion();
                });
            });
            
            function checkFormCompletion() {
                const notes = document.getElementById('meeting_notes').value.trim();
                const signatures = document.querySelectorAll('.signature-data');
                let allSigned = true;
                
                signatures.forEach(input => {
                    if (!input.value) {
                        allSigned = false;
                    }
                });
                
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = !(notes && allSigned);
            }
            
            // Check form completion on notes change
            document.getElementById('meeting_notes').addEventListener('input', checkFormCompletion);
            
            // Form submission validation
            document.getElementById('meetingForm').addEventListener('submit', function(e) {
                const notes = document.getElementById('meeting_notes').value.trim();
                if (!notes) {
                    e.preventDefault();
                    alert('Please enter meeting notes before submitting.');
                    return;
                }
                
                const signatures = document.querySelectorAll('.signature-data');
                let missingSignatures = [];
                
                signatures.forEach(input => {
                    if (!input.value) {
                        const participantName = input.closest('.signature-item').querySelector('.participant-name').textContent;
                        missingSignatures.push(participantName);
                    }
                });
                
                if (missingSignatures.length > 0) {
                    e.preventDefault();
                    alert('Missing signatures from: ' + missingSignatures.join(', '));
                    return;
                }
                
                if (!confirm('Are you sure you want to record this meeting as complete? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>