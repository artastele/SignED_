<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <?php
            // Define navigation based on role
            $navigation = [];
            
            switch ($data['role'] ?? '') {
                case 'parent':
                    $navigation = [
                        ['icon' => 'house-door', 'text' => 'Dashboard', 'url' => '/parent/dashboard', 'page' => 'dashboard'],
                        ['icon' => 'person-plus', 'text' => 'Enroll Child', 'url' => '/enrollment/beef', 'page' => 'enroll'],
                        ['icon' => 'file-earmark-text', 'text' => 'Requirements', 'url' => '/parent/manageRequirements', 'page' => 'requirements', 'badge' => $data['pending_documents'] ?? 0],
                        ['icon' => 'people', 'text' => 'My Children', 'url' => '/parent/children', 'page' => 'children'],
                    ];
                    break;
                    
                case 'sped_teacher':
                    $navigation = [
                        ['icon' => 'house-door', 'text' => 'Dashboard', 'url' => '/sped/dashboard', 'page' => 'dashboard'],
                        ['icon' => 'check-circle', 'text' => 'Verify Enrollments', 'url' => '/enrollment/verify', 'page' => 'verify', 'badge' => $data['pending_verifications_count'] ?? 0],
                        ['icon' => 'clipboard-check', 'text' => 'Review Assessments', 'url' => '/assessment/review', 'page' => 'assessment', 'badge' => $data['pending_assessments_count'] ?? 0],
                        ['icon' => 'file-earmark-medical', 'text' => 'IEP Management', 'url' => '/iep/list', 'page' => 'iep'],
                        ['icon' => 'folder', 'text' => 'Student Records', 'url' => '/learner/records', 'page' => 'records'],
                        ['icon' => 'book', 'text' => 'Learning Materials', 'url' => '/learner/uploadMaterial', 'page' => 'materials'],
                    ];
                    break;
                    
                case 'guidance':
                    $navigation = [
                        ['icon' => 'house-door', 'text' => 'Dashboard', 'url' => '/guidance/dashboard', 'page' => 'dashboard'],
                        ['icon' => 'calendar-event', 'text' => 'IEP Meetings', 'url' => '/iep/meetings', 'page' => 'meetings', 'badge' => $data['upcoming_meetings_count'] ?? 0],
                        ['icon' => 'folder', 'text' => 'Student Records', 'url' => '/learner/records', 'page' => 'records'],
                    ];
                    break;
                    
                case 'principal':
                    $navigation = [
                        ['icon' => 'house-door', 'text' => 'Dashboard', 'url' => '/principal/dashboard', 'page' => 'dashboard'],
                        ['icon' => 'check-square', 'text' => 'IEP Approvals', 'url' => '/iep/list', 'page' => 'approvals', 'badge' => $data['pending_approvals_count'] ?? 0],
                        ['icon' => 'folder', 'text' => 'Student Records', 'url' => '/learner/records', 'page' => 'records'],
                    ];
                    break;
                    
                case 'learner':
                    $navigation = [
                        ['icon' => 'house-door', 'text' => 'Dashboard', 'url' => '/learner/dashboard', 'page' => 'dashboard'],
                        ['icon' => 'book', 'text' => 'My Materials', 'url' => '/learner/materials', 'page' => 'materials', 'badge' => $data['new_materials'] ?? 0],
                        ['icon' => 'upload', 'text' => 'Submit Work', 'url' => '/learner/submitWork', 'page' => 'submit'],
                        ['icon' => 'graph-up', 'text' => 'My Progress', 'url' => '/learner/trackProgress', 'page' => 'progress'],
                    ];
                    break;
                    
                case 'admin':
                    $navigation = [
                        ['icon' => 'house-door', 'text' => 'Dashboard', 'url' => '/admin/dashboard', 'page' => 'dashboard'],
                        ['icon' => 'people', 'text' => 'Users', 'url' => '/admin/users', 'page' => 'users'],
                        ['icon' => 'megaphone', 'text' => 'Announcements', 'url' => '/admin/announcements', 'page' => 'announcements'],
                        ['icon' => 'gear', 'text' => 'Settings', 'url' => '/admin/settings', 'page' => 'settings'],
                        ['icon' => 'file-text', 'text' => 'Audit Logs', 'url' => '/admin/logs', 'page' => 'logs'],
                        ['icon' => 'shield-lock-fill', 'text' => 'Login Attempts', 'url' => '/admin/loginAttempts', 'page' => 'login_attempts'],
                        ['icon' => 'person-badge-fill', 'text' => 'Admin Activity', 'url' => '/admin/adminActivity', 'page' => 'admin_activity'],
                    ];
                    break;
            }
            
            $currentPage = $data['current_page'] ?? '';
            $hasEnrollments = $data['has_enrollments'] ?? false; // Check if parent has enrollments
            $hasApprovedEnrollment = true; // TEMPORARY: Always allow assessment access for testing
            
            // Check if any enrollment is approved
            // COMMENTED OUT FOR TESTING
            // if (isset($data['enrollments']) && is_array($data['enrollments'])) {
            //     foreach ($data['enrollments'] as $enrollment) {
            //         if ($enrollment->status === 'approved') {
            //             $hasApprovedEnrollment = true;
            //             break;
            //         }
            //     }
            // }
            
            foreach ($navigation as $item):
                $isActive = ($currentPage === $item['page']);
                $activeClass = $isActive ? 'active' : '';
                
                // Disable assessment link for parents without approved enrollments
                $isDisabled = false;
                $disabledTitle = '';
                if ($data['role'] === 'parent' && $item['page'] === 'assessment') {
                    if (!$hasEnrollments) {
                        $isDisabled = true;
                        $disabledTitle = 'Please enroll a child first to access assessments';
                    } elseif (!$hasApprovedEnrollment) {
                        $isDisabled = true;
                        $disabledTitle = 'Assessment will be unlocked after SPED teacher approves your enrollment';
                    }
                }
            ?>
                <li class="nav-item">
                    <?php if ($isDisabled): ?>
                        <a class="nav-link disabled text-muted" href="#" 
                           style="cursor: not-allowed; opacity: 0.6;" 
                           title="<?php echo $disabledTitle; ?>"
                           onclick="event.preventDefault(); alert('<?php echo $disabledTitle; ?>'); return false;">
                            <i class="bi bi-<?php echo $item['icon']; ?> me-2"></i>
                            <?php echo $item['text']; ?>
                            <i class="bi bi-lock-fill ms-1 text-warning"></i>
                        </a>
                    <?php else: ?>
                        <a class="nav-link <?php echo $activeClass; ?>" href="<?php echo URLROOT . $item['url']; ?>">
                            <i class="bi bi-<?php echo $item['icon']; ?> me-2"></i>
                            <?php echo $item['text']; ?>
                            <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                                <span class="badge bg-danger rounded-pill float-end"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <hr class="my-3">
        
        <!-- Bottom Navigation -->
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo URLROOT; ?>/user/profile">
                    <i class="bi bi-person me-2"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?php echo URLROOT; ?>/auth/logout">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
