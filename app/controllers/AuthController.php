<?php

class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Check account lockout
            if (!$this->checkAccountLockout($email)) {
                header('Location: ' . URLROOT . '/auth/login?locked=1');
                exit;
            }

            $userModel = $this->model('User');
            $user = $userModel->login($email);

            if (!$user) {
                $this->recordFailedAttempt($email);
                header('Location: ' . URLROOT . '/auth/login?error=' . urlencode('User not found. Please check your email address.'));
                exit;
            }

            if (!$user->is_verified) {
                header('Location: ' . URLROOT . '/auth/login?error=' . urlencode('Please verify your email first. Check your inbox for the verification code.'));
                exit;
            }

            if (!password_verify($password, $user->password)) {
                $this->recordFailedAttempt($email);
                header('Location: ' . URLROOT . '/auth/login?error=' . urlencode('Invalid password. Please try again.'));
                exit;
            }

            // Clear login attempts on successful login
            $this->clearLoginAttempts($email);

            $_SESSION['user_id'] = $user->id;
            $_SESSION['fullname'] = $user->fullname;
            $_SESSION['email'] = $user->email;
            $_SESSION['role'] = $user->role;

            // Redirect based on role with SPED support
            if (empty($user->role)) {
                header('Location: ' . URLROOT . '/auth/chooseRole');
            } elseif ($user->role == 'admin') {
                header('Location: ' . URLROOT . '/admin/dashboard');
            } elseif ($user->role == 'teacher') {
                header('Location: ' . URLROOT . '/teacher/dashboard');
            } elseif ($user->role == 'parent') {
                header('Location: ' . URLROOT . '/parent/dashboard');
            } elseif ($user->role == 'sped_teacher') {
                header('Location: ' . URLROOT . '/sped/dashboard');
            } elseif ($user->role == 'guidance') {
                header('Location: ' . URLROOT . '/sped/dashboard');
            } elseif ($user->role == 'principal') {
                header('Location: ' . URLROOT . '/sped/dashboard');
            } elseif ($user->role == 'learner') {
                header('Location: ' . URLROOT . '/learner/dashboard');
            } else {
                header('Location: ' . URLROOT . '/user/dashboard');
            }

            exit;

        } else {
            $this->view('auth/login');
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $firstName = trim($_POST['first_name']);
            $middleName = !empty($_POST['middle_name']) ? trim($_POST['middle_name']) : null;
            $lastName = trim($_POST['last_name']);
            $suffix = !empty($_POST['suffix']) ? trim($_POST['suffix']) : null;
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            // Role should NOT be set during registration - users choose role after verification
            $role = null;

            // Password validation
            if ($password !== $confirmPassword) {
                header('Location: ' . URLROOT . '/auth/register?error=' . urlencode('Passwords do not match. Please try again.'));
                exit;
            }

            // Enforce password policy
            if (!$this->validatePasswordPolicy($password)) {
                header('Location: ' . URLROOT . '/auth/register?error=' . urlencode('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.'));
                exit;
            }

            $userModel = $this->model('User');

            if ($userModel->findUserByEmail($email)) {
                header('Location: ' . URLROOT . '/auth/register?error=' . urlencode('Email already exists. Please use a different email or login.'));
                exit;
            }

            $otp = rand(100000, 999999);
            $otpExpiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $data = [
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'suffix' => $suffix,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role, // Allow role to be set during registration
                'otp_code' => $otp,
                'otp_expires_at' => $otpExpiresAt
            ];

            if ($userModel->register($data)) {
                require_once __DIR__ . '/../helpers/Mailer.php';
                $mailer = new Mailer();

                if ($mailer->sendOTP($email, $otp)) {
                    header('Location: ' . URLROOT . '/auth/verifyOtp?email=' . urlencode($email));
                    exit;
                } else {
                    header('Location: ' . URLROOT . '/auth/register?error=' . urlencode('Registration successful, but OTP email failed to send. Please contact support.'));
                    exit;
                }
            } else {
                header('Location: ' . URLROOT . '/auth/register?error=' . urlencode('Something went wrong during registration. Please try again.'));
                exit;
            }

        } else {
            $this->view('auth/register');
        }
    }

    public function verifyOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $email = trim($_POST['email']);
            $otp = trim($_POST['otp']);

            $userModel = $this->model('User');
            $user = $userModel->verifyOTP($email, $otp);

            if ($user) {
                if ($userModel->markAsVerified($email)) {

                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['fullname'] = $user->fullname;
                    $_SESSION['email'] = $user->email;
                    $_SESSION['role'] = $user->role;

                    header('Location: ' . URLROOT . '/auth/verifySuccess');
                    exit;

                } else {
                    header('Location: ' . URLROOT . '/auth/verifyOtp?email=' . urlencode($email) . '&error=' . urlencode('Verification failed. Please try again.'));
                    exit;
                }
            } else {
                header('Location: ' . URLROOT . '/auth/verifyOtp?email=' . urlencode($email) . '&error=' . urlencode('Invalid or expired OTP. Please check the code and try again.'));
                exit;
            }

        } else {
            $email = isset($_GET['email']) ? $_GET['email'] : '';
            $data = ['email' => $email];
            $this->view('auth/verify_otp', $data);
        }
    }

    public function verifySuccess()
    {
        $this->view('auth/verify_success');
    }

    public function chooseRole()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $this->view('auth/choose_role');
    }

    public function saveRole()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }

            $role = trim($_POST['role']);
            $allowedRoles = ['teacher', 'parent', 'sped_teacher', 'guidance', 'principal', 'learner'];

            if (!in_array($role, $allowedRoles)) {
                header('Location: ' . URLROOT . '/auth/chooseRole?error=' . urlencode('Invalid role selected. Please choose a valid role.'));
                exit;
            }

            $userModel = $this->model('User');

            if ($userModel->updateRole($_SESSION['user_id'], $role)) {
                $_SESSION['role'] = $role;

                // If learner role is selected, create a learner record
                if ($role == 'learner') {
                    $learnerModel = $this->model('Learner');
                    
                    // Check if learner record already exists
                    $existingLearner = $learnerModel->getByUserId($_SESSION['user_id']);
                    
                    if (!$existingLearner) {
                        // Create a basic learner record
                        // Note: This requires additional information to be collected later
                        $user = $userModel->getUserById($_SESSION['user_id']);
                        
                        $learnerData = [
                            'user_id' => $_SESSION['user_id'],
                            'parent_id' => null, // To be filled later
                            'first_name' => $user->first_name ?? 'Unknown',
                            'middle_name' => $user->middle_name,
                            'last_name' => $user->last_name ?? 'Unknown',
                            'suffix' => $user->suffix,
                            'date_of_birth' => null, // To be filled later
                            'grade_level' => null, // To be filled later
                            'status' => 'pending_info' // New status for incomplete profiles
                        ];
                        
                        $learnerModel->createBasicLearner($learnerData);
                    }
                }

                // Enhanced role-based redirection
                if ($role == 'teacher') {
                    header('Location: ' . URLROOT . '/teacher/dashboard');
                } elseif ($role == 'parent') {
                    header('Location: ' . URLROOT . '/parent/dashboard');
                } elseif ($role == 'sped_teacher' || $role == 'guidance' || $role == 'principal') {
                    header('Location: ' . URLROOT . '/sped/dashboard');
                } elseif ($role == 'learner') {
                    header('Location: ' . URLROOT . '/learner/dashboard');
                } else {
                    header('Location: ' . URLROOT . '/user/dashboard');
                }
                exit;
            } else {
                header('Location: ' . URLROOT . '/auth/chooseRole?error=' . urlencode('Failed to save role. Please try again.'));
                exit;
            }

        } else {
            header('Location: ' . URLROOT . '/auth/chooseRole');
            exit;
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        header('Location: ' . URLROOT . '/auth/login');
        exit;
    }

    // Google OAuth Methods
    public function googleLogin()
    {
        require_once '../config/google.php';
        
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'scope' => GOOGLE_SCOPES,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        $authUrl = GOOGLE_AUTH_URL . '?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    public function googleCallback()
    {
        require_once '../config/google.php';

        if (!isset($_GET['code'])) {
            die('Authorization code not received from Google.');
        }

        $code = $_GET['code'];

        // Exchange code for access token
        $tokenData = [
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_TOKEN_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            die('Failed to get access token from Google.');
        }

        $tokenInfo = json_decode($response, true);
        
        if (!isset($tokenInfo['access_token'])) {
            die('Access token not received from Google.');
        }

        // Get user info from Google
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_USER_INFO_URL . '?access_token=' . $tokenInfo['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $userResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            die('Failed to get user info from Google.');
        }

        $googleUser = json_decode($userResponse, true);

        if (!isset($googleUser['email'])) {
            die('Email not received from Google.');
        }

        $userModel = $this->model('User');

        // Check if user exists by Google ID
        $user = $userModel->findUserByGoogleId($googleUser['id']);

        if (!$user) {
            // Check if user exists by email
            $user = $userModel->findUserByEmail($googleUser['email']);
            
            if ($user) {
                // Link Google account to existing user
                $userModel->linkGoogleAccount($user->id, $googleUser['id']);
            } else {
                // Create new user
                if ($userModel->createGoogleUser($googleUser)) {
                    $user = $userModel->findUserByEmail($googleUser['email']);
                } else {
                    die('Failed to create user account.');
                }
            }
        }

        // Set session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['fullname'] = $user->fullname;
        $_SESSION['email'] = $user->email;
        $_SESSION['role'] = $user->role;

        // Redirect based on role
        if (empty($user->role)) {
            header('Location: ' . URLROOT . '/auth/chooseRole');
        } elseif ($user->role == 'admin') {
            header('Location: ' . URLROOT . '/admin/dashboard');
        } elseif ($user->role == 'teacher') {
            header('Location: ' . URLROOT . '/teacher/dashboard');
        } elseif ($user->role == 'parent') {
            header('Location: ' . URLROOT . '/parent/dashboard');
        } elseif ($user->role == 'sped_teacher') {
            header('Location: ' . URLROOT . '/sped/dashboard');
        } elseif ($user->role == 'guidance') {
            header('Location: ' . URLROOT . '/sped/dashboard');
        } elseif ($user->role == 'principal') {
            header('Location: ' . URLROOT . '/sped/dashboard');
        } elseif ($user->role == 'learner') {
            header('Location: ' . URLROOT . '/learner/dashboard');
        } else {
            header('Location: ' . URLROOT . '/user/dashboard');
        }
        exit;
    }

    /**
     * Validate password policy
     */
    private function validatePasswordPolicy($password)
    {
        // Minimum 8 characters
        if (strlen($password) < 8) {
            return false;
        }

        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // At least one number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        // At least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Check and handle account lockout
     */
    private function checkAccountLockout($email)
    {
        $cacheKey = 'login_attempts_' . md5($email);
        
        // Simple file-based attempt tracking (you can enhance this with Redis/Memcached)
        $attemptsFile = '../cache/' . $cacheKey . '.txt';
        
        if (!file_exists('../cache/')) {
            mkdir('../cache/', 0755, true);
        }

        $attempts = 0;
        $lastAttempt = 0;

        if (file_exists($attemptsFile)) {
            $data = file_get_contents($attemptsFile);
            list($attempts, $lastAttempt) = explode('|', $data);
        }

        // Reset attempts if more than 15 minutes have passed
        if (time() - $lastAttempt > 900) { // 15 minutes
            $attempts = 0;
        }

        // Check if account is locked (5 attempts in 15 minutes = 30 minute lockout)
        if ($attempts >= 5 && time() - $lastAttempt < 1800) { // 30 minutes
            return false; // Account is locked
        }

        return true; // Account is not locked
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedAttempt($email)
    {
        $cacheKey = 'login_attempts_' . md5($email);
        $attemptsFile = '../cache/' . $cacheKey . '.txt';

        $attempts = 0;
        if (file_exists($attemptsFile)) {
            $data = file_get_contents($attemptsFile);
            list($attempts, $lastAttempt) = explode('|', $data);
        }

        $attempts++;
        file_put_contents($attemptsFile, $attempts . '|' . time());
    }

    /**
     * Clear login attempts on successful login
     */
    private function clearLoginAttempts($email)
    {
        $cacheKey = 'login_attempts_' . md5($email);
        $attemptsFile = '../cache/' . $cacheKey . '.txt';

        if (file_exists($attemptsFile)) {
            unlink($attemptsFile);
        }
    }
}