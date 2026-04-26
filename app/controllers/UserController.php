<?php

class UserController extends Controller
{
    public function dashboard()
    {
        $this->requireLogin();
        $this->view('user/dashboard');
    }

    public function profile()
    {
        $this->requireLogin();
        
        $userModel = $this->model('User');
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        $data = [
            'user' => $user,
            'role' => $_SESSION['role'] ?? '',
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'page_title' => 'Profile - SignED SPED',
            'current_page' => 'profile'
        ];
        
        $this->view('user/profile', $data);
    }

    public function settings()
    {
        $this->requireLogin();
        
        $data = [
            'role' => $_SESSION['role'] ?? '',
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'page_title' => 'Settings - SignED SPED',
            'current_page' => 'settings'
        ];
        
        $this->view('user/settings', $data);
    }
}
