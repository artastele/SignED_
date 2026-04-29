<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Sidebar -->
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    
    <!-- Page Header -->
    <div class="mb-4">
        <h4><i class="bi bi-upload me-2"></i>Upload Enrollment Documents</h4>
        <p class="text-muted small mb-0">Upload all required documents to complete your enrollment</p>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($data['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($data['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($data['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Upload Progress -->
    <?php
    $psaUploaded = isset($data['uploaded_types']['psa']) ? 1 : 0;
    $totalRequired = 1;
    ?>
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="mb-2"><i class="bi bi-graph-up me-2"></i>Upload Progress</h6>
            <p class="mb-0"><strong><?php echo $psaUploaded; ?> / <?php echo $totalRequired; ?> required document</strong></p>
            <p class="text-muted small mb-0">PSA Birth Certificate is required to proceed</p>
        </div>
    </div>
    
    <!-- Documents Grid -->
    <div class="row g-3">
        
        <!-- BEEF Form - Already Submitted -->
        <div class="col-md-6">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-2">
                    <span class="small"><i class="bi bi-file-earmark-check me-2"></i>Basic Education Enrollment Form (BEEF)</span>
                    <span class="badge bg-light text-success">✓ Submitted</span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Basic Education Enrollment Form from DepEd</p>
                    <div class="bg-light p-2 rounded small">
                        <div class="fw-semibold">BEEF Form - <?php echo htmlspecialchars($data['enrollment']->learner_first_name . ' ' . $data['enrollment']->learner_last_name); ?></div>
                        <div class="text-muted">✅ Submitted during enrollment process</div>
                        <div class="text-muted">📅 <?php echo date('M d, Y g:i A', strtotime($data['enrollment']->created_at)); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PSA Birth Certificate - Required -->
        <div class="col-md-6">
            <div class="card <?php echo isset($data['uploaded_types']['psa']) ? 'border-success' : 'border-danger'; ?> h-100">
                <div class="card-header <?php echo isset($data['uploaded_types']['psa']) ? 'bg-success' : 'bg-danger'; ?> text-white d-flex justify-content-between align-items-center py-2">
                    <span class="small"><i class="bi bi-file-earmark-text me-2"></i>PSA Birth Certificate <span class="text-warning">*</span></span>
                    <span class="badge bg-light <?php echo isset($data['uploaded_types']['psa']) ? 'text-success' : 'text-danger'; ?>">
                        <?php echo isset($data['uploaded_types']['psa']) ? '✓ Uploaded' : '⚠ Required'; ?>
                    </span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Official birth certificate from Philippine Statistics Authority (REQUIRED)</p>
                    
                    <?php if (isset($data['uploaded_types']['psa'])): ?>
                        <div class="bg-light p-2 rounded small mb-2">
                            <div class="fw-semibold"><?php echo htmlspecialchars($data['uploaded_types']['psa']->original_filename); ?></div>
                            <div class="text-muted">📅 Uploaded: <?php echo date('M d, Y g:i A', strtotime($data['uploaded_types']['psa']->uploaded_at)); ?></div>
                            <div class="text-muted">📦 Size: <?php echo number_format($data['uploaded_types']['psa']->file_size / 1024, 1); ?> KB</div>
                        </div>
                        <button class="btn btn-sm btn-success" onclick="replaceDocument('psa', '<?php echo $data['enrollment']->id; ?>')">
                            <i class="bi bi-arrow-repeat me-1"></i>Replace
                        </button>
                    <?php else: ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $data['enrollment']->id; ?>" 
                              enctype="multipart/form-data" id="form-psa">
                            <input type="hidden" name="document_type" value="psa">
                            <div class="border border-2 border-dashed rounded text-center p-3 mb-2" 
                                 id="upload-area-psa" 
                                 onclick="document.getElementById('file-psa').click()"
                                 style="cursor: pointer;">
                                <i class="bi bi-cloud-upload fs-3 text-muted d-block"></i>
                                <div class="small text-muted">Click to select file or drag and drop here</div>
                            </div>
                            <input type="file" id="file-psa" name="document" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="handleFileSelect(this, 'psa')">
                            <button type="submit" class="btn btn-primary btn-sm w-100" id="submit-psa" style="display: none;">
                                <i class="bi bi-upload me-1"></i>Upload
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- PWD ID Card - Optional -->
        <div class="col-md-6">
            <div class="card <?php echo isset($data['uploaded_types']['pwd_id']) ? 'border-success' : 'border-secondary'; ?> h-100">
                <div class="card-header <?php echo isset($data['uploaded_types']['pwd_id']) ? 'bg-success' : 'bg-secondary'; ?> text-white d-flex justify-content-between align-items-center py-2">
                    <span class="small"><i class="bi bi-file-earmark-text me-2"></i>PWD ID Card</span>
                    <span class="badge bg-light <?php echo isset($data['uploaded_types']['pwd_id']) ? 'text-success' : 'text-secondary'; ?>">
                        <?php echo isset($data['uploaded_types']['pwd_id']) ? '✓ Uploaded' : 'Optional'; ?>
                    </span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Person with Disability identification card (upload if available)</p>
                    
                    <?php if (isset($data['uploaded_types']['pwd_id'])): ?>
                        <div class="bg-light p-2 rounded small mb-2">
                            <div class="fw-semibold"><?php echo htmlspecialchars($data['uploaded_types']['pwd_id']->original_filename); ?></div>
                            <div class="text-muted">📅 Uploaded: <?php echo date('M d, Y g:i A', strtotime($data['uploaded_types']['pwd_id']->uploaded_at)); ?></div>
                            <div class="text-muted">📦 Size: <?php echo number_format($data['uploaded_types']['pwd_id']->file_size / 1024, 1); ?> KB</div>
                        </div>
                        <button class="btn btn-sm btn-success" onclick="replaceDocument('pwd_id', '<?php echo $data['enrollment']->id; ?>')">
                            <i class="bi bi-arrow-repeat me-1"></i>Replace
                        </button>
                    <?php else: ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $data['enrollment']->id; ?>" 
                              enctype="multipart/form-data" id="form-pwd_id">
                            <input type="hidden" name="document_type" value="pwd_id">
                            <div class="border border-2 border-dashed rounded text-center p-3 mb-2" 
                                 id="upload-area-pwd_id" 
                                 onclick="document.getElementById('file-pwd_id').click()"
                                 style="cursor: pointer;">
                                <i class="bi bi-cloud-upload fs-3 text-muted d-block"></i>
                                <div class="small text-muted">Click to select file or drag and drop here</div>
                            </div>
                            <input type="file" id="file-pwd_id" name="document" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="handleFileSelect(this, 'pwd_id')">
                            <button type="submit" class="btn btn-primary btn-sm w-100" id="submit-pwd_id" style="display: none;">
                                <i class="bi bi-upload me-1"></i>Upload
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Medical Records - Optional -->
        <div class="col-md-6">
            <div class="card <?php echo isset($data['uploaded_types']['medical_record']) ? 'border-success' : 'border-secondary'; ?> h-100">
                <div class="card-header <?php echo isset($data['uploaded_types']['medical_record']) ? 'bg-success' : 'bg-secondary'; ?> text-white d-flex justify-content-between align-items-center py-2">
                    <span class="small"><i class="bi bi-file-earmark-text me-2"></i>Medical Records</span>
                    <span class="badge bg-light <?php echo isset($data['uploaded_types']['medical_record']) ? 'text-success' : 'text-secondary'; ?>">
                        <?php echo isset($data['uploaded_types']['medical_record']) ? '✓ Uploaded' : 'Optional'; ?>
                    </span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Medical documentation supporting disability status (upload if available)</p>
                    
                    <?php if (isset($data['uploaded_types']['medical_record'])): ?>
                        <div class="bg-light p-2 rounded small mb-2">
                            <div class="fw-semibold"><?php echo htmlspecialchars($data['uploaded_types']['medical_record']->original_filename); ?></div>
                            <div class="text-muted">📅 Uploaded: <?php echo date('M d, Y g:i A', strtotime($data['uploaded_types']['medical_record']->uploaded_at)); ?></div>
                            <div class="text-muted">📦 Size: <?php echo number_format($data['uploaded_types']['medical_record']->file_size / 1024, 1); ?> KB</div>
                        </div>
                        <button class="btn btn-sm btn-success" onclick="replaceDocument('medical_record', '<?php echo $data['enrollment']->id; ?>')">
                            <i class="bi bi-arrow-repeat me-1"></i>Replace
                        </button>
                    <?php else: ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/enrollment/upload?id=<?php echo $data['enrollment']->id; ?>" 
                              enctype="multipart/form-data" id="form-medical_record">
                            <input type="hidden" name="document_type" value="medical_record">
                            <div class="border border-2 border-dashed rounded text-center p-3 mb-2" 
                                 id="upload-area-medical_record" 
                                 onclick="document.getElementById('file-medical_record').click()"
                                 style="cursor: pointer;">
                                <i class="bi bi-cloud-upload fs-3 text-muted d-block"></i>
                                <div class="small text-muted">Click to select file or drag and drop here</div>
                            </div>
                            <input type="file" id="file-medical_record" name="document" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="handleFileSelect(this, 'medical_record')">
                            <button type="submit" class="btn btn-primary btn-sm w-100" id="submit-medical_record" style="display: none;">
                                <i class="bi bi-upload me-1"></i>Upload
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Back Button -->
    <div class="mt-4">
        <a href="<?php echo URLROOT; ?>/parent/dashboard" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
    
</main>

<script>
function handleFileSelect(input, documentType) {
    const file = input.files[0];
    if (!file) return;
    
    if (file.size > 5 * 1024 * 1024) {
        alert('File size exceeds 5MB limit. Please select a smaller file.');
        input.value = '';
        return;
    }
    
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please select a PDF, JPG, or PNG file.');
        input.value = '';
        return;
    }
    
    const uploadArea = document.getElementById('upload-area-' + documentType);
    uploadArea.innerHTML = `
        <i class="bi bi-check-circle fs-3 text-success d-block"></i>
        <div class="small fw-semibold">${file.name}</div>
        <div class="small text-muted">Size: ${(file.size / 1024).toFixed(1)} KB</div>
    `;
    
    document.getElementById('submit-' + documentType).style.display = 'block';
}

function replaceDocument(documentType, enrollmentId) {
    if (confirm('Are you sure you want to replace this document?')) {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.pdf,.jpg,.jpeg,.png';
        fileInput.onchange = function() {
            const file = this.files[0];
            if (!file) return;
            
            if (file.size > 5 * 1024 * 1024) {
                alert('File size exceeds 5MB limit.');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo URLROOT; ?>/enrollment/upload?id=' + enrollmentId;
            form.enctype = 'multipart/form-data';
            
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'document_type';
            typeInput.value = documentType;
            
            const fileInputClone = document.createElement('input');
            fileInputClone.type = 'file';
            fileInputClone.name = 'document';
            fileInputClone.files = this.files;
            
            form.appendChild(typeInput);
            form.appendChild(fileInputClone);
            document.body.appendChild(form);
            form.submit();
        };
        fileInput.click();
    }
}

// Drag and drop
document.querySelectorAll('[id^="upload-area-"]').forEach(area => {
    area.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#0d6efd';
        this.style.backgroundColor = '#e7f1ff';
    });
    
    area.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '';
        this.style.backgroundColor = '';
    });
    
    area.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '';
        this.style.backgroundColor = '';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const documentType = this.id.replace('upload-area-', '');
            const fileInput = document.getElementById('file-' + documentType);
            fileInput.files = files;
            handleFileSelect(fileInput, documentType);
        }
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
