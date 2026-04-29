<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-clipboard-check me-2"></i>
            Initial Assessment Form
        </h1>
        <p class="mb-0">Complete the assessment for <?php echo htmlspecialchars($data['learner']->first_name . ' ' . $data['learner']->last_name); ?></p>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Assessment Form -->
    <form id="assessment-form" method="POST" action="<?php echo URLROOT; ?>/assessment/submit">
        <input type="hidden" name="assessment_id" value="<?php echo $data['assessment']->id; ?>">

        <!-- PART A: Learner's Information Background (Auto-filled) -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    A. Learner's Information Background
                    <span class="badge bg-light text-dark float-end">Auto-filled</span>
                </h5>
            </div>
            <div class="card-body">
                <!-- Personal Information -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Last Name</label>
                        <input type="text" class="form-control bg-light" name="last_name" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['last_name'] ?? $data['learner']->last_name ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">First Name</label>
                        <input type="text" class="form-control bg-light" name="first_name" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['first_name'] ?? $data['learner']->first_name ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Middle Name</label>
                        <input type="text" class="form-control bg-light" name="middle_name" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['middle_name'] ?? $data['learner']->middle_name ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Name Extension</label>
                        <input type="text" class="form-control bg-light" name="suffix" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['suffix'] ?? $data['learner']->suffix ?? ''); ?>" readonly>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date of Birth</label>
                        <input type="text" class="form-control bg-light" name="date_of_birth" value="<?php echo $data['learner']->date_of_birth ? date('F j, Y', strtotime($data['learner']->date_of_birth)) : 'N/A'; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Age</label>
                        <input type="text" class="form-control bg-light" name="age" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['age'] ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Sex</label>
                        <select class="form-select" name="sex">
                            <option value="">Select</option>
                            <option value="Male" <?php echo (isset($data['learner_background']['personal_info']['sex']) && $data['learner_background']['personal_info']['sex'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($data['learner_background']['personal_info']['sex']) && $data['learner_background']['personal_info']['sex'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Religion</label>
                        <input type="text" class="form-control" name="religion" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['religion'] ?? ''); ?>" placeholder="e.g. Roman Catholic">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Home Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Complete home address"><?php echo htmlspecialchars($data['learner_background']['personal_info']['address'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">LRN</label>
                        <input type="text" class="form-control bg-light" name="lrn" value="<?php echo htmlspecialchars($data['learner']->lrn ?? 'Not assigned'); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">School Year</label>
                        <input type="text" class="form-control bg-light" name="school_year" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['school_year'] ?? date('Y') . '-' . (date('Y') + 1)); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">School</label>
                        <input type="text" class="form-control bg-light" name="school" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['school'] ?? 'SignED SPED'); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Name of Adviser</label>
                        <input type="text" class="form-control" name="adviser" value="<?php echo htmlspecialchars($data['learner_background']['personal_info']['adviser'] ?? ''); ?>" placeholder="TBA">
                    </div>
                </div>

                <!-- Sources of Information -->
                <h6 class="fw-bold mb-3 border-bottom pb-2">Sources of Information</h6>
                
                <!-- Father -->
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Name of Father</label>
                        <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['father_name'] ?? ''); ?>" placeholder="Father's full name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Contact Number</label>
                        <input type="text" class="form-control" name="father_contact" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['father_contact'] ?? ''); ?>" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Occupation</label>
                        <input type="text" class="form-control" name="father_occupation" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['father_occupation'] ?? ''); ?>" placeholder="Father's occupation">
                    </div>
                </div>

                <!-- Mother -->
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Name of Mother</label>
                        <input type="text" class="form-control" name="mother_name" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['mother_name'] ?? ''); ?>" placeholder="Mother's full name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Contact Number</label>
                        <input type="text" class="form-control" name="mother_contact" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['mother_contact'] ?? ''); ?>" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Occupation</label>
                        <input type="text" class="form-control" name="mother_occupation" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['mother_occupation'] ?? ''); ?>" placeholder="Mother's occupation">
                    </div>
                </div>

                <!-- Guardian/Caregiver -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Others (Guardian/Caregiver/Relative/s)</label>
                        <input type="text" class="form-control" name="guardian_name" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['guardian_name'] ?? ''); ?>" placeholder="Guardian's full name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Contact Number</label>
                        <input type="text" class="form-control" name="guardian_contact" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['guardian_contact'] ?? ''); ?>" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Occupation</label>
                        <input type="text" class="form-control" name="guardian_occupation" value="<?php echo htmlspecialchars($data['learner_background']['family_background']['guardian_occupation'] ?? ''); ?>" placeholder="Guardian's occupation">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-secondary" onclick="saveDraft('learner_background')">
                        <i class="bi bi-save me-2"></i>Save Draft
                    </button>
                </div>
            </div>
        </div>

        <!-- Education History (REQUIRED) -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-book me-2"></i>
                    Education History
                    <span class="badge bg-light text-danger float-end">REQUIRED</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Previous School Attended <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="previous_school" value="<?php echo htmlspecialchars($data['education_history']['previous_school'] ?? ''); ?>" required>
                        <small class="text-muted">Write "None" if first time enrollee</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Grade Level <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="previous_grade_level" value="<?php echo htmlspecialchars($data['education_history']['previous_grade_level'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">With IEP? <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="with_iep" id="with_iep_yes" value="Yes" 
                                    <?php echo (isset($data['education_history']['with_iep']) && $data['education_history']['with_iep'] === 'Yes') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="with_iep_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="with_iep" id="with_iep_no" value="No"
                                    <?php echo (isset($data['education_history']['with_iep']) && $data['education_history']['with_iep'] === 'No') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="with_iep_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">With support service/s? <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="with_support_services" id="support_yes" value="Yes" 
                                    <?php echo (isset($data['education_history']['with_support_services']) && $data['education_history']['with_support_services'] === 'Yes') ? 'checked' : ''; ?>
                                    onchange="toggleSupportServices()" required>
                                <label class="form-check-label" for="support_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="with_support_services" id="support_no" value="No"
                                    <?php echo (isset($data['education_history']['with_support_services']) && $data['education_history']['with_support_services'] === 'No') ? 'checked' : ''; ?>
                                    onchange="toggleSupportServices()" required>
                                <label class="form-check-label" for="support_no">No</label>
                            </div>
                        </div>

                        <div id="support-services-section" style="display: none;">
                            <p class="fw-bold mb-2">If Yes, specify the support service/s availed:</p>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Screening and Assessment" id="service1" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service1">Screening and Assessment (e.g. MFAT, ECCD Checklist, Psycho-Educational)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Occupational Therapy" id="service2" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service2">Occupational Therapy</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Behavioral Therapy" id="service3" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service3">Behavioral Therapy</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Speech and Language Therapy" id="service4" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service4">Speech and Language Therapy</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Skills Development" id="service5" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service5">Skills Development</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Physical Therapy" id="service6" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service6">Physical Therapy</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Psychosocial Intervention" id="service7" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service7">Psychosocial Intervention</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Daily Living Skills" id="service8" onchange="handleServiceCheck(this)">
                                        <label class="form-check-label" for="service8">Daily Living Skills</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="support_services[]" value="Others" id="service9" onchange="toggleOthersField(); handleServiceCheck(this);">
                                        <label class="form-check-label" for="service9">Others, please specify:</label>
                                    </div>
                                    <input type="text" class="form-control" name="support_services_others" id="support_services_others" placeholder="Specify other services" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-secondary" onclick="saveDraft('education_history')">
                        <i class="bi bi-save me-2"></i>Save Draft
                    </button>
                </div>
            </div>
        </div>

        <!-- PART B: Assessment Information (OPTIONAL) -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    B. Assessment Information
                    <span class="badge bg-light text-info float-end">OPTIONAL</span>
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Fill this section if the learner has availed assessment services before.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Assessment Service/s Availed</th>
                                <th>Members of MDT</th>
                                <th>Date/s of Assessment/s</th>
                                <th>Supporting Documents</th>
                            </tr>
                        </thead>
                        <tbody id="assessment-table-body">
                            <?php 
                            $assessmentInfo = $data['additional_info']['assessment_info'] ?? [];
                            if (!empty($assessmentInfo)) {
                                foreach ($assessmentInfo as $index => $row): 
                            ?>
                            <tr>
                                <td><input type="text" class="form-control" name="assessment_info[<?php echo $index; ?>][service]" value="<?php echo htmlspecialchars($row['service'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control" name="assessment_info[<?php echo $index; ?>][mdt_members]" value="<?php echo htmlspecialchars($row['mdt_members'] ?? ''); ?>"></td>
                                <td><input type="date" class="form-control" name="assessment_info[<?php echo $index; ?>][date]" value="<?php echo htmlspecialchars($row['date'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control" name="assessment_info[<?php echo $index; ?>][documents]" value="<?php echo htmlspecialchars($row['documents'] ?? ''); ?>"></td>
                            </tr>
                            <?php 
                                endforeach;
                            } else {
                                // Empty table - rows will be added when services are checked
                                echo '<tr><td colspan="4" class="text-center text-muted">No services selected yet. Check services above to add rows.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAssessmentRow()">
                    <i class="bi bi-plus-circle me-2"></i>Add Row
                </button>

                <div class="mt-3">
                    <button type="button" class="btn btn-secondary" onclick="saveDraft('additional_info')">
                        <i class="bi bi-save me-2"></i>Save Draft
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Please review all sections before submitting. Once submitted, you cannot edit the assessment.
                </p>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle me-2"></i>Submit Assessment
                </button>
                <a href="<?php echo URLROOT; ?>/assessment" class="btn btn-outline-secondary btn-lg ms-2">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
            </div>
        </div>

    </form>

</main>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Assessment Form JavaScript -->
<script>
// Toggle support services section
function toggleSupportServices() {
    const supportYes = document.getElementById('support_yes');
    const servicesSection = document.getElementById('support-services-section');
    
    if (supportYes.checked) {
        servicesSection.style.display = 'block';
    } else {
        servicesSection.style.display = 'none';
        // Uncheck all checkboxes
        document.querySelectorAll('input[name="support_services[]"]').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('support_services_others').style.display = 'none';
        document.getElementById('support_services_others').value = '';
        // Clear Part B table
        clearAssessmentTable();
    }
}

// Toggle others field
function toggleOthersField() {
    const othersCheckbox = document.getElementById('service9');
    const othersField = document.getElementById('support_services_others');
    
    if (othersCheckbox.checked) {
        othersField.style.display = 'block';
    } else {
        othersField.style.display = 'none';
        othersField.value = '';
    }
}

// Auto-add rows to Part B when services are checked
function handleServiceCheck(checkbox) {
    const serviceName = checkbox.value;
    const tbody = document.getElementById('assessment-table-body');
    
    // Remove placeholder row if it exists
    const placeholderRow = tbody.querySelector('td[colspan="4"]');
    if (placeholderRow) {
        placeholderRow.parentElement.remove();
    }
    
    if (checkbox.checked) {
        // Add row with service name pre-filled
        const rowCount = tbody.querySelectorAll('tr').length;
        const newRow = `
            <tr data-service="${serviceName}">
                <td><input type="text" class="form-control bg-light" name="assessment_info[${rowCount}][service]" value="${serviceName}" readonly></td>
                <td><input type="text" class="form-control" name="assessment_info[${rowCount}][mdt_members]" placeholder="e.g. John Doe - SPED Teacher"></td>
                <td><input type="date" class="form-control" name="assessment_info[${rowCount}][date]"></td>
                <td><input type="text" class="form-control" name="assessment_info[${rowCount}][documents]" placeholder="e.g. ECCD Checklist"></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', newRow);
    } else {
        // Remove row with this service
        const rowToRemove = tbody.querySelector(`tr[data-service="${serviceName}"]`);
        if (rowToRemove) {
            rowToRemove.remove();
            // Re-index remaining rows
            reindexAssessmentTable();
        }
        
        // If no rows left, add placeholder
        if (tbody.querySelectorAll('tr').length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No services selected yet. Check services above to add rows.</td></tr>';
        }
    }
}

// Clear assessment table
function clearAssessmentTable() {
    const tbody = document.getElementById('assessment-table-body');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No services selected yet. Check services above to add rows.</td></tr>';
}

// Re-index assessment table rows
function reindexAssessmentTable() {
    const tbody = document.getElementById('assessment-table-body');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach((row, index) => {
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', newName);
            }
        });
    });
}

// Add assessment row
function addAssessmentRow() {
    const tbody = document.getElementById('assessment-table-body');
    const rowCount = tbody.querySelectorAll('tr').length;
    
    const newRow = `
        <tr>
            <td><input type="text" class="form-control" name="assessment_info[${rowCount}][service]" placeholder="Enter service name"></td>
            <td><input type="text" class="form-control" name="assessment_info[${rowCount}][mdt_members]" placeholder="e.g. John Doe - SPED Teacher"></td>
            <td><input type="date" class="form-control" name="assessment_info[${rowCount}][date]"></td>
            <td><input type="text" class="form-control" name="assessment_info[${rowCount}][documents]" placeholder="e.g. ECCD Checklist"></td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', newRow);
}

// Save draft function
function saveDraft(section) {
    const assessmentId = document.querySelector('input[name="assessment_id"]').value;
    const form = document.getElementById('assessment-form');
    const formData = new FormData(form);
    
    let data = {};
    
    if (section === 'learner_background') {
        data = {
            last_name: formData.get('last_name'),
            first_name: formData.get('first_name'),
            middle_name: formData.get('middle_name'),
            suffix: formData.get('suffix'),
            date_of_birth: formData.get('date_of_birth'),
            age: formData.get('age'),
            sex: formData.get('sex'),
            religion: formData.get('religion'),
            address: formData.get('address'),
            lrn: formData.get('lrn'),
            school_year: formData.get('school_year'),
            school: formData.get('school'),
            adviser: formData.get('adviser'),
            father_name: formData.get('father_name'),
            father_contact: formData.get('father_contact'),
            father_occupation: formData.get('father_occupation'),
            mother_name: formData.get('mother_name'),
            mother_contact: formData.get('mother_contact'),
            mother_occupation: formData.get('mother_occupation'),
            guardian_name: formData.get('guardian_name'),
            guardian_contact: formData.get('guardian_contact'),
            guardian_occupation: formData.get('guardian_occupation')
        };
    } else if (section === 'education_history') {
        const supportServices = [];
        document.querySelectorAll('input[name="support_services[]"]:checked').forEach(cb => {
            supportServices.push(cb.value);
        });
        
        data = {
            previous_school: formData.get('previous_school'),
            previous_grade_level: formData.get('previous_grade_level'),
            with_iep: formData.get('with_iep'),
            with_support_services: formData.get('with_support_services'),
            support_services: supportServices,
            support_services_others: formData.get('support_services_others')
        };
    } else if (section === 'additional_info') {
        const assessmentInfo = [];
        const rows = document.querySelectorAll('#assessment-table-body tr');
        rows.forEach((row, index) => {
            assessmentInfo.push({
                service: formData.get(`assessment_info[${index}][service]`),
                mdt_members: formData.get(`assessment_info[${index}][mdt_members]`),
                date: formData.get(`assessment_info[${index}][date]`),
                documents: formData.get(`assessment_info[${index}][documents]`)
            });
        });
        data = { assessment_info: assessmentInfo };
    }
    
    fetch('<?php echo URLROOT; ?>/assessment/saveDraft', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            assessment_id: assessmentId,
            section: section,
            data: JSON.stringify(data)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Draft saved successfully!', 'success');
        } else {
            showToast('Failed to save draft: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while saving draft', 'error');
    });
}

// Show toast notification
function showToast(message, type) {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHtml);
    const toastElement = document.querySelector('.toast:last-child');
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Confirm before submit
document.getElementById('assessment-form').addEventListener('submit', function(e) {
    if (!confirm('Sigurado ka ba? Dili na ni ma-edit pag na-submit.')) {
        e.preventDefault();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if support services should be shown
    const supportYes = document.getElementById('support_yes');
    if (supportYes && supportYes.checked) {
        toggleSupportServices();
    }
    
    // Check if others field should be shown
    const othersCheckbox = document.getElementById('service9');
    if (othersCheckbox && othersCheckbox.checked) {
        toggleOthersField();
    }
});
</script>

</body>
</html>
