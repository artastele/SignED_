<?php

class ParentController extends Controller
{
    private $notificationService;
    private $enrollmentModel;
    private $learnerModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = $this->model('NotificationService');
        $this->enrollmentModel = $this->model('Enrollment');
        $this->learnerModel = $this->model('Learner');
    }

    public function dashboard()
    {
        $this->requireLogin();
        $this->requireRole('parent');

        $parentId = $_SESSION['user_id'];

        // Get announcements for parents
        $announcementModel = $this->model('Announcement');
        $announcements = $announcementModel->getForUser('parent');

        // Get parent's enrollments
        $enrollments = $this->enrollmentModel->getByParent($parentId);

        // Get parent's learners (enrolled children)
        $learners = $this->learnerModel->getByParent($parentId);

        // Get unread notifications count
        $unreadCount = $this->notificationService->getUnreadCount($parentId);

        $data = [
            'role' => $_SESSION['role'] ?? 'parent',
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'current_page' => 'dashboard',
            'page_title' => 'Parent Dashboard - SignED SPED',
            'announcements' => $announcements,
            'enrollments' => $enrollments,
            'learners' => $learners,
            'unread_notifications' => $unreadCount,
            'has_enrollments' => count($enrollments) > 0
        ];

        $this->view('parent/dashboard', $data);
    }

    /**
     * Show enrollment checklist and start enrollment
     */
    public function enrollChild()
    {
        $this->requireLogin();
        $this->requireRole('parent');

        // Redirect to BEEF form
        header('Location: ' . URLROOT . '/enrollment/beef');
        exit;
    }

    /**
     * Manage requirements (view uploaded documents)
     */
    public function manageRequirements()
    {
        $this->requireLogin();
        $this->requireRole('parent');

        $parentId = $_SESSION['user_id'];
        $enrollments = $this->enrollmentModel->getByParent($parentId);

        // Count pending documents
        $pendingDocs = 0;
        foreach ($enrollments as $enrollment) {
            if ($enrollment->status === 'pending_documents') {
                $pendingDocs++;
            }
        }

        $data = [
            'role' => $_SESSION['role'] ?? 'parent',
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'current_page' => 'requirements',
            'page_title' => 'Manage Requirements - SignED SPED',
            'enrollments' => $enrollments,
            'pending_documents' => $pendingDocs,
            'unread_notifications' => $this->notificationService->getUnreadCount($parentId)
        ];

        $this->view('parent/manage_requirements', $data);
    }

    /**
     * View children (enrolled learners)
     */
    public function children()
    {
        $this->requireLogin();
        $this->requireRole('parent');

        $parentId = $_SESSION['user_id'];
        $learners = $this->learnerModel->getByParent($parentId);

        $data = [
            'role' => $_SESSION['role'] ?? 'parent',
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'current_page' => 'children',
            'page_title' => 'My Children - SignED SPED',
            'learners' => $learners,
            'unread_notifications' => $this->notificationService->getUnreadCount($parentId)
        ];

        $this->view('parent/children', $data);
    }

    /**
     * View learner details
     */
    public function viewLearner($learnerId = null)
    {
        $this->requireLogin();
        $this->requireRole('parent');

        if (!$learnerId) {
            header('Location: ' . URLROOT . '/parent/children');
            exit;
        }

        $parentId = $_SESSION['user_id'];
        
        // Get learner details
        $learner = $this->learnerModel->getById($learnerId);
        
        // Verify learner belongs to current parent
        if (!$learner || $learner->parent_id != $parentId) {
            header('Location: ' . URLROOT . '/parent/children?error=Access denied');
            exit;
        }

        // Get enrollment details
        $enrollmentModel = $this->model('Enrollment');
        $enrollment = $enrollmentModel->getLatestByLearner($learnerId);

        $data = [
            'role' => $_SESSION['role'] ?? 'parent',
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'current_page' => 'children',
            'page_title' => 'Learner Details - SignED SPED',
            'learner' => $learner,
            'enrollment' => $enrollment,
            'unread_notifications' => $this->notificationService->getUnreadCount($parentId)
        ];

        $this->view('parent/viewLearner', $data);
    }
}