<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEEF Form - <?php echo htmlspecialchars($data['enrollment']->learner_first_name . ' ' . $data['enrollment']->learner_last_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .beef-container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .beef-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }
        .beef-header h2 {
            margin: 0;
            font-weight: bold;
            color: #333;
        }
        .beef-header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .section-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
            color: #333;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .info-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 3px;
        }
        .info-value {
            font-weight: 500;
            color: #333;
        }
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        @media print {
            body {
                background-color: white;
            }
            .beef-container {
                max-width: 100%;
                margin: 0;
                padding: 20px;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
            .section-title {
                page-break-after: avoid;
            }
            .info-row {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Action Buttons (No Print) -->
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary mb-2">
            <i class="bi bi-printer me-2"></i>Print
        </button>
        <br>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="bi bi-x-lg me-2"></i>Close
        </button>
    </div>

    <div class="beef-container">
        
        <!-- Document Header -->
        <div class="beef-header">
            <h2>BASIC EDUCATION ENROLLMENT FORM (BEEF)</h2>
            <p>Department of Education</p>
            <p>Special Education (SPED) Program</p>
        </div>

        <?php
        $beef = $data['beef_data'];
        $enrollment = $data['enrollment'];
        ?>

        <!-- Learner Information Section -->
        <div class="section-title">I. LEARNER INFORMATION</div>
        <div class="row">
            <div class="col-md-3 info-row">
                <div class="info-label">First Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['first_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Middle Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['middle_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Last Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['last_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Extension Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['suffix'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Date of Birth</div>
                <div class="info-value"><?php echo $beef['date_of_birth'] ? date('F j, Y', strtotime($beef['date_of_birth'])) : 'N/A'; ?></div>
            </div>
            <div class="col-md-2 info-row">
                <div class="info-label">Age</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['age'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Gender</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['gender'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">PSA Birth Certificate No.</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['psa_birth_cert'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-6 info-row">
                <div class="info-label">Place of Birth</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['place_of_birth'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Mother Tongue</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['mother_tongue'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">LRN (if available)</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['lrn'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <!-- Current Address Section -->
        <div class="section-title">II. CURRENT ADDRESS</div>
        <div class="row">
            <div class="col-md-6 info-row">
                <div class="info-label">House No. / Street</div>
                <div class="info-value"><?php echo htmlspecialchars(trim(($beef['current_house_no'] ?? '') . ' ' . ($beef['current_street'] ?? '')) ?: 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Barangay</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['current_barangay'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">City/Municipality</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['current_city'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Province</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['current_province'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Country</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['current_country'] ?? 'Philippines'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Zip Code</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['current_zip_code'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <!-- Permanent Address Section -->
        <?php if (($beef['same_address'] ?? 'no') === 'no'): ?>
        <div class="section-title">III. PERMANENT ADDRESS</div>
        <div class="row">
            <div class="col-md-6 info-row">
                <div class="info-label">House No. / Street</div>
                <div class="info-value"><?php echo htmlspecialchars(trim(($beef['permanent_house_no'] ?? '') . ' ' . ($beef['permanent_street'] ?? '')) ?: 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Barangay</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['permanent_barangay'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">City/Municipality</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['permanent_city'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Province</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['permanent_province'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Country</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['permanent_country'] ?? 'Philippines'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Zip Code</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['permanent_zip_code'] ?? 'N/A'); ?></div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-title">III. PERMANENT ADDRESS</div>
        <p class="text-muted fst-italic">Same as current address</p>
        <?php endif; ?>

        <!-- Parent/Guardian Information Section -->
        <div class="section-title">IV. PARENT/GUARDIAN INFORMATION</div>
        
        <!-- Father's Information -->
        <div class="fw-bold mb-2" style="font-size: 0.95rem;">Father's Information</div>
        <div class="row">
            <div class="col-md-4 info-row">
                <div class="info-label">Last Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['father_last_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">First Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['father_first_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Middle Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['father_middle_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Contact Number</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['father_contact'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <!-- Mother's Information -->
        <div class="fw-bold mb-2 mt-3" style="font-size: 0.95rem;">Mother's Information</div>
        <div class="row">
            <div class="col-md-4 info-row">
                <div class="info-label">Last Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['mother_last_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">First Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['mother_first_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Middle Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['mother_middle_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Contact Number</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['mother_contact'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <!-- Guardian's Information (if applicable) -->
        <?php if (!empty($beef['guardian_last_name']) || !empty($beef['guardian_first_name'])): ?>
        <div class="fw-bold mb-2 mt-3" style="font-size: 0.95rem;">Guardian's Information</div>
        <div class="row">
            <div class="col-md-4 info-row">
                <div class="info-label">Last Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['guardian_last_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">First Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['guardian_first_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Middle Name</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['guardian_middle_name'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Contact Number</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['guardian_contact'] ?? 'N/A'); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Enrollment Details Section -->
        <div class="section-title">V. ENROLLMENT DETAILS</div>
        <div class="row">
            <div class="col-md-3 info-row">
                <div class="info-label">Student Type</div>
                <div class="info-value">
                    <?php 
                    $studentType = $beef['student_type'] ?? 'new';
                    echo $studentType === 'old' ? 'Returning Student' : 'New Student';
                    ?>
                </div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Grade Level</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['grade_level'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">School Year</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['school_year'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Learning Modality</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['learning_modalities'] ?? 'N/A'); ?></div>
            </div>

            <?php if (($beef['student_type'] ?? 'new') === 'old'): ?>
            <div class="col-12"><hr class="my-3"></div>
            <div class="col-md-3 info-row">
                <div class="info-label">Last Grade Completed</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['last_grade_completed'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Last School Year</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['last_school_year'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-6 info-row">
                <div class="info-label">Last School Attended</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['last_school_attended'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-3 info-row">
                <div class="info-label">Last School ID</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['last_school_id'] ?? 'N/A'); ?></div>
            </div>
            <?php endif; ?>

            <!-- SHS Details (if applicable) -->
            <?php if (!empty($beef['track']) || !empty($beef['strand'])): ?>
            <div class="col-12"><hr class="my-3"></div>
            <div class="col-md-4 info-row">
                <div class="info-label">Semester</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['semester'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Track</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['track'] ?? 'N/A'); ?></div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Strand</div>
                <div class="info-value"><?php echo htmlspecialchars($beef['strand'] ?? 'N/A'); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Special Information Section -->
        <div class="section-title">VI. SPECIAL INFORMATION</div>
        <div class="row">
            <div class="col-md-4 info-row">
                <div class="info-label">Indigenous People</div>
                <div class="info-value">
                    <?php 
                    $isIndigenous = $beef['is_indigenous'] ?? 'No';
                    echo $isIndigenous === 'Yes' ? 'Yes' : 'No';
                    if ($isIndigenous === 'Yes' && !empty($beef['indigenous_specify'])) {
                        echo ' (' . htmlspecialchars($beef['indigenous_specify']) . ')';
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">4Ps Beneficiary</div>
                <div class="info-value">
                    <?php 
                    $is4ps = $beef['is_4ps'] ?? 'No';
                    echo $is4ps === 'Yes' ? 'Yes' : 'No';
                    if ($is4ps === 'Yes' && !empty($beef['4ps_household_id'])) {
                        echo ' (ID: ' . htmlspecialchars($beef['4ps_household_id']) . ')';
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-4 info-row">
                <div class="info-label">Person with Disability</div>
                <div class="info-value">
                    <?php 
                    $isDisabled = $beef['is_disabled'] ?? 'No';
                    echo $isDisabled === 'Yes' ? 'Yes' : 'No';
                    if ($isDisabled === 'Yes' && !empty($beef['disability_types'])) {
                        echo ' (' . htmlspecialchars($beef['disability_types']) . ')';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Document Footer -->
        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd;">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">Submitted: <?php echo date('F j, Y g:i A', strtotime($enrollment->created_at)); ?></small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">Enrollment ID: <?php echo htmlspecialchars($enrollment->id); ?></small>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
