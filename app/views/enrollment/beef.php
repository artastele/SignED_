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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p class="text-muted mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Fill in all required information about the learner. Fields marked with <span class="text-danger">*</span> are required.
            </p>

            <form action="<?php echo URLROOT; ?>/enrollment/beef" method="POST" id="beefForm">
                
                <!-- Student Type Selection -->
                <div class="mb-4">
                    <h5 class="mb-3">Student Type <span class="text-danger">*</span></h5>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="student_type" id="type_new" value="new" checked>
                        <label class="btn btn-outline-primary" for="type_new">
                            <i class="bi bi-person-plus me-2"></i>New Student
                        </label>

                        <input type="radio" class="btn-check" name="student_type" id="type_old" value="old">
                        <label class="btn btn-outline-primary" for="type_old">
                            <i class="bi bi-arrow-repeat me-2"></i>Old Student (Balik-Aral)
                        </label>

                        <input type="radio" class="btn-check" name="student_type" id="type_transfer" value="transfer">
                        <label class="btn btn-outline-primary" for="type_transfer">
                            <i class="bi bi-arrow-left-right me-2"></i>Transfer Student
                        </label>
                    </div>
                </div>

                <!-- School Year and Grade Level -->
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">School Year <span class="text-danger">*</span></label>
                            <select class="form-select" name="school_year" required>
                                <option value="">Select School Year</option>
                                <option value="2024-2025" selected>2024-2025</option>
                                <option value="2025-2026">2025-2026</option>
                                <option value="2026-2027">2026-2027</option>
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
                                <option value="SPED">SPED</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Old Student LRN Lookup Section -->
                <div class="mb-4" id="oldStudentSection" style="display: none;">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-search me-2"></i>Find Your Previous Record
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="small mb-3">
                                Enter your LRN (Learner Reference Number) or full name to retrieve your information
                            </p>
                            <div class="row g-2 mb-3">
                                <div class="col-md-9">
                                    <label class="form-label">LRN or Full name of the student</label>
                                    <input type="text" class="form-control" id="lrn_lookup" placeholder="Enter 12-digit LRN or full name">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" onclick="lookupLRN()">
                                        <i class="bi bi-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="hideOldStudentLookup()">
                                    Can't Find? Enter Manually
                                </button>
                            </div>
                            <!-- Success Message (hidden by default) -->
                            <div class="alert alert-success mt-3" id="lrnSuccessMessage" style="display: none;">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Student Record Found!</strong><br>
                                <span id="welcomeMessage"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transfer Student Previous School Section -->
                <div class="mb-4" id="transferStudentSection" style="display: none;">
                    <div class="card border-warning">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0">
                                <i class="bi bi-arrow-left-right me-2"></i>Returning Learner (Balik-Aral) and Those Who will Transfer/Move In
                            </h6>
                        </div>
                        <div class="card-body">
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
                                <div class="col-md-8">
                                    <label class="form-label">Last School Attended <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="last_school_attended" id="last_school_attended" placeholder="Enter complete school name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">School ID</label>
                                    <input type="text" class="form-control" name="last_school_id" id="last_school_id" placeholder="School ID (if available)">
                                </div>
                            </div>
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
                            <input type="text" class="form-control" name="psa_birth_cert" id="psa_birth_cert">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Learner Reference No. (LRN)</label>
                            <input type="text" class="form-control" name="lrn" id="lrn" placeholder="12-digit learner reference number" maxlength="12">
                            <div class="form-text">12-digit learner reference number</div>
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
                            <label class="form-label">Ext. Name (Jr., Sr., III)</label>
                            <input type="text" class="form-control" name="extension_name" id="extension_name" placeholder="e.g., Jr., III">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mother Tongue <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mother_tongue" id="mother_tongue" required placeholder="e.g., Cebuano">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date_of_birth" id="birthdate" required>
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
                                    <input class="form-check-input" type="radio" name="gender" id="sex_male" value="Male" required>
                                    <label class="form-check-label" for="sex_male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="sex_female" value="Female" required>
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

                <!-- Page Navigation Buttons -->
                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                        <i class="bi bi-save me-1"></i>Save Draft
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goToPage2()">
                        Next <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Page 2 - Hidden by default, will be shown when Next is clicked -->
    <div class="card shadow-sm mb-4" id="page2" style="display: none;">
        <div class="card-body">
        <form action="<?php echo URLROOT; ?>/enrollment/beef" method="POST" id="beefFormPage2">
            <?php include '../app/views/partials/csrf_token.php'; ?>
            
            <!-- Hidden fields from Page 1 -->
            <input type="hidden" name="student_type" id="hidden_student_type">
            <input type="hidden" name="school_year" id="hidden_school_year">
            <input type="hidden" name="grade_level" id="hidden_grade_level">
            <input type="hidden" name="psa_birth_cert" id="hidden_psa_birth_cert">
            <input type="hidden" name="lrn" id="hidden_lrn">
            <input type="hidden" name="last_name" id="hidden_last_name">
            <input type="hidden" name="first_name" id="hidden_first_name">
            <input type="hidden" name="middle_name" id="hidden_middle_name">
            <input type="hidden" name="extension_name" id="hidden_extension_name">
            <input type="hidden" name="mother_tongue" id="hidden_mother_tongue">
            <input type="hidden" name="date_of_birth" id="hidden_date_of_birth">
            <input type="hidden" name="age" id="hidden_age">
            <input type="hidden" name="gender" id="hidden_gender">
            <input type="hidden" name="place_of_birth" id="hidden_place_of_birth">
            <input type="hidden" name="is_indigenous" id="hidden_is_indigenous">
            <input type="hidden" name="indigenous_specify" id="hidden_indigenous_specify">
            <input type="hidden" name="is_4ps" id="hidden_is_4ps">
            <input type="hidden" name="4ps_household_id" id="hidden_4ps_household_id">
            <input type="hidden" name="is_disabled" id="hidden_is_disabled">
            <input type="hidden" name="disability_types" id="hidden_disability_types">
            <input type="hidden" name="last_grade_completed" id="hidden_last_grade_completed">
            <input type="hidden" name="last_school_year" id="hidden_last_school_year">
            <input type="hidden" name="last_school_attended" id="hidden_last_school_attended">
            <input type="hidden" name="last_school_id" id="hidden_last_school_id">
            
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
                        <label class="form-label">Sitio/Street Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="current_street" id="current_street" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Barangay <span class="text-danger">*</span></label>
                        <select class="form-select" name="current_barangay" id="current_barangay" required disabled>
                            <option value="">Select City First</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Municipality/City <span class="text-danger">*</span></label>
                        <select class="form-select" name="current_city" id="current_city" required disabled>
                            <option value="">Select Province First</option>
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
                    <input class="form-check-input" type="radio" name="same_address_option" id="same_address_yes" value="yes">
                    <label class="form-check-label" for="same_address_yes">
                        Same with your Current Address?
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="same_address_option" id="same_address_no" value="no" checked>
                    <label class="form-check-label" for="same_address_no">
                        Different Address
                    </label>
                </div>

                <div id="permanent_address_fields">
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label">House No.</label>
                            <input type="text" class="form-control" name="permanent_house_no" id="permanent_house_no">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Sitio/Street Name</label>
                            <input type="text" class="form-control" name="permanent_street" id="permanent_street">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Barangay</label>
                            <select class="form-select" name="permanent_barangay" id="permanent_barangay" disabled>
                                <option value="">Select City First</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Municipality/City</label>
                            <select class="form-select" name="permanent_city" id="permanent_city" disabled>
                                <option value="">Select Province First</option>
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
                    <i class="bi bi-people me-2 text-primary"></i>Parent's/Guardian's Information <span class="text-danger">*</span>
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

                <div class="row g-3">
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
            </div>

            <!-- Distance Learning Modalities -->
            <div class="mb-4">
                <h5 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-laptop me-2 text-primary"></i>Distance Learning Modalities
                </h5>
                <p class="small text-muted mb-3">
                    If school will implement other distance learning modalities aside from face-to-face instruction, what would you prefer for your child? (Choose all that apply)
                </p>
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

            <!-- Data Privacy and Certification -->
            <div class="mb-4">
                <!-- Data Privacy Information Box -->
                <div class="alert alert-info">
                    <h6 class="mb-2">
                        <i class="bi bi-shield-check me-2"></i>Data Privacy Notice (RA 10173)
                    </h6>
                    <p class="small mb-0">
                        The information provided in this form will be used to create and maintain your child's learner record in the Department of Education's Learner Information System. All personal data will be kept confidential and secure in compliance with the Data Privacy Act of 2012. You have the right to access, correct, and request deletion of your child's data. For concerns, contact the School Data Protection Officer.
                    </p>
                </div>

                <!-- Certification -->
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-file-earmark-check me-2"></i>Parental Certification
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            I hereby certify that:
                        </p>
                        <ul class="small mb-3">
                            <li>All information provided in this enrollment form is <strong>true, accurate, and complete</strong></li>
                            <li>I consent to DepEd's use of my child's information for enrollment and educational purposes</li>
                            <li>I understand that providing false information may result in cancellation of enrollment</li>
                            <li>I will inform the school of any changes to the information provided</li>
                        </ul>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                <strong>I have read and agree to the above certification</strong> 
                                <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex gap-2 justify-content-between border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" onclick="goToPage1()">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                        <i class="bi bi-save me-1"></i>Save Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Submit
                    </button>
                </div>
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

<script>
// ========================================
// PAGE NAVIGATION
// ========================================
function goToPage2() {
    // Validate Page 1 required fields
    const form = document.getElementById('beefForm');
    let isValid = true;
    let missingFields = [];
    
    // Check text inputs and selects
    const textFields = form.querySelectorAll('input[required]:not([type="radio"]):not([type="checkbox"]), select[required]');
    textFields.forEach(field => {
        if (!field.value || field.value.trim() === '') {
            isValid = false;
            field.classList.add('is-invalid');
            missingFields.push(field.previousElementSibling?.textContent || field.name);
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Check radio button groups
    const radioGroups = {};
    form.querySelectorAll('input[type="radio"][required]').forEach(radio => {
        const name = radio.getAttribute('name');
        if (!radioGroups[name]) {
            radioGroups[name] = false;
        }
        if (radio.checked) {
            radioGroups[name] = true;
        }
    });
    
    for (const [groupName, isChecked] of Object.entries(radioGroups)) {
        if (!isChecked) {
            isValid = false;
            missingFields.push(groupName.replace('_', ' '));
        }
    }
    
    if (!isValid) {
        console.log('Missing fields:', missingFields);
        alert('Please fill out all required fields on this page:\n\n' + missingFields.join('\n'));
        return false;
    }
    
    // Copy all Page 1 data to hidden fields in Page 2
    document.getElementById('hidden_student_type').value = document.querySelector('input[name="student_type"]:checked')?.value || '';
    document.getElementById('hidden_school_year').value = document.querySelector('select[name="school_year"]').value;
    document.getElementById('hidden_grade_level').value = document.getElementById('grade_level').value;
    document.getElementById('hidden_psa_birth_cert').value = document.getElementById('psa_birth_cert').value;
    document.getElementById('hidden_lrn').value = document.getElementById('lrn').value;
    document.getElementById('hidden_last_name').value = document.getElementById('last_name').value;
    document.getElementById('hidden_first_name').value = document.getElementById('first_name').value;
    document.getElementById('hidden_middle_name').value = document.getElementById('middle_name').value;
    document.getElementById('hidden_extension_name').value = document.getElementById('extension_name').value;
    document.getElementById('hidden_mother_tongue').value = document.getElementById('mother_tongue').value;
    document.getElementById('hidden_date_of_birth').value = document.getElementById('birthdate').value;
    document.getElementById('hidden_age').value = document.getElementById('age').value;
    document.getElementById('hidden_gender').value = document.querySelector('input[name="gender"]:checked')?.value || '';
    document.getElementById('hidden_place_of_birth').value = document.getElementById('place_of_birth').value;
    document.getElementById('hidden_is_indigenous').value = document.querySelector('input[name="is_indigenous"]:checked')?.value || '';
    document.getElementById('hidden_indigenous_specify').value = document.getElementById('indigenous_specify').value;
    document.getElementById('hidden_is_4ps').value = document.querySelector('input[name="is_4ps"]:checked')?.value || '';
    document.getElementById('hidden_4ps_household_id').value = document.getElementById('4ps_household_id').value;
    document.getElementById('hidden_is_disabled').value = document.querySelector('input[name="is_disabled"]:checked')?.value || '';
    
    // Collect disability types
    const disabilityTypes = [];
    document.querySelectorAll('input[name="disability[]"]:checked').forEach(cb => {
        disabilityTypes.push(cb.value);
    });
    document.getElementById('hidden_disability_types').value = disabilityTypes.join(', ');
    
    // Transfer student fields
    document.getElementById('hidden_last_grade_completed').value = document.getElementById('last_grade_completed')?.value || '';
    document.getElementById('hidden_last_school_year').value = document.getElementById('last_school_year')?.value || '';
    document.getElementById('hidden_last_school_attended').value = document.getElementById('last_school_attended')?.value || '';
    document.getElementById('hidden_last_school_id').value = document.getElementById('last_school_id')?.value || '';
    
    // Hide Page 1, Show Page 2
    document.querySelector('.card.shadow-sm.mb-4').style.display = 'none';
    document.getElementById('page2').style.display = 'block';
    
    // Scroll to top
    window.scrollTo(0, 0);
}

function goToPage1() {
    // Hide Page 2, Show Page 1
    document.getElementById('page2').style.display = 'none';
    document.querySelector('.card.shadow-sm.mb-4').style.display = 'block';
    
    // Scroll to top
    window.scrollTo(0, 0);
}

// ========================================
// STUDENT TYPE SELECTION
// ========================================
document.querySelectorAll('input[name="student_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const oldStudentSection = document.getElementById('oldStudentSection');
        const transferStudentSection = document.getElementById('transferStudentSection');
        
        // Reset all sections first
        oldStudentSection.style.display = 'none';
        transferStudentSection.style.display = 'none';
        
        // Reset required fields
        document.getElementById('last_grade_completed').required = false;
        document.getElementById('last_school_year').required = false;
        document.getElementById('last_school_attended').required = false;
        
        if (this.value === 'old') {
            // Old/Returning student - show LRN lookup
            oldStudentSection.style.display = 'block';
        } else if (this.value === 'transfer') {
            // Transfer student - show transfer info
            transferStudentSection.style.display = 'block';
            
            // Make transfer fields required
            document.getElementById('last_grade_completed').required = true;
            document.getElementById('last_school_year').required = true;
            document.getElementById('last_school_attended').required = true;
        }
    });
});

// ========================================
// OLD STUDENT - LRN LOOKUP
// ========================================
function lookupLRN() {
    const searchInput = document.getElementById('lrn_lookup').value.trim();
    
    if (searchInput.length === 0) {
        alert('Please enter an LRN or full name to search');
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
        body: 'lrn=' + encodeURIComponent(searchInput)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const successMsg = document.getElementById('lrnSuccessMessage');
            const welcomeMsg = document.getElementById('welcomeMessage');
            welcomeMsg.textContent = `Welcome back, ${data.learner.first_name}! Your information from Grade ${data.learner.last_grade_completed || 'previous enrollment'} has been loaded. Please review and update any information if needed.`;
            successMsg.style.display = 'block';
            
            // Auto-fill form with learner data
            const learner = data.learner;
            
            // Basic Information
            if (learner.lrn) document.getElementById('lrn').value = learner.lrn;
            if (learner.psa_birth_cert) document.getElementById('psa_birth_cert').value = learner.psa_birth_cert;
            if (learner.last_name) document.getElementById('last_name').value = learner.last_name;
            if (learner.first_name) document.getElementById('first_name').value = learner.first_name;
            if (learner.middle_name) document.getElementById('middle_name').value = learner.middle_name;
            if (learner.extension_name) document.getElementById('extension_name').value = learner.extension_name;
            if (learner.date_of_birth) document.getElementById('birthdate').value = learner.date_of_birth;
            if (learner.mother_tongue) document.getElementById('mother_tongue').value = learner.mother_tongue;
            if (learner.place_of_birth) document.getElementById('place_of_birth').value = learner.place_of_birth;
            
            // Gender
            if (learner.gender === 'Male') {
                document.getElementById('sex_male').checked = true;
            } else if (learner.gender === 'Female') {
                document.getElementById('sex_female').checked = true;
            }
            
            // Trigger age calculation
            if (learner.date_of_birth) {
                document.getElementById('birthdate').dispatchEvent(new Event('change'));
            }
            
            // If previous enrollment data exists, fill additional fields
            if (data.previous_data) {
                const prevData = data.previous_data;
                
                // Address fields (will be filled on page 2)
                // Parent information (will be filled on page 2)
            }
            
            alert('Learner information loaded successfully! Please review and update any necessary fields.');
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while looking up the record. Please try again.');
    })
    .finally(() => {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function hideOldStudentLookup() {
    document.getElementById('oldStudentSection').style.display = 'none';
}

// ========================================
// GRADE LEVEL - SHOW/HIDE SHS SECTION
// ========================================
document.getElementById('grade_level').addEventListener('change', function() {
    const shsSection = document.getElementById('shs_section');
    if (this.value === '11' || this.value === '12') {
        shsSection.style.display = 'block';
    } else {
        shsSection.style.display = 'none';
    }
});

// ========================================
// AGE CALCULATION
// ========================================
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

// ========================================
// INDIGENOUS PEOPLES - SHOW/HIDE SPECIFY
// ========================================
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

// ========================================
// 4PS - SHOW/HIDE HOUSEHOLD ID
// ========================================
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

// ========================================
// DISABILITY - SHOW/HIDE TYPES
// ========================================
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

// ========================================
// SAME ADDRESS FUNCTIONALITY
// ========================================
document.querySelectorAll('input[name="same_address_option"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const permanentFields = document.getElementById('permanent_address_fields');
        
        if (this.value === 'yes') {
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
});

// ========================================
// SAVE DRAFT FUNCTION
// ========================================
function saveDraft() {
    alert('Draft save feature will be implemented. Your progress will be saved.');
}

// ========================================
// AUTO-FORMAT NAME FIELDS
// ========================================
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
const PSGC_API = 'https://psgc.gitlab.io/api';

// Load Provinces on Page Load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Loading provinces...');
    loadProvinces();
});

// Load all Provinces
async function loadProvinces() {
    try {
        console.log('Fetching provinces from API...');
        const response = await fetch(`${PSGC_API}/provinces/`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const provinces = await response.json();
        console.log('Provinces loaded:', provinces.length);
        
        const currentProvinceSelect = document.getElementById('current_province');
        const permanentProvinceSelect = document.getElementById('permanent_province');
        
        // Clear existing options except the first one
        currentProvinceSelect.innerHTML = '<option value="">Select Province</option>';
        permanentProvinceSelect.innerHTML = '<option value="">Select Province</option>';
        
        provinces.forEach(province => {
            const option1 = new Option(province.name, province.name);
            const option2 = new Option(province.name, province.name);
            option1.dataset.code = province.code;
            option2.dataset.code = province.code;
            currentProvinceSelect.add(option1);
            permanentProvinceSelect.add(option2);
        });
        
        console.log('Provinces populated successfully');
    } catch (error) {
        console.error('Error loading provinces:', error);
        alert('Failed to load provinces. Please refresh the page or contact support if the issue persists.');
    }
}

// Load Cities when Province is selected (Current Address)
document.getElementById('current_province').addEventListener('change', async function() {
    console.log('Province changed:', this.value);
    
    const selectedOption = this.options[this.selectedIndex];
    const provinceCode = selectedOption.dataset.code;
    
    const citySelect = document.getElementById('current_city');
    const barangaySelect = document.getElementById('current_barangay');
    
    // Reset city and barangay
    citySelect.innerHTML = '<option value="">Select Municipality/City</option>';
    barangaySelect.innerHTML = '<option value="">Select City First</option>';
    barangaySelect.disabled = true;
    
    if (!provinceCode) {
        citySelect.disabled = true;
        return;
    }
    
    // Show loading
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    citySelect.disabled = true;
    
    try {
        console.log('Fetching cities for province code:', provinceCode);
        const response = await fetch(`${PSGC_API}/provinces/${provinceCode}/cities-municipalities/`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const cities = await response.json();
        console.log('Cities loaded:', cities.length);
        
        // Clear and populate cities
        citySelect.innerHTML = '<option value="">Select Municipality/City</option>';
        cities.forEach(city => {
            const option = new Option(city.name, city.name);
            option.dataset.code = city.code;
            citySelect.add(option);
        });
        
        // Enable the dropdown
        citySelect.disabled = false;
        console.log('Cities populated successfully');
        
    } catch (error) {
        console.error('Error loading cities:', error);
        citySelect.innerHTML = '<option value="">Error loading cities - Please try again</option>';
        citySelect.disabled = false;
        alert('Failed to load cities. Please try selecting the province again.');
    }
});

// Load Barangays when City is selected (Current Address)
document.getElementById('current_city').addEventListener('change', async function() {
    console.log('City changed:', this.value);
    
    const selectedOption = this.options[this.selectedIndex];
    const cityCode = selectedOption.dataset.code;
    
    const barangaySelect = document.getElementById('current_barangay');
    
    // Reset barangay
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (!cityCode) {
        barangaySelect.disabled = true;
        return;
    }
    
    // Show loading
    barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
    barangaySelect.disabled = true;
    
    try {
        console.log('Fetching barangays for city code:', cityCode);
        const response = await fetch(`${PSGC_API}/cities-municipalities/${cityCode}/barangays/`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const barangays = await response.json();
        console.log('Barangays loaded:', barangays.length);
        
        // Clear and populate barangays
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        barangays.forEach(barangay => {
            const option = new Option(barangay.name, barangay.name);
            barangaySelect.add(option);
        });
        
        // Enable the dropdown
        barangaySelect.disabled = false;
        console.log('Barangays populated successfully');
        
    } catch (error) {
        console.error('Error loading barangays:', error);
        barangaySelect.innerHTML = '<option value="">Error loading barangays - Please try again</option>';
        barangaySelect.disabled = false;
        alert('Failed to load barangays. Please try selecting the city again.');
    }
});

// Load Cities when Province is selected (Permanent Address)
document.getElementById('permanent_province').addEventListener('change', async function() {
    console.log('Permanent Province changed:', this.value);
    
    const selectedOption = this.options[this.selectedIndex];
    const provinceCode = selectedOption.dataset.code;
    
    const citySelect = document.getElementById('permanent_city');
    const barangaySelect = document.getElementById('permanent_barangay');
    
    // Reset city and barangay
    citySelect.innerHTML = '<option value="">Select Municipality/City</option>';
    barangaySelect.innerHTML = '<option value="">Select City First</option>';
    barangaySelect.disabled = true;
    
    if (!provinceCode) {
        citySelect.disabled = true;
        return;
    }
    
    // Show loading
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    citySelect.disabled = true;
    
    try {
        console.log('Fetching cities for permanent province code:', provinceCode);
        const response = await fetch(`${PSGC_API}/provinces/${provinceCode}/cities-municipalities/`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const cities = await response.json();
        console.log('Permanent cities loaded:', cities.length);
        
        // Clear and populate cities
        citySelect.innerHTML = '<option value="">Select Municipality/City</option>';
        cities.forEach(city => {
            const option = new Option(city.name, city.name);
            option.dataset.code = city.code;
            citySelect.add(option);
        });
        
        // Enable the dropdown
        citySelect.disabled = false;
        console.log('Permanent cities populated successfully');
        
    } catch (error) {
        console.error('Error loading permanent cities:', error);
        citySelect.innerHTML = '<option value="">Error loading cities - Please try again</option>';
        citySelect.disabled = false;
        alert('Failed to load cities. Please try selecting the province again.');
    }
});

// Load Barangays when City is selected (Permanent Address)
document.getElementById('permanent_city').addEventListener('change', async function() {
    console.log('Permanent City changed:', this.value);
    
    const selectedOption = this.options[this.selectedIndex];
    const cityCode = selectedOption.dataset.code;
    
    const barangaySelect = document.getElementById('permanent_barangay');
    
    // Reset barangay
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (!cityCode) {
        barangaySelect.disabled = true;
        return;
    }
    
    // Show loading
    barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
    barangaySelect.disabled = true;
    
    try {
        console.log('Fetching barangays for permanent city code:', cityCode);
        const response = await fetch(`${PSGC_API}/cities-municipalities/${cityCode}/barangays/`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const barangays = await response.json();
        console.log('Permanent barangays loaded:', barangays.length);
        
        // Clear and populate barangays
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        barangays.forEach(barangay => {
            const option = new Option(barangay.name, barangay.name);
            barangaySelect.add(option);
        });
        
        // Enable the dropdown
        barangaySelect.disabled = false;
        console.log('Permanent barangays populated successfully');
        
    } catch (error) {
        console.error('Error loading permanent barangays:', error);
        barangaySelect.innerHTML = '<option value="">Error loading barangays - Please try again</option>';
        barangaySelect.disabled = false;
        alert('Failed to load barangays. Please try selecting the city again.');
    }
});

// ========================================
// FORM SUBMISSION
// ========================================
document.getElementById('beefFormPage2').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
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
        alert('Please fill out all required fields.');
        return false;
    }
    
    // Check if terms are agreed
    if (!document.getElementById('agree_terms').checked) {
        alert('Please agree to the certification before submitting.');
        return false;
    }
    
    // Submit the form
    this.submit();
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
