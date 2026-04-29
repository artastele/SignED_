<?php

class PrincipalController extends Controller
{
    private $notificationService;
    private $iepModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = $this->model('NotificationService');
        $this->iepModel = $this->model('Iep');
    }

    public function dashboard()
    {
        $this->requireLogin();
        $this->requireRole('principal');

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        // Get announcements for principal
        $announcementModel = $this->model('Announcement');
        $announcements = $announcementModel->getForUser('principal');

        // Get principal-specific data
        $pendingApprovals = $this->iepModel->getByStatus('pending_approval');
        $recentApprovals = $this->iepModel->getRecentApprovals();
        $rejectedIeps = $this->iepModel->getByStatus('rejected');

        // Get unread notifications count
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        $data = [
            'role' => $role,
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'current_page' => 'dashboard',
            'page_title' => 'Principal Dashboard - SignED SPED',
            'announcements' => $announcements,
            'pending_approvals' => $pendingApprovals,
            'recent_approvals' => $recentApprovals,
            'rejected_ieps' => $rejectedIeps,
            'unread_notifications' => $unreadCount,
            'pending_approvals_count' => count($pendingApprovals)
        ];

        $this->view('principal/dashboard', $data);
    }
}
