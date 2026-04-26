<?php

class TeacherController extends Controller
{
    public function dashboard()
    {
        $this->requireLogin();
        $this->requireRole('teacher');

        $this->view('teacher/dashboard');
    }
}