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

    public function updateProfile()
    {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            
            $data = [
                'first_name' => trim($_POST['first_name']),
                'middle_name' => !empty($_POST['middle_name']) ? trim($_POST['middle_name']) : null,
                'last_name' => trim($_POST['last_name']),
                'suffix' => !empty($_POST['suffix']) ? trim($_POST['suffix']) : null,
                'email' => trim($_POST['email']),
                'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
                'address' => !empty($_POST['address']) ? trim($_POST['address']) : null
            ];
            
            if ($userModel->updateProfile($_SESSION['user_id'], $data)) {
                // Update session with new name
                $user = $userModel->getUserById($_SESSION['user_id']);
                $_SESSION['fullname'] = $user->fullname;
                $_SESSION['email'] = $user->email;
                
                header('Location: ' . URLROOT . '/user/profile?success=1');
            } else {
                header('Location: ' . URLROOT . '/user/profile?error=1');
            }
            exit;
        }
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
