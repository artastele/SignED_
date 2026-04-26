<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Collapsed Sidebar for Forms -->
<nav class="col-auto d-md-block bg-white sidebar-collapsed shadow-sm">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column text-center">
            <li class="nav-item mb-3">
                <a class="nav-link" href="<?php echo URLROOT; ?>/parent/dashboard" title="Dashboard">
                    <i class="bi bi-house-door fs-4"></i>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link active" href="<?php echo URLROOT; ?>/enrollment/beef" title="Enroll Child">
                    <i class="bi bi-person-plus fs-4"></i>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="<?php echo URLROOT; ?>/parent/manageRequirements" title="Requirements">
                    <i class="bi bi-file-earmark-text fs-4"></i>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="<?php echo URLROOT; ?>/parent/children" title="My Children">
                    <i class="bi bi-people fs-4"></i>
                </a>
            </li>
        </ul>
        
        <hr class="my-3">
        
        <ul class="nav flex-column text-center">
            <li class="nav-item mb-3">
                <a class="nav-link" href="<?php echo URLROOT; ?>/user/profile" title="Profile">
                    <i class="bi bi-person fs-4"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?php echo URLROOT; ?>/auth/logout" title="Logout">
                    <i class="bi bi-box-arrow-right fs-4"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Main Content -->
<main class="col px-md-4" style="margin-left: 80px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <div>
            <a href="<?php echo URLROOT; ?>/parent/dashboard" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
            <h1 class="h2 d-inline-block mb-0">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                Basic Education Enrollment Form (BEEF)
            </h1>
        </div>
    </div>

    <?php include '../app/views/partials/simple_popup.php'; ?>

    <!-- Select2 CSS for Better Dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p class="text-muted mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Fill in all required information about the learner. Fields marked with <span class="text-danger">*</span> are required.
            </p>

            <form action="<?php echo URLROOT; ?>/enrollment/beef" method="POST" id="beefForm">
                
                <!-- Student Type Selection -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Student Type</h5>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="student_type" id="type_new" value="new" checked>
                            <label class="btn btn-outline-primary" for="type_new">New Student</label>

                            <input type="radio" class="btn-check" name="student_type" id="type_old" value="old">
                            <label class="btn btn-outline-primary" for="type_old">Old Student</label>

                            <input type="radio" class="btn-check" name="student_type" id="type_transfer" value="transfer">
                            <label class="btn btn-outline-primary" for="type_transfer">Transfer Student</label>
                        </div>
                    </div>

                    <!-- Old Student LRN Lookup -->
                    <div class="alert alert-info" id="oldStudentSection" style="display: none;">
                        <h6><i class="bi bi-search me-2"></i>Returning Learner (Balik-Aral)</h6>
                        <p class="small mb-3">Enter the Learner Reference Number (LRN) to auto-fill information</p>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="lrn_lookup" placeholder="Enter 12-digit LRN" maxlength="12">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" onclick="lookupLRN()">
                                    <i class="bi bi-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Year and Grade Level -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-calendar me-2 text-primary"></i>School Year and Grade Level
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">School Year <span class="text-danger">*</span></label>
                            <select class="form-select" name="school_year" required>
                                <option value="">Select School Year</option>
                                <option value="2024-2025" selected>2024-2025</option>
                                <option value="2025-2026">2025-2026</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grade Level to Enroll <span class="text-danger">*</span></label>
                            <select class="form-select" name="grade_level" id="grade_level" required>
                                <option value="">Select Grade Level</option>
                                <option value="Kinder">Kindergarten</option>
                                <option value="1">Grade 1</option>
                                <option value="2">Grade 2</option>
                                <option value="3">Grade 3</option>
                                <option value="4">Grade 4</option>
                                <option value="5">Grade 5</option>
                                <option value="6">Grade 6</option>
                                <option value="7">Grade 7</option>
                                <option value="8">Grade 8</option>
                                <option value="9">Grade 9</option>
                                <option value="10">Grade 10</option>
                                <option value="11">Grade 11</option>
                                <option value="12">Grade 12</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Learner Information -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-person me-2 text-primary"></i>Learner Information
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">PSA Birth Certificate No. (if available)</label>
                            <input type="text" class="form-control" name="psa_birth_cert_no" id="psa_birth_cert_no">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Learner Reference No. (LRN)</label>
                            <input type="text" class="form-control" name="lrn" id="lrn" placeholder="12-digit learner reference number" maxlength="12">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" id="last_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" id="first_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" id="middle_name">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Extension Name (Jr., Sr., III)</label>
                            <input type="text" class="form-control" name="extension_name" id="extension_name" placeholder="e.g., Jr., III">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mother Tongue <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mother_tongue" id="mother_tongue" required placeholder="e.g., Cebuano">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="birthdate" id="birthdate" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" id="age" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Sex <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sex" id="sex_male" value="Male" required>
                                    <label class="form-check-label" for="sex_male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sex" id="sex_female" value="Female" required>
                                    <label class="form-check-label" for="sex_female">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Place of Birth (Municipality/City) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="place_of_birth" id="place_of_birth" required placeholder="e.g., Cebu City">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Belonging to any Indigenous Peoples (IP) Community/Indigenous Cultural Community? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_indigenous" id="ip_yes" value="Yes" required>
                                    <label class="form-check-label" for="ip_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_indigenous" id="ip_no" value="No" required>
                                    <label class="form-check-label" for="ip_no">No</label>
                                </div>
                            </div>
                            <input type="text" class="form-control mt-2" name="indigenous_specify" id="indigenous_specify" placeholder="If Yes, please specify" style="display: none;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Is your family a beneficiary of 4Ps? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_4ps" id="4ps_yes" value="Yes" required>
                                    <label class="form-check-label" for="4ps_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_4ps" id="4ps_no" value="No" required>
                                    <label class="form-check-label" for="4ps_no">No</label>
                                </div>
                            </div>
                            <input type="text" class="form-control mt-2" name="4ps_household_id" id="4ps_household_id" placeholder="If Yes, write the 4Ps Household ID Number" style="display: none;">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Is the child a Learner with Disability? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_disabled" id="disabled_yes" value="Yes" required>
                                    <label class="form-check-label" for="disabled_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_disabled" id="disabled_no" value="No" required>
                                    <label class="form-check-label" for="disabled_no">No</label>
                                </div>
                            </div>
                            
                            <div id="disability_types" class="mt-3" style="display: none;">
                                <p class="small text-muted mb-2">If Yes, specify the type of disability:</p>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Hearing Impairment" id="dis_hearing">
                                            <label class="form-check-label" for="dis_hearing">Hearing Impairment</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Autism Spectrum Disorder" id="dis_autism">
                                            <label class="form-check-label" for="dis_autism">Autism Spectrum Disorder</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Speech/Language Disorder" id="dis_speech">
                                            <label class="form-check-label" for="dis_speech">Speech/Language Disorder</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Learning Disability" id="dis_learning">
                                            <label class="form-check-label" for="dis_learning">Learning Disability</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Emotional-Behavioral" id="dis_emotional">
                                            <label class="form-check-label" for="dis_emotional">Emotional-Behavioral</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Cerebral Palsy" id="dis_cerebral">
                                            <label class="form-check-label" for="dis_cerebral">Cerebral Palsy</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Intellectual Disability" id="dis_intellectual">
                                            <label class="form-check-label" for="dis_intellectual">Intellectual Disability</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Physical Handicap" id="dis_physical">
                                            <label class="form-check-label" for="dis_physical">Physical Handicap</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="disability[]" value="Multiple Disorder" id="dis_multiple">
                                            <label class="form-check-label" for="dis_multiple">Multiple Disorder</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Address -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-geo-alt me-2 text-primary"></i>Current Address
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label">House No.</label>
                            <input type="text" class="form-control" name="current_house_no" id="current_house_no">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Street Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="current_street" id="current_street" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select class="form-select" name="current_barangay" id="current_barangay" required>
                                <option value="">Select Barangay</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Municipality/City <span class="text-danger">*</span></label>
                            <select class="form-select" name="current_city" id="current_city" required>
                                <option value="">Select Municipality/City</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Province <span class="text-danger">*</span></label>
                            <select class="form-select" name="current_province" id="current_province" required>
                                <option value="">Select Province</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="current_country" id="current_country" value="Philippines" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Zip Code</label>
                            <input type="text" class="form-control" name="current_zip_code" id="current_zip_code" maxlength="4">
                        </div>
                    </div>
                </div>

                <!-- Permanent Address -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-house me-2 text-primary"></i>Permanent Address
                    </h5>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="same_address" name="same_address">
                        <label class="form-check-label" for="same_address">
                            Same with your Current Address?
                        </label>
                    </div>

                    <div id="permanent_address_fields">
                        <div class="row g-3 mb-3">
                            <div class="col-md-2">
                                <label class="form-label">House No.</label>
                                <input type="text" class="form-control" name="permanent_house_no" id="permanent_house_no">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Street Name</label>
                                <input type="text" class="form-control" name="permanent_street" id="permanent_street">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Barangay</label>
                                <select class="form-select" name="permanent_barangay" id="permanent_barangay">
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Municipality/City</label>
                                <select class="form-select" name="permanent_city" id="permanent_city">
                                    <option value="">Select Municipality/City</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Province</label>
                                <select class="form-select" name="permanent_province" id="permanent_province">
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" name="permanent_country" id="permanent_country" value="Philippines">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Zip Code</label>
                                <input type="text" class="form-control" name="permanent_zip_code" id="permanent_zip_code" maxlength="4">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent/Guardian Information -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-people me-2 text-primary"></i>Parent/Guardian's Information <span class="text-danger">*</span>
                    </h5>

                    <!-- Father's Information -->
                    <h6 class="text-muted mb-3">Father's Name</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="father_last_name" id="father_last_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="father_first_name" id="father_first_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="father_middle_name" id="father_middle_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact No.</label>
                            <input type="tel" class="form-control" name="father_contact" id="father_contact" placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>

                    <!-- Mother's Maiden Name -->
                    <h6 class="text-muted mb-3">Mother's Maiden Name</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="mother_last_name" id="mother_last_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="mother_first_name" id="mother_first_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="mother_middle_name" id="mother_middle_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact No.</label>
                            <input type="tel" class="form-control" name="mother_contact" id="mother_contact" placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>

                    <!-- Guardian's Name -->
                    <h6 class="text-muted mb-3">Guardian's Name (if not parent)</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="guardian_last_name" id="guardian_last_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="guardian_first_name" id="guardian_first_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="guardian_middle_name" id="guardian_middle_name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact No.</label>
                            <input type="tel" class="form-control" name="guardian_contact" id="guardian_contact" placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>
                </div>

                <!-- For Senior High School Learners -->
                <div class="mb-4" id="shs_section" style="display: none;">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-mortarboard me-2 text-primary"></i>For Senior High School Learners
                    </h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <select class="form-select" name="semester" id="semester">
                                <option value="">Select Semester</option>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Track</label>
                            <select class="form-select" name="track" id="track">
                                <option value="">Select Track</option>
                                <option value="Academic">Academic</option>
                                <option value="TVL">Technical-Vocational-Livelihood (TVL)</option>
                                <option value="Sports">Sports</option>
                                <option value="Arts and Design">Arts and Design</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Strand</label>
                            <select class="form-select" name="strand" id="strand">
                                <option value="">Select Strand</option>
                                <option value="STEM">STEM</option>
                                <option value="ABM">ABM</option>
                                <option value="HUMSS">HUMSS</option>
                                <option value="GAS">GAS</option>
                                <option value="TVL-HE">TVL - Home Economics</option>
                                <option value="TVL-ICT">TVL - ICT</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">If school will implement other distance learning modalities aside from face-to-face instruction, what would you prefer for your child?</label>
                        <p class="small text-muted mb-2">(Choose all that apply)</p>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="learning_modality[]" value="Modular (Print)" id="mod_print">
                                    <label class="form-check-label" for="mod_print">Modular (Print)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="learning_modality[]" value="Modular (Digital)" id="mod_digital">
                                    <label class="form-check-label" for="mod_digital">Modular (Digital)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="learning_modality[]" value="Online" id="mod_online">
                                    <label class="form-check-label" for="mod_online">Online</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="learning_modality[]" value="Educational Television" id="mod_tv">
                                    <label class="form-check-label" for="mod_tv">Educational Television</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="learning_modality[]" value="Radio-Based Instruction" id="mod_radio">
                                    <label class="form-check-label" for="mod_radio">Radio-Based Instruction</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="learning_modality[]" value="Homeschooling" id="mod_home">
                                    <label class="form-check-label" for="mod_home">Homeschooling</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="learning_modality[]" value="Blended" id="mod_blended">
                                    <label class="form-check-label" for="mod_blended">Blended</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Returning Learner (Balik-Aral) and Those Who will Transfer/Move In -->
                <div class="mb-4" id="returning_transfer_section" style="display: none;">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-arrow-repeat me-2 text-primary"></i>Returning Learner (Balik-Aral) and Those Who will Transfer/Move In
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Last Grade Completed <span class="text-danger">*</span></label>
                            <select class="form-select" name="last_grade_completed" id="last_grade_completed">
                                <option value="">Select Grade</option>
                                <option value="Kinder">Kindergarten</option>
                                <option value="1">Grade 1</option>
                                <option value="2">Grade 2</option>
                                <option value="3">Grade 3</option>
                                <option value="4">Grade 4</option>
                                <option value="5">Grade 5</option>
                                <option value="6">Grade 6</option>
                                <option value="7">Grade 7</option>
                                <option value="8">Grade 8</option>
                                <option value="9">Grade 9</option>
                                <option value="10">Grade 10</option>
                                <option value="11">Grade 11</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last School Year Completed <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_school_year" id="last_school_year" placeholder="e.g., 2023-2024">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-8">
                            <label class="form-label">Last School Attended <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_school_attended" id="last_school_attended">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">School ID</label>
                            <input type="text" class="form-control" name="last_school_id" id="last_school_id">
                        </div>
                    </div>
                </div>

                <!-- Disclaimer and Certification -->
                <div class="mb-4">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="bi bi-shield-check me-2"></i>Data Privacy and Certification</h6>
                        <p class="mb-0 small">
                            I hereby certify that the above information given are true and correct to the best of my knowledge and I allow the Department of Education to use my child's details to create and/or update his/her learner profile in the Learner Information System. The information herein shall be treated as confidential in compliance with the Data Privacy Act of 2012.
                        </p>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                        <label class="form-check-label" for="agree_terms">
                            I have read and agree to the above certification <span class="text-danger">*</span>
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                        <i class="bi bi-save me-1"></i>Save Draft
                    </button>
                    <a href="<?php echo URLROOT; ?>/parent/dashboard" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

</main>

<style>
.sidebar-collapsed {
    width: 80px;
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 0;
}

.sidebar-collapsed .nav-link {
    color: #6b7280;
    padding: 12px 0;
    transition: all 0.3s ease;
}

.sidebar-collapsed .nav-link:hover {
    color: #a01422;
    background-color: #f3f4f6;
}

.sidebar-collapsed .nav-link.active {
    color: #a01422;
    background-color: #fee2e2;
}

main {
    margin-left: 80px;
}

@media (max-width: 768px) {
    .sidebar-collapsed {
        display: none;
    }
    
    main {
        margin-left: 0 !important;
    }
}
</style>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Student Type Selection
document.querySelectorAll('input[name="student_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const oldStudentSection = document.getElementById('oldStudentSection');
        const returningTransferSection = document.getElementById('returning_transfer_section');
        
        if (this.value === 'old') {
            oldStudentSection.style.display = 'block';
            returningTransferSection.style.display = 'block';
        } else if (this.value === 'transfer') {
            oldStudentSection.style.display = 'none';
            returningTransferSection.style.display = 'block';
        } else {
            oldStudentSection.style.display = 'none';
            returningTransferSection.style.display = 'none';
        }
    });
});

// Grade Level Change - Show/Hide SHS Section
document.getElementById('grade_level').addEventListener('change', function() {
    const shsSection = document.getElementById('shs_section');
    if (this.value === '11' || this.value === '12') {
        shsSection.style.display = 'block';
    } else {
        shsSection.style.display = 'none';
    }
});

// Calculate Age from Birthdate
document.getElementById('birthdate').addEventListener('change', function() {
    const birthdate = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    const monthDiff = today.getMonth() - birthdate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
        age--;
    }
    
    document.getElementById('age').value = age;
});

// Indigenous Peoples - Show/Hide Specify Field
document.querySelectorAll('input[name="is_indigenous"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const specifyField = document.getElementById('indigenous_specify');
        if (this.value === 'Yes') {
            specifyField.style.display = 'block';
            specifyField.required = true;
        } else {
            specifyField.style.display = 'none';
            specifyField.required = false;
            specifyField.value = '';
        }
    });
});

// 4Ps - Show/Hide Household ID Field
document.querySelectorAll('input[name="is_4ps"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const householdField = document.getElementById('4ps_household_id');
        if (this.value === 'Yes') {
            householdField.style.display = 'block';
            householdField.required = true;
        } else {
            householdField.style.display = 'none';
            householdField.required = false;
            householdField.value = '';
        }
    });
});

// Disability - Show/Hide Disability Types
document.querySelectorAll('input[name="is_disabled"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const disabilityTypes = document.getElementById('disability_types');
        if (this.value === 'Yes') {
            disabilityTypes.style.display = 'block';
        } else {
            disabilityTypes.style.display = 'none';
            // Uncheck all disability checkboxes
            document.querySelectorAll('input[name="disability[]"]').forEach(cb => cb.checked = false);
        }
    });
});

// LRN Lookup Function (for Old Students)
function lookupLRN() {
    const lrn = document.getElementById('lrn_lookup').value.trim();
    
    if (lrn.length !== 12) {
        alert('Please enter a valid 12-digit LRN');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Searching...';
    
    // Make AJAX call to lookup LRN
    fetch('<?php echo URLROOT; ?>/enrollment/lookupLRN', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'lrn=' + encodeURIComponent(lrn)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Auto-fill form with learner data
            const learner = data.learner;
            
            // Basic Information
            document.getElementById('lrn').value = learner.lrn || '';
            document.getElementById('last_name').value = learner.last_name || '';
            document.getElementById('first_name').value = learner.first_name || '';
            document.getElementById('middle_name').value = learner.middle_name || '';
            document.getElementById('extension_name').value = learner.extension_name || '';
            document.getElementById('birthdate').value = learner.date_of_birth || '';
            document.getElementById('mother_tongue').value = learner.mother_tongue || '';
            document.getElementById('place_of_birth').value = learner.place_of_birth || '';
            
            // Gender
            if (learner.gender === 'Male') {
                document.getElementById('sex_male').checked = true;
            } else if (learner.gender === 'Female') {
                document.getElementById('sex_female').checked = true;
            }
            
            // Last Grade Completed
            if (learner.last_grade_completed) {
                document.getElementById('last_grade_completed').value = learner.last_grade_completed;
            }
            
            // If previous enrollment data exists, fill additional fields
            if (data.previous_data) {
                const prevData = data.previous_data;
                
                // Fill any additional fields from previous enrollment
                if (prevData.nationality) document.getElementById('nationality').value = prevData.nationality;
                if (prevData.religion) document.getElementById('religion').value = prevData.religion;
                
                // Address fields
                if (prevData.current_house_no) document.getElementById('current_house_no').value = prevData.current_house_no;
                if (prevData.current_street) document.getElementById('current_street').value = prevData.current_street;
                
                // Parent information
                if (prevData.father_last_name) document.getElementById('father_last_name').value = prevData.father_last_name;
                if (prevData.father_first_name) document.getElementById('father_first_name').value = prevData.father_first_name;
                if (prevData.father_middle_name) document.getElementById('father_middle_name').value = prevData.father_middle_name;
                if (prevData.father_contact) document.getElementById('father_contact').value = prevData.father_contact;
                
                if (prevData.mother_last_name) document.getElementById('mother_last_name').value = prevData.mother_last_name;
                if (prevData.mother_first_name) document.getElementById('mother_first_name').value = prevData.mother_first_name;
                if (prevData.mother_middle_name) document.getElementById('mother_middle_name').value = prevData.mother_middle_name;
                if (prevData.mother_contact) document.getElementById('mother_contact').value = prevData.mother_contact;
            }
            
            // Trigger age calculation
            document.getElementById('birthdate').dispatchEvent(new Event('change'));
            
            // Show success message
            alert('Learner information loaded successfully! Please review and update any necessary fields.');
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while looking up the LRN. Please try again.');
    })
    .finally(() => {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// Save Draft Function
function saveDraft() {
    alert('Draft save feature will be implemented. Your progress will be saved.');
}

// Auto-format name fields (capitalize first letter)
const nameFields = document.querySelectorAll('input[name*="name"], input[name*="Name"]');
nameFields.forEach(field => {
    field.addEventListener('blur', function() {
        let value = this.value.trim();
        value = value.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
        value = value.replace(/\s+/g, ' ');
        this.value = value;
    });
});

// ========================================
// PHILIPPINE LOCATION API INTEGRATION
// ========================================

// API Base URL
const PSGC_API = 'https://psgc.gitlab.io/api';

// Load Provinces on Page Load
document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
    
    // Initialize Select2 for better search functionality
    setTimeout(initializeSelect2, 1000); // Wait for data to load
});

// Initialize Select2 on all location dropdowns
function initializeSelect2() {
    // Current Address
    $('#current_province').select2({
        theme: 'bootstrap-5',
        placeholder: 'Type to search province...',
        allowClear: true,
        width: '100%'
    });
    
    $('#current_city').select2({
        theme: 'bootstrap-5',
        placeholder: 'Type to search city/municipality...',
        allowClear: true,
        width: '100%'
    });
    
    $('#current_barangay').select2({
        theme: 'bootstrap-5',
        placeholder: 'Type to search barangay...',
        allowClear: true,
        width: '100%'
    });
    
    // Permanent Address
    $('#permanent_province').select2({
        theme: 'bootstrap-5',
        placeholder: 'Type to search province...',
        allowClear: true,
        width: '100%'
    });
    
    $('#permanent_city').select2({
        theme: 'bootstrap-5',
        placeholder: 'Type to search city/municipality...',
        allowClear: true,
        width: '100%'
    });
    
    $('#permanent_barangay').select2({
        theme: 'bootstrap-5',
        placeholder: 'Type to search barangay...',
        allowClear: true,
        width: '100%'
    });
}

// Reinitialize Select2 after updating dropdown options
function reinitializeSelect2(elementId) {
    $(`#${elementId}`).select2('destroy');
    $(`#${elementId}`).select2({
        theme: 'bootstrap-5',
        placeholder: `Type to search ${elementId.includes('province') ? 'province' : elementId.includes('city') ? 'city/municipality' : 'barangay'}...`,
        allowClear: true,
        width: '100%'
    });
}

// Load all Provinces
async function loadProvinces() {
    try {
        const response = await fetch(`${PSGC_API}/provinces/`);
        const provinces = await response.json();
        
        const currentProvinceSelect = document.getElementById('current_province');
        const permanentProvinceSelect = document.getElementById('permanent_province');
        
        provinces.forEach(province => {
            const option1 = new Option(province.name, province.name);
            const option2 = new Option(province.name, province.name);
            option1.dataset.code = province.code;
            option2.dataset.code = province.code;
            currentProvinceSelect.add(option1);
            permanentProvinceSelect.add(option2);
        });
    } catch (error) {
        console.error('Error loading provinces:', error);
    }
}


// Load Cities when Province is selected (Current Address)
document.getElementById('current_province').addEventListener('change', async function() {
    const selectedOption = this.options[this.selectedIndex];
    const provinceCode = selectedOption.dataset.code;
    
    if (!provinceCode) return;
    
    const citySelect = document.getElementById('current_city');
    citySelect.innerHTML = '<option value="">Select Municipality/City</option>';
    citySelect.disabled = true;
    
    // Clear barangay
    const barangaySelect = document.getElementById('current_barangay');
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    barangaySelect.disabled = true;
    
    try {
        const response = await fetch(`${PSGC_API}/provinces/${provinceCode}/cities-municipalities/`);
        const cities = await response.json();
        
        cities.forEach(city => {
            const option = new Option(city.name, city.name);
            option.dataset.code = city.code;
            citySelect.add(option);
        });
        
        citySelect.disabled = false;
        reinitializeSelect2('current_city');
        reinitializeSelect2('current_barangay');
    } catch (error) {
        console.error('Error loading cities:', error);
        alert('Failed to load cities. Please try again.');
    }
});

// Load Barangays when City is selected (Current Address)
document.getElementById('current_city').addEventListener('change', async function() {
    const selectedOption = this.options[this.selectedIndex];
    const cityCode = selectedOption.dataset.code;
    
    if (!cityCode) return;
    
    const barangaySelect = document.getElementById('current_barangay');
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    barangaySelect.disabled = true;
    
    try {
        const response = await fetch(`${PSGC_API}/cities-municipalities/${cityCode}/barangays/`);
        const barangays = await response.json();
        
        barangays.forEach(barangay => {
            const option = new Option(barangay.name, barangay.name);
            barangaySelect.add(option);
        });
        
        barangaySelect.disabled = false;
        reinitializeSelect2('current_barangay');
    } catch (error) {
        console.error('Error loading barangays:', error);
        alert('Failed to load barangays. Please try again.');
    }
});

// Load Cities when Province is selected (Permanent Address)
document.getElementById('permanent_province').addEventListener('change', async function() {
    const selectedOption = this.options[this.selectedIndex];
    const provinceCode = selectedOption.dataset.code;
    
    if (!provinceCode) return;
    
    const citySelect = document.getElementById('permanent_city');
    citySelect.innerHTML = '<option value="">Select Municipality/City</option>';
    citySelect.disabled = true;
    
    // Clear barangay
    const barangaySelect = document.getElementById('permanent_barangay');
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    barangaySelect.disabled = true;
    
    try {
        const response = await fetch(`${PSGC_API}/provinces/${provinceCode}/cities-municipalities/`);
        const cities = await response.json();
        
        cities.forEach(city => {
            const option = new Option(city.name, city.name);
            option.dataset.code = city.code;
            citySelect.add(option);
        });
        
        citySelect.disabled = false;
        reinitializeSelect2('permanent_city');
        reinitializeSelect2('permanent_barangay');
    } catch (error) {
        console.error('Error loading cities:', error);
        alert('Failed to load cities. Please try again.');
    }
});

// Load Barangays when City is selected (Permanent Address)
document.getElementById('permanent_city').addEventListener('change', async function() {
    const selectedOption = this.options[this.selectedIndex];
    const cityCode = selectedOption.dataset.code;
    
    if (!cityCode) return;
    
    const barangaySelect = document.getElementById('permanent_barangay');
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    barangaySelect.disabled = true;
    
    try {
        const response = await fetch(`${PSGC_API}/cities-municipalities/${cityCode}/barangays/`);
        const barangays = await response.json();
        
        barangays.forEach(barangay => {
            const option = new Option(barangay.name, barangay.name);
            barangaySelect.add(option);
        });
        
        barangaySelect.disabled = false;
        reinitializeSelect2('permanent_barangay');
    } catch (error) {
        console.error('Error loading barangays:', error);
        alert('Failed to load barangays. Please try again.');
    }
});

// Update Same Address functionality to work with dropdowns
document.getElementById('same_address').addEventListener('change', function() {
    const permanentFields = document.getElementById('permanent_address_fields');
    
    if (this.checked) {
        permanentFields.style.display = 'none';
        
        // Copy current address to permanent address
        document.getElementById('permanent_house_no').value = document.getElementById('current_house_no').value;
        document.getElementById('permanent_street').value = document.getElementById('current_street').value;
        
        // Copy dropdown values
        const currentProvince = document.getElementById('current_province');
        const permanentProvince = document.getElementById('permanent_province');
        permanentProvince.value = currentProvince.value;
        
        const currentCity = document.getElementById('current_city');
        const permanentCity = document.getElementById('permanent_city');
        permanentCity.value = currentCity.value;
        
        const currentBarangay = document.getElementById('current_barangay');
        const permanentBarangay = document.getElementById('permanent_barangay');
        permanentBarangay.value = currentBarangay.value;
        
        document.getElementById('permanent_country').value = document.getElementById('current_country').value;
        document.getElementById('permanent_zip_code').value = document.getElementById('current_zip_code').value;
    } else {
        permanentFields.style.display = 'block';
    }
});

// Form Validation
document.getElementById('beefForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim() && field.type !== 'checkbox' && field.type !== 'radio') {
            isValid = false;
            field.classList.add('is-invalid');
        } else if ((field.type === 'checkbox' || field.type === 'radio') && !field.checked) {
            const name = field.getAttribute('name');
            const checkedInputs = document.querySelectorAll(`input[name="${name}"]:checked`);
            if (checkedInputs.length === 0) {
                isValid = false;
            }
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill out all required fields.');
        return false;
    }
    
    // Check if terms are agreed
    if (!document.getElementById('agree_terms').checked) {
        e.preventDefault();
        alert('Please agree to the certification before submitting.');
        return false;
    }
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
