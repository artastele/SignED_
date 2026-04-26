<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">
                <i class="bi bi-cloud-upload me-2 text-brand-red"></i>
                Upload Learning Material
            </h1>
            <p class="text-muted mb-0">Assign new learning materials to support IEP objectives</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/<?php echo $data['role'] === 'admin' ? 'admin' : 'sped'; ?>/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <!-- Error Message -->
    <?php if (isset($data['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error:</strong> <?php echo htmlspecialchars($data['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Upload Form Card -->
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <?php if (empty($data['active_ieps'])): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-medical fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No Active IEPs Found</h5>
                    <p class="text-muted mb-4">There are no active IEPs available for material assignment.<br>
                    Please ensure learners have approved and active IEPs before uploading materials.</p>
                    <a href="<?php echo URLROOT; ?>/<?php echo $data['role'] === 'admin' ? 'admin' : 'sped'; ?>/dashboard" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            <?php else: ?>
                <!-- Upload Form -->
                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                    <!-- IEP Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Select IEP <span class="text-danger">*</span>
                        </label>
                        <div class="border rounded p-3 bg-light">
                            <?php foreach ($data['active_ieps'] as $iep): ?>
                            <div class="form-check mb-2 p-2 rounded hover-bg-white">
                                <input class="form-check-input" type="radio" name="iep_id" 
                                       value="<?php echo $iep->id; ?>" 
                                       id="iep_<?php echo $iep->id; ?>" required>
                                <label class="form-check-label w-100" for="iep_<?php echo $iep->id; ?>" style="cursor: pointer;">
                                    <div class="fw-semibold">
                                        <?php echo htmlspecialchars($iep->first_name . ' ' . $iep->last_name); ?>
                                    </div>
                                    <small class="text-muted">
                                        IEP Period: <?php echo date('M j, Y', strtotime($iep->start_date)); ?> - 
                                        <?php echo date('M j, Y', strtotime($iep->end_date)); ?>
                                    </small>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Select the IEP for which you want to upload learning materials.</small>
                    </div>

                    <!-- Material Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">
                            Material Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="title" name="title" class="form-control" 
                               placeholder="Enter a descriptive title for the learning material"
                               value="<?php echo isset($data['form_data']['title']) ? htmlspecialchars($data['form_data']['title']) : ''; ?>" 
                               required>
                        <small class="text-muted">Provide a clear, descriptive title that learners will easily understand.</small>
                    </div>

                    <!-- IEP Objective -->
                    <div class="mb-4">
                        <label for="iep_objective" class="form-label fw-bold">
                            IEP Objective <span class="text-danger">*</span>
                        </label>
                        <textarea id="iep_objective" name="iep_objective" class="form-control" rows="3"
                                  placeholder="Specify which IEP objective this material addresses" 
                                  required><?php echo isset($data['form_data']['iep_objective']) ? htmlspecialchars($data['form_data']['iep_objective']) : ''; ?></textarea>
                        <small class="text-muted">Clearly state which specific IEP objective this material is designed to support.</small>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"
                                  placeholder="Provide additional details about the material and instructions for the learner"><?php echo isset($data['form_data']['description']) ? htmlspecialchars($data['form_data']['description']) : ''; ?></textarea>
                        <small class="text-muted">Optional: Add instructions, context, or additional information about the material.</small>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label for="due_date" class="form-label fw-bold">Due Date</label>
                        <input type="date" id="due_date" name="due_date" class="form-control" 
                               min="<?php echo date('Y-m-d'); ?>"
                               value="<?php echo isset($data['form_data']['due_date']) ? htmlspecialchars($data['form_data']['due_date']) : ''; ?>">
                        <small class="text-muted">Optional: Set a deadline for when the learner should complete this material.</small>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-4">
                        <label for="material_file" class="form-label fw-bold">
                            Learning Material File <span class="text-danger">*</span>
                        </label>
                        <div class="border border-2 border-dashed rounded p-4 text-center bg-light" 
                             id="fileUpload" style="cursor: pointer; transition: all 0.3s;">
                            <i class="bi bi-cloud-arrow-up fs-1 text-muted d-block mb-2"></i>
                            <div class="mb-2">
                                <strong>Click to select a file</strong> or drag and drop
                            </div>
                            <small class="text-muted">Supported formats: PDF, DOC, DOCX, PPT, PPTX, ZIP (Max 10MB)</small>
                            <input type="file" id="material_file" name="material_file" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.zip" 
                                   style="display: none;" required>
                        </div>
                        <div id="fileSelected" class="alert alert-info mt-2" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-semibold" id="fileName"></div>
                                        <small class="text-muted" id="fileSize"></small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger" id="removeFile">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Upload the learning material file that learners will access and work with.</small>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="bi bi-cloud-upload me-1"></i> Upload Material
                        </button>
                        <a href="<?php echo URLROOT; ?>/sped/dashboard" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<style>
.hover-bg-white:hover {
    background-color: white !important;
}

#fileUpload:hover {
    border-color: #0d6efd !important;
    background-color: #e7f1ff !important;
}

#fileUpload.dragover {
    border-color: #dc3545 !important;
    background-color: #fff5f5 !important;
}
</style>

<script>
// File upload handling
const fileUpload = document.getElementById('fileUpload');
const fileInput = document.getElementById('material_file');
const fileSelected = document.getElementById('fileSelected');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const removeFile = document.getElementById('removeFile');
const submitBtn = document.getElementById('submitBtn');

// Click to select file
if (fileUpload) {
    fileUpload.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop handling
    fileUpload.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUpload.classList.add('dragover');
    });

    fileUpload.addEventListener('dragleave', () => {
        fileUpload.classList.remove('dragover');
    });

    fileUpload.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUpload.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelection(files[0]);
        }
    });
}

// File selection handling
if (fileInput) {
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });
}

// Remove file
if (removeFile) {
    removeFile.addEventListener('click', () => {
        fileInput.value = '';
        fileSelected.style.display = 'none';
        updateSubmitButton();
    });
}

function handleFileSelection(file) {
    // Validate file type
    const allowedTypes = ['application/pdf', 'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/zip'];
    
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please select a PDF, DOC, DOCX, PPT, PPTX, or ZIP file.');
        fileInput.value = '';
        return;
    }

    // Validate file size (10MB)
    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('File size exceeds 10MB limit. Please select a smaller file.');
        fileInput.value = '';
        return;
    }

    // Display selected file
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    fileSelected.style.display = 'block';
    updateSubmitButton();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function updateSubmitButton() {
    if (!submitBtn) return;
    
    const hasFile = fileInput && fileInput.files.length > 0;
    const hasIep = document.querySelector('input[name="iep_id"]:checked');
    const titleInput = document.getElementById('title');
    const objectiveInput = document.getElementById('iep_objective');
    const hasTitle = titleInput && titleInput.value.trim();
    const hasObjective = objectiveInput && objectiveInput.value.trim();
    
    submitBtn.disabled = !(hasFile && hasIep && hasTitle && hasObjective);
}

// Form validation
const titleInput = document.getElementById('title');
const objectiveInput = document.getElementById('iep_objective');

if (titleInput) {
    titleInput.addEventListener('input', updateSubmitButton);
}

if (objectiveInput) {
    objectiveInput.addEventListener('input', updateSubmitButton);
}

document.querySelectorAll('input[name="iep_id"]').forEach(radio => {
    radio.addEventListener('change', updateSubmitButton);
});

// Form submission handling
const uploadForm = document.getElementById('uploadForm');
if (uploadForm) {
    uploadForm.addEventListener('submit', (e) => {
        if (!fileInput || !fileInput.files.length) {
            e.preventDefault();
            alert('Please select a file to upload.');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Uploading...';
        }
    });
}

// Initial button state
updateSubmitButton();
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
