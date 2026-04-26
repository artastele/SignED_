<?php

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $this->view('admin/dashboard');
    }

    public function users()
    {
        $this->requireLogin();
        $this->requireRole('admin');

        $userModel = $this->model('User');
        $users = $userModel->getAllUsers();

        $data = [
            'users' => $users
        ];

        $this->view('admin/users', $data);
    }

    public function updateRole($id = null)
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $role = $_POST['role'];

            $allowedRoles = ['admin', 'teacher', 'parent'];

            if (!in_array($role, $allowedRoles)) {
                die('Invalid role.');
            }

            $userModel = $this->model('User');

            if ($userModel->updateUserRole($id, $role)) {
                header('Location: ' . URLROOT . '/admin/users');
                exit;
            } else {
                die('Failed to update role.');
            }
        } else {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
    }

    public function deleteUser($id = null)
    {
        $this->requireLogin();
        $this->requireRole('admin');

        if ($id === null) {
            die('User ID is required.');
        }

        $userModel = $this->model('User');

        if ($userModel->deleteUser($id)) {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        } else {
            die('Failed to delete user.');
        }
    }
}