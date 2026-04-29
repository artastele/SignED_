<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="bi bi-clipboard-check me-2"></i>
                    Assessment - <?php echo htmlspecialchars($data['assessment']->first_name . ' ' . $data['assessment']->last_name); ?>
                </h1>
                <p class="mb-0">
                    LRN: <?php echo htmlspecialchars($data['assessment']->lrn ?? 'N/A'); ?> | 
                    Grade: <?php echo htmlspecialchars($data['assessment']->grade_level); ?>
                </p>
            </div>
            <div>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
                <a href="<?php echo URLROOT; ?>/assessment/review" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="alert alert-success mb-4">
        <i class="bi bi-check-circle me-2"></i>
        <strong>Status:</strong> Submitted on <?php echo date('F j, Y g:i A', strtotime($data['assessment']->parent_submitted_at)); ?>
    </div>

    <!-- PART A: Learner's Information Background -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-person-badge me-2"></i>
                A. Learner's Information Background
            </h5>
        </div>
        <div class="card-body">
            <!-- Personal Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Last Name</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['last_name'] ?? $data['assessment']->last_name ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">First Name</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['first_name'] ?? $data['assessment']->first_name ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Middle Name</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['middle_name'] ?? $data['assessment']->middle_name ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Name Extension</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['suffix'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Date of Birth</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['date_of_birth'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Age</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['age'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Sex</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['sex'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Religion</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['religion'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Home Address</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['address'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">LRN</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['lrn'] ?? $data['assessment']->lrn ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">School Year</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['school_year'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">School</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['school'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Name of Adviser</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['personal_info']['adviser'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <!-- Sources of Information -->
            <h6 class="fw-bold mb-3 border-bottom pb-2">Sources of Information</h6>
            
            <!-- Father -->
            <div class="row g-3 mb-2">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Name of Father</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['father_name'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Contact Number</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['father_contact'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">Occupation</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['father_occupation'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <!-- Mother -->
            <div class="row g-3 mb-2">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Name of Mother</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['mother_name'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Contact Number</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['mother_contact'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">Occupation</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['mother_occupation'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <!-- Guardian/Caregiver -->
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Others (Guardian/Caregiver/Relative/s)</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['guardian_name'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Contact Number</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['guardian_contact'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">Occupation</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['learner_background']['family_background']['guardian_occupation'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Education History -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="bi bi-book me-2"></i>
                Education History
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Previous School Attended</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['education_history']['previous_school'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Grade Level</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['education_history']['previous_grade_level'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">With IEP?</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['education_history']['with_iep'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">With support service/s?</label>
                    <p class="form-control-plaintext"><?php echo htmlspecialchars($data['education_history']['with_support_services'] ?? 'N/A'); ?></p>
                </div>
                <?php if (isset($data['education_history']['support_services']) && !empty($data['education_history']['support_services'])): ?>
                <div class="col-12">
                    <label class="form-label fw-bold">Support Services Availed:</label>
                    <ul class="list-unstyled ms-3">
                        <?php foreach ($data['education_history']['support_services'] as $service): ?>
                            <li><i class="bi bi-check-circle text-success me-2"></i><?php echo htmlspecialchars($service); ?></li>
                        <?php endforeach; ?>
                        <?php if (!empty($data['education_history']['support_services_others'])): ?>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Others: <?php echo htmlspecialchars($data['education_history']['support_services_others']); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- PART B: Assessment Information -->
    <?php if (isset($data['additional_info']['assessment_info']) && !empty($data['additional_info']['assessment_info'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="bi bi-table me-2"></i>
                B. Assessment Information
            </h5>
        </div>
        <div class="card-body">
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
                    <tbody>
                        <?php foreach ($data['additional_info']['assessment_info'] as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['service'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['mdt_members'] ?? 'N/A'); ?></td>
                            <td><?php echo $row['date'] ? date('F j, Y', strtotime($row['date'])) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($row['documents'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Print Styles -->
<style media="print">
    .sidebar, .navbar, .btn, .page-header p {
        display: none !important;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .card {
        border: 1px solid #ddd;
        box-shadow: none;
        page-break-inside: avoid;
    }
    
    .card-header {
        background-color: #1e4072 !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>

</body>
</html>
