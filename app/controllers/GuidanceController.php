<?php

class GuidanceController extends Controller
{
    private $notificationService;
    private $meetingModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = $this->model('NotificationService');
        $this->meetingModel = $this->model('IepMeeting');
    }

    public function dashboard()
    {
        $this->requireLogin();
        $this->requireRole('guidance');

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        // Get announcements for guidance
        $announcementModel = $this->model('Announcement');
        $announcements = $announcementModel->getForUser('guidance');

        // Get guidance-specific data
        $scheduledMeetings = $this->meetingModel->getByStatus('scheduled');
        $confirmedMeetings = $this->meetingModel->getByStatus('confirmed');
        $upcomingMeetings = $this->meetingModel->getUpcoming();

        // Get unread notifications count
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        $data = [
            'role' => $role,
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'current_page' => 'dashboard',
            'page_title' => 'Guidance Dashboard - SignED SPED',
            'announcements' => $announcements,
            'scheduled_meetings' => $scheduledMeetings,
            'confirmed_meetings' => $confirmedMeetings,
            'upcoming_meetings' => $upcomingMeetings,
            'unread_notifications' => $unreadCount,
            'upcoming_meetings_count' => count($upcomingMeetings)
        ];

        $this->view('guidance/dashboard', $data);
    }
}
