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
        $announcementsSql = "SELECT * FROM announcements 
                            WHERE (target_role = 'parent' OR target_role = 'all') 
                            AND is_active = 1 
                            AND (expires_at IS NULL OR expires_at > NOW())
                            ORDER BY priority DESC, created_at DESC 
                            LIMIT 5";
        
        $stmt = $this->db->prepare($announcementsSql);
        $stmt->execute();
        $announcements = $stmt->fetchAll(PDO::FETCH_OBJ);

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

        $this->view('parent/dashboard_bootstrap', $data);
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
}