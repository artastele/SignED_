<?php

class SpedController extends Controller
{
    public function dashboard()
    {
        // Require authentication and check session timeout
        $this->requireLogin();
        $this->checkSessionTimeout();

        // Get current user role
        $role = $this->getCurrentUserRole();
        
        // Ensure user has a SPED-related role
        $spedRoles = ['sped_teacher', 'guidance', 'principal', 'admin'];
        if (!in_array($role, $spedRoles)) {
            die('Access denied: Invalid role for SPED dashboard.');
        }

        // Get dashboard data based on role
        $data = $this->getDashboardData($role);
        
        // Load role-specific dashboard view
        $this->view('sped/dashboard', $data);
    }

    public function statistics()
    {
        // Only admin can access system statistics
        $this->requireAdmin();

        $data = $this->getSystemStatistics();
        
        $this->view('sped/statistics', $data);
    }

    public function navigation()
    {
        // Require authentication
        $this->requireLogin();
        $this->checkSessionTimeout();

        $role = $this->getCurrentUserRole();
        $navigationItems = $this->getNavigationItems($role);
        
        // Return JSON for AJAX requests
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($navigationItems);
            exit;
        }
        
        // Otherwise return view
        $data = ['navigation' => $navigationItems];
        $this->view('sped/navigation', $data);
    }

    /**
     * Get dashboard data based on user role
     */
    private function getDashboardData($role)
    {
        $data = [
            'role' => $role,
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'page_title' => 'SPED Dashboard - SignED',
            'current_page' => 'dashboard',
            'navigation' => $this->getNavigationItems($role)
        ];

        // Load announcements for all roles
        $announcementModel = $this->model('Announcement');
        $data['announcements'] = $announcementModel->getForUser($role);

        switch ($role) {
            case 'sped_teacher':
                $data = array_merge($data, $this->getSpedTeacherData());
                break;
            case 'guidance':
                $data = array_merge($data, $this->getGuidanceData());
                break;
            case 'principal':
                $data = array_merge($data, $this->getPrincipalData());
                break;
            case 'admin':
                $data = array_merge($data, $this->getAdminData());
                break;
        }

        return $data;
    }

    /**
     * Get SPED teacher dashboard data
     */
    private function getSpedTeacherData()
    {
        $enrollmentModel = $this->model('Enrollment');
        $learnerModel = $this->model('Learner');
        $iepModel = $this->model('Iep');

        $pendingVerifications = $enrollmentModel->getByStatus('pending_verification');
        $pendingAssessments = $learnerModel->getByStatus('assessment_pending');
        $activeIeps = $iepModel->getByStatus('approved');

        return [
            'pending_verifications' => $pendingVerifications,
            'pending_assessments' => $pendingAssessments,
            'active_ieps' => $activeIeps,
            'recent_submissions' => $this->getRecentSubmissions(),
            // Badge counts for sidebar
            'pending_verifications_count' => count($pendingVerifications),
            'pending_assessments_count' => count($pendingAssessments)
        ];
    }

    /**
     * Get guidance counselor dashboard data
     */
    private function getGuidanceData()
    {
        $meetingModel = $this->model('IepMeeting');
        
        $scheduledMeetings = $meetingModel->getByStatus('scheduled');
        $confirmedMeetings = $meetingModel->getByStatus('confirmed');
        $upcomingMeetings = $meetingModel->getUpcoming();
        
        return [
            'scheduled_meetings' => $scheduledMeetings,
            'confirmed_meetings' => $confirmedMeetings,
            'upcoming_meetings' => $upcomingMeetings,
            // Badge count for sidebar
            'upcoming_meetings_count' => count($upcomingMeetings)
        ];
    }

    /**
     * Get principal dashboard data
     */
    private function getPrincipalData()
    {
        $iepModel = $this->model('Iep');
        
        $pendingApprovals = $iepModel->getByStatus('pending_approval');
        $recentApprovals = $iepModel->getRecentApprovals();
        
        return [
            'pending_approvals' => $pendingApprovals,
            'recent_approvals' => $recentApprovals,
            'rejected_ieps' => $iepModel->getByStatus('rejected'),
            // Badge count for sidebar
            'pending_approvals_count' => count($pendingApprovals)
        ];
    }

    /**
     * Get admin dashboard data
     */
    private function getAdminData()
    {
        return array_merge(
            $this->getSystemStatistics(),
            $this->getSpedTeacherData(),
            $this->getGuidanceData(),
            $this->getPrincipalData()
        );
    }

    /**
     * Get system statistics for admin dashboard
     */
    private function getSystemStatistics()
    {
        $enrollmentModel = $this->model('Enrollment');
        $learnerModel = $this->model('Learner');
        $iepModel = $this->model('Iep');
        $userModel = $this->model('User');

        return [
            'total_enrollments' => $enrollmentModel->getTotalCount(),
            'pending_verifications' => count($enrollmentModel->getByStatus('pending_verification')),
            'active_learners' => count($learnerModel->getByStatus('active')),
            'active_ieps' => count($iepModel->getByStatus('approved')),
            'total_users' => $userModel->getSpedUserCount(),
            'recent_activity' => $this->getRecentActivity()
        ];
    }

    /**
     * Get navigation items based on user role
     */
    private function getNavigationItems($role)
    {
        $baseItems = [
            [
                'title' => 'Dashboard',
                'url' => URLROOT . '/sped/dashboard',
                'icon' => 'dashboard',
                'active' => true
            ]
        ];

        $roleSpecificItems = [];

        switch ($role) {
            case 'sped_teacher':
                $roleSpecificItems = [
                    [
                        'title' => 'Enrollment Verification',
                        'url' => URLROOT . '/enrollment/verify',
                        'icon' => 'verification',
                        'badge' => $this->getPendingVerificationCount()
                    ],
                    [
                        'title' => 'Assessments',
                        'url' => URLROOT . '/assessment/list',
                        'icon' => 'assessment',
                        'badge' => $this->getPendingAssessmentCount()
                    ],
                    [
                        'title' => 'IEP Management',
                        'url' => URLROOT . '/iep/list',
                        'icon' => 'iep',
                        'submenu' => [
                            ['title' => 'Create IEP', 'url' => URLROOT . '/iep/create'],
                            ['title' => 'Schedule Meeting', 'url' => URLROOT . '/iep/schedule-meeting'],
                            ['title' => 'View All IEPs', 'url' => URLROOT . '/iep/list']
                        ]
                    ],
                    [
                        'title' => 'Learning Materials',
                        'url' => URLROOT . '/learner/materials',
                        'icon' => 'materials'
                    ]
                ];
                break;

            case 'guidance':
                $roleSpecificItems = [
                    [
                        'title' => 'IEP Meetings',
                        'url' => URLROOT . '/iep/meetings',
                        'icon' => 'meetings',
                        'badge' => $this->getScheduledMeetingCount()
                    ],
                    [
                        'title' => 'Learner Progress',
                        'url' => URLROOT . '/learner/progress',
                        'icon' => 'progress'
                    ]
                ];
                break;

            case 'principal':
                $roleSpecificItems = [
                    [
                        'title' => 'IEP Approvals',
                        'url' => URLROOT . '/iep/approve',
                        'icon' => 'approval',
                        'badge' => $this->getPendingApprovalCount()
                    ],
                    [
                        'title' => 'System Reports',
                        'url' => URLROOT . '/sped/reports',
                        'icon' => 'reports'
                    ]
                ];
                break;

            case 'admin':
                $roleSpecificItems = [
                    [
                        'title' => 'User Management',
                        'url' => URLROOT . '/admin/users',
                        'icon' => 'users'
                    ],
                    [
                        'title' => 'System Statistics',
                        'url' => URLROOT . '/sped/statistics',
                        'icon' => 'statistics'
                    ],
                    [
                        'title' => 'Audit Logs',
                        'url' => URLROOT . '/admin/audit-logs',
                        'icon' => 'audit'
                    ],
                    [
                        'title' => 'All Enrollments',
                        'url' => URLROOT . '/enrollment/all',
                        'icon' => 'enrollments'
                    ],
                    [
                        'title' => 'All IEPs',
                        'url' => URLROOT . '/iep/all',
                        'icon' => 'iep'
                    ]
                ];
                break;
        }

        return array_merge($baseItems, $roleSpecificItems, [
            [
                'title' => 'Profile',
                'url' => URLROOT . '/user/profile',
                'icon' => 'profile'
            ],
            [
                'title' => 'Logout',
                'url' => URLROOT . '/auth/logout',
                'icon' => 'logout',
                'class' => 'logout-item'
            ]
        ]);
    }

    /**
     * Helper methods to get counts for navigation badges
     */
    private function getPendingVerificationCount()
    {
        $enrollmentModel = $this->model('Enrollment');
        return count($enrollmentModel->getByStatus('pending_verification'));
    }

    private function getPendingAssessmentCount()
    {
        $learnerModel = $this->model('Learner');
        return count($learnerModel->getByStatus('assessment_pending'));
    }

    private function getScheduledMeetingCount()
    {
        $meetingModel = $this->model('IepMeeting');
        return count($meetingModel->getByStatus('scheduled'));
    }

    private function getPendingApprovalCount()
    {
        $iepModel = $this->model('Iep');
        return count($iepModel->getByStatus('pending_approval'));
    }

    /**
     * Get recent submissions for SPED teacher dashboard
     */
    private function getRecentSubmissions()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT ls.*, lm.title as material_title, l.first_name, l.last_name
                FROM learner_submissions ls
                JOIN learning_materials lm ON ls.material_id = lm.id
                JOIN learners l ON ls.learner_id = l.id
                ORDER BY ls.submitted_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log("Error fetching recent submissions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent system activity for admin dashboard
     */
    private function getRecentActivity()
    {
        try {
            $auditModel = $this->model('AuditLog');
            return $auditModel->getRecentActivity(10);
        } catch (Exception $e) {
            error_log("Error fetching recent activity: " . $e->getMessage());
            return [];
        }
    }
}