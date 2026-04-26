<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule IEP Meeting - SignED SPED</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #1E40AF;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .participants-section {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            background: #f9f9f9;
        }
        
        .participant-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        
        .participant-checkbox input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        
        .participant-info {
            flex: 1;
        }
        
        .participant-role {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .btn {
            background: #1E40AF;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #1D4ED8;
        }
        
        .btn-secondary {
            background: #6B7280;
        }
        
        .btn-secondary:hover {
            background: #4B5563;
        }
        
        .error {
            background: #FEE2E2;
            color: #B91C1C;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .required {
            color: #B91C1C;
        }
        
        .datetime-group {
            display: flex;
            gap: 15px;
        }
        
        .datetime-group .form-group {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Schedule IEP Meeting</h1>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/iep/meetings">Meetings</a>
                <a href="/logout">Logout</a>
            </nav>
        </div>

        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/iep/schedule-meeting">
                <div class="form-group">
                    <label for="learner_id">Select Learner <span class="required">*</span></label>
                    <select name="learner_id" id="learner_id" required>
                        <option value="">-- Select Learner --</option>
                        <?php foreach ($learners as $learner): ?>
                            <option value="<?= $learner->id ?>" 
                                    <?= (isset($_POST['learner_id']) && $_POST['learner_id'] == $learner->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($learner->first_name . ' ' . $learner->last_name) ?>
                                (Grade <?= htmlspecialchars($learner->grade_level) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="datetime-group">
                    <div class="form-group">
                        <label for="meeting_date">Meeting Date <span class="required">*</span></label>
                        <input type="date" 
                               name="meeting_date" 
                               id="meeting_date" 
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               value="<?= htmlspecialchars($_POST['meeting_date'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="meeting_time">Meeting Time <span class="required">*</span></label>
                        <input type="time" 
                               name="meeting_time" 
                               id="meeting_time"
                               value="<?= htmlspecialchars($_POST['meeting_time'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Meeting Location <span class="required">*</span></label>
                    <input type="text" 
                           name="location" 
                           id="location" 
                           placeholder="e.g., Conference Room A, Principal's Office"
                           value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Meeting Participants <span class="required">*</span></label>
                    <div class="participants-section">
                        <p style="margin-bottom: 15px; color: #666; font-size: 14px;">
                            Select all participants for this IEP meeting. Guidance and Principal are required.
                        </p>
                        
                        <?php 
                        $selectedParticipants = $_POST['participants'] ?? [];
                        $roleGroups = [];
                        foreach ($users as $user) {
                            $roleGroups[$user->role][] = $user;
                        }
                        ?>
                        
                        <?php foreach ($roleGroups as $role => $roleUsers): ?>
                            <h4 style="margin: 15px 0 10px 0; color: #1E40AF; text-transform: capitalize;">
                                <?= htmlspecialchars($role) ?> 
                                <?php if (in_array($role, ['guidance', 'principal'])): ?>
                                    <span class="required">*</span>
                                <?php endif; ?>
                            </h4>
                            
                            <?php foreach ($roleUsers as $user): ?>
                                <div class="participant-checkbox">
                                    <input type="checkbox" 
                                           name="participants[]" 
                                           value="<?= $user->id ?>"
                                           id="participant_<?= $user->id ?>"
                                           <?= in_array($user->id, $selectedParticipants) ? 'checked' : '' ?>
                                           <?= in_array($role, ['guidance', 'principal']) ? 'required' : '' ?>>
                                    <label for="participant_<?= $user->id ?>" class="participant-info">
                                        <div><?= htmlspecialchars($user->fullname) ?></div>
                                        <div class="participant-role"><?= htmlspecialchars($user->email) ?></div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 30px; text-align: center;">
                    <button type="submit" class="btn">Schedule Meeting</button>
                    <a href="/iep/meetings" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Ensure required roles are selected
        document.querySelector('form').addEventListener('submit', function(e) {
            const guidanceChecked = document.querySelector('input[name="participants[]"][value*="guidance"]:checked');
            const principalChecked = document.querySelector('input[name="participants[]"][value*="principal"]:checked');
            
            // Get users by role to check if guidance/principal are selected
            const selectedIds = Array.from(document.querySelectorAll('input[name="participants[]"]:checked'))
                                     .map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one participant for the meeting.');
                return;
            }
            
            // Additional validation can be added here if needed
        });

        // Set minimum date to tomorrow
        document.getElementById('meeting_date').min = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    </script>
</body>
</html>