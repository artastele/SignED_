<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1>
            <i class="bi bi-cloud-upload me-2"></i>
            Upload IEP Draft Document
        </h1>
        <p class="mb-0">Upload IEP draft for <?php echo htmlspecialchars($data['iep']->first_name . ' ' . $data['iep']->last_name); ?></p>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($data['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Instructions -->
    <div class="alert alert-info mb-4">
        <h5 class="alert-heading">
            <i class="bi bi-info-circle me-2"></i>
            Instructions
        </h5>
        <ul class="mb-0">
            <li>Upload the completed IEP draft document in PDF format</li>
            <li>Maximum file size: 10MB</li>
            <li>Once uploaded, the IEP draft will be shared with:
                <ul>
                    <li><strong>Guidance Counselor</strong> - for behavioral insights and feedback</li>
                    <li><strong>Principal</strong> - for review and initial comments</li>
                    <li><strong>Parent/Guardian</strong> - for information</li>
                </ul>
            </li>
            <li>After uploading, you can proceed to schedule the IEP meeting</li>
        </ul>
    </div>

    <!-- Upload Form -->
    <form method="POST" action="<?php echo URLROOT; ?>/iep/uploadDraft?iep_id=<?php echo $data['iep']->id; ?>" enctype="multipart/form-data">
        
        <!-- Learner Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Learner Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->first_name . ' ' . ($data['iep']->middle_name ?? '') . ' ' . $data['iep']->last_name . ' ' . ($data['iep']->suffix ?? '')); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">LRN</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->lrn ?? 'N/A'); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Grade Level</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($data['iep']->grade_level ?? 'N/A'); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Upload -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-pdf me-2"></i>
                    IEP Draft Document
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="iep_draft" class="form-label fw-bold">
                        Select PDF File <span class="text-danger">*</span>
                    </label>
                    <input type="file" class="form-control" id="iep_draft" name="iep_draft" accept=".pdf" required>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Only PDF files are accepted. Maximum size: 10MB
                    </small>
                </div>

                <!-- File Preview -->
                <div id="file-preview" class="alert alert-secondary d-none">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-pdf fs-1 me-3 text-danger"></i>
                        <div class="flex-grow-1">
                            <strong id="file-name"></strong>
                            <br>
                            <small id="file-size" class="text-muted"></small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                            <i class="bi bi-x-circle"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipients Information -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Document Will Be Shared With
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <i class="bi bi-person-heart fs-1 text-success mb-2"></i>
                                <h6 class="fw-bold">Guidance Counselor</h6>
                                <p class="small text-muted mb-0">For behavioral insights and feedback</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <i class="bi bi-person-badge fs-1 text-primary mb-2"></i>
                                <h6 class="fw-bold">Principal</h6>
                                <p class="small text-muted mb-0">For review and initial comments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-person-check fs-1 text-warning mb-2"></i>
                                <h6 class="fw-bold">Parent/Guardian</h6>
                                <p class="small text-muted mb-0">For information and awareness</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-bell me-2"></i>
                    <strong>Note:</strong> All recipients will receive email notifications with a link to view the IEP draft.
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?php echo URLROOT; ?>/iep/create?learner_id=<?php echo $data['iep']->learner_id; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Edit Draft
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" id="upload-btn">
                        <i class="bi bi-cloud-upload me-1"></i> Upload & Share IEP Draft
                    </button>
                </div>
            </div>
        </div>

    </form>

</main>

<script>
// File input change handler
document.getElementById('iep_draft').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const uploadBtn = document.getElementById('upload-btn');

    if (file) {
        // Validate file type
        if (file.type !== 'application/pdf') {
            alert('Only PDF files are allowed');
            e.target.value = '';
            preview.classList.add('d-none');
            return;
        }

        // Validate file size (10MB)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File size must not exceed 10MB');
            e.target.value = '';
            preview.classList.add('d-none');
            return;
        }

        // Show preview
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        preview.classList.remove('d-none');
        uploadBtn.disabled = false;
    } else {
        preview.classList.add('d-none');
        uploadBtn.disabled = true;
    }
});

function clearFile() {
    document.getElementById('iep_draft').value = '';
    document.getElementById('file-preview').classList.add('d-none');
    document.getElementById('upload-btn').disabled = true;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Form submission handler
document.querySelector('form').addEventListener('submit', function(e) {
    const uploadBtn = document.getElementById('upload-btn');
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
