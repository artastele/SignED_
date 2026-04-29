<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-file-earmark-medical me-2"></i>
            <?php echo isset($data['existing_iep']) ? 'Edit IEP Draft' : 'Create IEP Draft'; ?>
        </h1>
        <p class="mb-0">Individualized Education Program for <?php echo htmlspecialchars($data['learner']->first_name . ' ' . $data['learner']->last_name); ?></p>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($data['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- IEP Form -->
    <form id="iep-form" method="POST" action="<?php echo URLROOT; ?>/iep/create?learner_id=<?php echo $data['learner']->id; ?>">
        <input type="hidden" name="assessment_id" value="<?php echo $data['assessment']->id ?? ''; ?>">

        <!-- Student Information (Read-only) -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Student Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['learner']->first_name . ' ' . ($data['learner']->middle_name ?? '') . ' ' . $data['learner']->last_name . ' ' . ($data['learner']->suffix ?? '')); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">LRN</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['learner']->lrn ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Grade Level</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['learner']->grade_level ?? 'N/A'); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Parse existing draft data if available
        $draftData = [];
        if (isset($data['existing_iep']) && $data['existing_iep']->draft_data) {
            $draftData = json_decode($data['existing_iep']->draft_data, true);
        }
        $existingGoals = $draftData['goals'] ?? [];
        $existingServices = $draftData['services'] ?? [];
        $existingAccommodations = $draftData['accommodations'] ?? [];
        $existingRemarks = $draftData['remarks'] ?? '';
        ?>

        <!-- IEP Goals (Based on IEP P2.pdf) -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-bullseye me-2"></i>
                    Developmental Domains & Goals
                </h5>
                <button type="button" class="btn btn-light btn-sm" onclick="addGoalRow()">
                    <i class="bi bi-plus-circle me-1"></i> Add Goal
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="goals-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Domain</th>
                                <th style="width: 15%;">Skill</th>
                                <th style="width: 20%;">Description</th>
                                <th style="width: 8%;">Mastered?</th>
                                <th style="width: 12%;">Quarter 1</th>
                                <th style="width: 12%;">Quarter 2</th>
                                <th style="width: 13%;">Performance Level</th>
                                <th style="width: 5%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="goals-tbody">
                            <?php if (!empty($existingGoals)): ?>
                                <?php foreach ($existingGoals as $index => $goal): ?>
                                    <tr>
                                        <td>
                                            <select class="form-select form-select-sm" name="goals[<?php echo $index; ?>][domain]">
                                                <option value="">Select Domain</option>
                                                <option value="Perceptuo-Cognitive" <?php echo ($goal['domain'] ?? '') === 'Perceptuo-Cognitive' ? 'selected' : ''; ?>>Perceptuo-Cognitive</option>
                                                <option value="Psychosocial" <?php echo ($goal['domain'] ?? '') === 'Psychosocial' ? 'selected' : ''; ?>>Psychosocial</option>
                                                <option value="Psychomotor" <?php echo ($goal['domain'] ?? '') === 'Psychomotor' ? 'selected' : ''; ?>>Psychomotor</option>
                                                <option value="Socio-Emotional" <?php echo ($goal['domain'] ?? '') === 'Socio-Emotional' ? 'selected' : ''; ?>>Socio-Emotional</option>
                                                <option value="Daily Living Skills" <?php echo ($goal['domain'] ?? '') === 'Daily Living Skills' ? 'selected' : ''; ?>>Daily Living Skills</option>
                                                <option value="Communication & Language" <?php echo ($goal['domain'] ?? '') === 'Communication & Language' ? 'selected' : ''; ?>>Communication & Language</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="goals[<?php echo $index; ?>][skill]" value="<?php echo htmlspecialchars($goal['skill'] ?? ''); ?>" placeholder="Skill">
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" name="goals[<?php echo $index; ?>][description]" rows="2" placeholder="Description"><?php echo htmlspecialchars($goal['description'] ?? ''); ?></textarea>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="goals[<?php echo $index; ?>][mastered]" value="yes" <?php echo ($goal['mastered_yes'] ?? 0) == 1 ? 'checked' : ''; ?>>
                                                <label class="form-check-label small">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="goals[<?php echo $index; ?>][mastered]" value="no" <?php echo ($goal['mastered_no'] ?? 0) == 1 ? 'checked' : ''; ?>>
                                                <label class="form-check-label small">No</label>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" name="goals[<?php echo $index; ?>][quarter1]" rows="2" placeholder="Q1 Recommendation"><?php echo htmlspecialchars($goal['quarter1_recommendation'] ?? ''); ?></textarea>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" name="goals[<?php echo $index; ?>][quarter2]" rows="2" placeholder="Q2 Recommendation"><?php echo htmlspecialchars($goal['quarter2_recommendation'] ?? ''); ?></textarea>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="goals[<?php echo $index; ?>][performance_level]">
                                                <option value="">Select</option>
                                                <option value="Beginning (74% below)" <?php echo ($goal['performance_level'] ?? '') === 'Beginning (74% below)' ? 'selected' : ''; ?>>Beginning (74% below)</option>
                                                <option value="Developing (75-79%)" <?php echo ($goal['performance_level'] ?? '') === 'Developing (75-79%)' ? 'selected' : ''; ?>>Developing (75-79%)</option>
                                                <option value="Approaching Proficiency (80-84%)" <?php echo ($goal['performance_level'] ?? '') === 'Approaching Proficiency (80-84%)' ? 'selected' : ''; ?>>Approaching Proficiency (80-84%)</option>
                                                <option value="Proficient (85-89%)" <?php echo ($goal['performance_level'] ?? '') === 'Proficient (85-89%)' ? 'selected' : ''; ?>>Proficient (85-89%)</option>
                                                <option value="Advanced (90%+)" <?php echo ($goal['performance_level'] ?? '') === 'Advanced (90%+)' ? 'selected' : ''; ?>>Advanced (90%+)</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mb-0 mt-2">
                    <i class="bi bi-info-circle me-1"></i>
                    At least 1 goal is required to send IEP
                </p>
            </div>
        </div>

        <!-- Special Education Services -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-heart-pulse me-2"></i>
                    Special Education Services
                </h5>
                <button type="button" class="btn btn-light btn-sm" onclick="addServiceRow()">
                    <i class="bi bi-plus-circle me-1"></i> Add Service
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="services-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Service Type</th>
                                <th style="width: 20%;">Provider</th>
                                <th style="width: 20%;">Frequency</th>
                                <th style="width: 15%;">Duration</th>
                                <th style="width: 15%;">Location</th>
                                <th style="width: 5%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="services-tbody">
                            <?php if (!empty($existingServices)): ?>
                                <?php foreach ($existingServices as $index => $service): ?>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="services[<?php echo $index; ?>][type]" value="<?php echo htmlspecialchars($service['service_type'] ?? ''); ?>" placeholder="e.g. Speech Therapy">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="services[<?php echo $index; ?>][provider]" value="<?php echo htmlspecialchars($service['provider'] ?? ''); ?>" placeholder="Provider name">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="services[<?php echo $index; ?>][frequency]" value="<?php echo htmlspecialchars($service['frequency'] ?? ''); ?>" placeholder="e.g. 2x per week">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="services[<?php echo $index; ?>][duration]" value="<?php echo htmlspecialchars($service['duration'] ?? ''); ?>" placeholder="e.g. 30 mins">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="services[<?php echo $index; ?>][location]" value="<?php echo htmlspecialchars($service['location'] ?? ''); ?>" placeholder="Location">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mb-0 mt-2">
                    <i class="bi bi-info-circle me-1"></i>
                    At least 1 service is required to send IEP
                </p>
            </div>
        </div>

        <!-- Accommodations -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Accommodations
                </h5>
                <button type="button" class="btn btn-dark btn-sm" onclick="addAccommodationRow()">
                    <i class="bi bi-plus-circle me-1"></i> Add Accommodation
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="accommodations-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">Accommodation Type</th>
                                <th style="width: 65%;">Description</th>
                                <th style="width: 5%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="accommodations-tbody">
                            <?php if (!empty($existingAccommodations)): ?>
                                <?php foreach ($existingAccommodations as $index => $accommodation): ?>
                                    <tr>
                                        <td>
                                            <select class="form-select form-select-sm" name="accommodations[<?php echo $index; ?>][type]">
                                                <option value="">Select Type</option>
                                                <option value="Instructional" <?php echo ($accommodation['accommodation_type'] ?? '') === 'Instructional' ? 'selected' : ''; ?>>Instructional</option>
                                                <option value="Environmental" <?php echo ($accommodation['accommodation_type'] ?? '') === 'Environmental' ? 'selected' : ''; ?>>Environmental</option>
                                                <option value="Assessment" <?php echo ($accommodation['accommodation_type'] ?? '') === 'Assessment' ? 'selected' : ''; ?>>Assessment</option>
                                                <option value="Behavioral" <?php echo ($accommodation['accommodation_type'] ?? '') === 'Behavioral' ? 'selected' : ''; ?>>Behavioral</option>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" name="accommodations[<?php echo $index; ?>][description]" rows="2" placeholder="Describe the accommodation"><?php echo htmlspecialchars($accommodation['description'] ?? ''); ?></textarea>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mb-0 mt-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Accommodations are optional
                </p>
            </div>
        </div>

        <!-- Remarks -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-chat-left-text me-2"></i>
                    Remarks
                </h5>
            </div>
            <div class="card-body">
                <textarea class="form-control" name="remarks" rows="4" placeholder="Additional notes or remarks (optional)"><?php echo htmlspecialchars($existingRemarks); ?></textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?php echo URLROOT; ?>/iep/list" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                    <div>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save me-1"></i> Save Draft
                        </button>
                        <button type="button" class="btn btn-success" onclick="sendIEP()">
                            <i class="bi bi-send me-1"></i> Send IEP
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>

</main>

<!-- Send IEP Form (Hidden) -->
<form id="send-iep-form" method="POST" action="<?php echo URLROOT; ?>/iep/send" style="display: none;">
    <input type="hidden" name="iep_id" id="send-iep-id" value="<?php echo $data['existing_iep']->id ?? ''; ?>">
</form>

<script>
let goalIndex = <?php echo !empty($existingGoals) ? count($existingGoals) : 0; ?>;
let serviceIndex = <?php echo !empty($existingServices) ? count($existingServices) : 0; ?>;
let accommodationIndex = <?php echo !empty($existingAccommodations) ? count($existingAccommodations) : 0; ?>;

function addGoalRow() {
    const tbody = document.getElementById('goals-tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="goals[${goalIndex}][domain]">
                <option value="">Select Domain</option>
                <option value="Perceptuo-Cognitive">Perceptuo-Cognitive</option>
                <option value="Psychosocial">Psychosocial</option>
                <option value="Psychomotor">Psychomotor</option>
                <option value="Socio-Emotional">Socio-Emotional</option>
                <option value="Daily Living Skills">Daily Living Skills</option>
                <option value="Communication & Language">Communication & Language</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="goals[${goalIndex}][skill]" placeholder="Skill">
        </td>
        <td>
            <textarea class="form-control form-control-sm" name="goals[${goalIndex}][description]" rows="2" placeholder="Description"></textarea>
        </td>
        <td>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="goals[${goalIndex}][mastered]" value="yes">
                <label class="form-check-label small">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="goals[${goalIndex}][mastered]" value="no">
                <label class="form-check-label small">No</label>
            </div>
        </td>
        <td>
            <textarea class="form-control form-control-sm" name="goals[${goalIndex}][quarter1]" rows="2" placeholder="Q1 Recommendation"></textarea>
        </td>
        <td>
            <textarea class="form-control form-control-sm" name="goals[${goalIndex}][quarter2]" rows="2" placeholder="Q2 Recommendation"></textarea>
        </td>
        <td>
            <select class="form-select form-select-sm" name="goals[${goalIndex}][performance_level]">
                <option value="">Select</option>
                <option value="Beginning (74% below)">Beginning (74% below)</option>
                <option value="Developing (75-79%)">Developing (75-79%)</option>
                <option value="Approaching Proficiency (80-84%)">Approaching Proficiency (80-84%)</option>
                <option value="Proficient (85-89%)">Proficient (85-89%)</option>
                <option value="Advanced (90%+)">Advanced (90%+)</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    goalIndex++;
}

function addServiceRow() {
    const tbody = document.getElementById('services-tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm" name="services[${serviceIndex}][type]" placeholder="e.g. Speech Therapy">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="services[${serviceIndex}][provider]" placeholder="Provider name">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="services[${serviceIndex}][frequency]" placeholder="e.g. 2x per week">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="services[${serviceIndex}][duration]" placeholder="e.g. 30 mins">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="services[${serviceIndex}][location]" placeholder="Location">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    serviceIndex++;
}

function addAccommodationRow() {
    const tbody = document.getElementById('accommodations-tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="accommodations[${accommodationIndex}][type]">
                <option value="">Select Type</option>
                <option value="Instructional">Instructional</option>
                <option value="Environmental">Environmental</option>
                <option value="Assessment">Assessment</option>
                <option value="Behavioral">Behavioral</option>
            </select>
        </td>
        <td>
            <textarea class="form-control form-control-sm" name="accommodations[${accommodationIndex}][description]" rows="2" placeholder="Describe the accommodation"></textarea>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    accommodationIndex++;
}

function removeRow(button) {
    button.closest('tr').remove();
}

function sendIEP() {
    <?php if (isset($data['existing_iep'])): ?>
        if (confirm('Are you sure you want to send this IEP? This will proceed to meeting scheduling.\n\nMinimum requirements:\n- At least 1 goal\n- At least 1 service')) {
            document.getElementById('send-iep-form').submit();
        }
    <?php else: ?>
        alert('Please save the IEP draft first before sending.');
    <?php endif; ?>
}

// Auto-save draft every 5 minutes
setInterval(function() {
    const form = document.getElementById('iep-form');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            console.log('Draft auto-saved');
        }
    });
}, 300000); // 5 minutes
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
