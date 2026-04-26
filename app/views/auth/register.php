<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 150px;
            height: auto;
        }
        .password-requirements {
            background: #f3f4f6;
            border-left: 3px solid #1e4072;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 13px;
        }
        .password-requirements h4 {
            margin: 0 0 8px 0;
            color: #1f2937;
            font-size: 14px;
        }
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            color: #6b7280;
        }
        .password-requirements li {
            margin: 4px 0;
        }
        .password-requirements li.valid {
            color: #10b981;
        }
        .password-requirements li.invalid {
            color: #ef4444;
        }
        .password-strength {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }
        .password-strength-bar.weak {
            width: 33%;
            background: #ef4444;
        }
        .password-strength-bar.medium {
            width: 66%;
            background: #f59e0b;
        }
        .password-strength-bar.strong {
            width: 100%;
            background: #10b981;
        }
        .password-strength-text {
            font-size: 12px;
            margin-top: 4px;
            color: #6b7280;
        }
    </style>
</head>
<body class="auth-body">

<?php include '../app/views/partials/simple_popup.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <img src="<?php echo URLROOT; ?>/assets/images/SIGNED%20LOGO.png" alt="SignED Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <h1 style="color: #1e4072; margin: 0; display: none;">SignED</h1>
        </div>

        <div class="brand-box">
            <h1>Create Account</h1>
            <p>Fill in your details to get started</p>
        </div>

        <!-- Password Requirements (shown before user types) -->
        <div class="password-requirements">
            <h4>Password Requirements:</h4>
            <ul id="passwordChecklist">
                <li id="req-length">At least 8 characters</li>
                <li id="req-uppercase">One uppercase letter (A-Z)</li>
                <li id="req-lowercase">One lowercase letter (a-z)</li>
                <li id="req-number">One number (0-9)</li>
                <li id="req-special">One special character (!@#$%^&*)</li>
            </ul>
        </div>

        <form action="<?php echo URLROOT; ?>/auth/register" method="POST" class="auth-form" id="registerForm">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="password-strength-text" id="strengthText"></div>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn full">Register</button>
        </form>

        <div class="divider">
            <span>OR</span>
        </div>

        <a href="<?php echo URLROOT; ?>/auth/googleLogin" class="btn btn-google full">
            <svg width="18" height="18" viewBox="0 0 24 24" style="margin-right: 8px;">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
        </a>

        <p class="auth-link">
            Already have an account?
            <a href="<?php echo URLROOT; ?>/auth/login">Login here</a>
        </p>
    </div>
</div>

<script>
// Name normalization - auto-format Full Name field
document.getElementById('fullname').addEventListener('blur', function() {
    let name = this.value.trim();
    
    // Capitalize first letter of each word
    name = name.toLowerCase().replace(/\b\w/g, function(char) {
        return char.toUpperCase();
    });
    
    // Remove extra spaces
    name = name.replace(/\s+/g, ' ');
    
    this.value = name;
});

// Real-time password validation
const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('strengthBar');
const strengthText = document.getElementById('strengthText');

const requirements = {
    length: { regex: /.{8,}/, element: document.getElementById('req-length') },
    uppercase: { regex: /[A-Z]/, element: document.getElementById('req-uppercase') },
    lowercase: { regex: /[a-z]/, element: document.getElementById('req-lowercase') },
    number: { regex: /[0-9]/, element: document.getElementById('req-number') },
    special: { regex: /[^A-Za-z0-9]/, element: document.getElementById('req-special') }
};

passwordInput.addEventListener('input', function() {
    const password = this.value;
    let validCount = 0;
    
    // Check each requirement
    for (let key in requirements) {
        const req = requirements[key];
        if (req.regex.test(password)) {
            req.element.classList.add('valid');
            req.element.classList.remove('invalid');
            validCount++;
        } else {
            req.element.classList.add('invalid');
            req.element.classList.remove('valid');
        }
    }
    
    // Update strength bar
    strengthBar.className = 'password-strength-bar';
    if (validCount <= 2) {
        strengthBar.classList.add('weak');
        strengthText.textContent = 'Weak password';
        strengthText.style.color = '#ef4444';
    } else if (validCount <= 4) {
        strengthBar.classList.add('medium');
        strengthText.textContent = 'Medium password';
        strengthText.style.color = '#f59e0b';
    } else {
        strengthBar.classList.add('strong');
        strengthText.textContent = 'Strong password';
        strengthText.style.color = '#10b981';
    }
    
    // Clear if empty
    if (password === '') {
        strengthBar.className = 'password-strength-bar';
        strengthText.textContent = '';
        for (let key in requirements) {
            requirements[key].element.classList.remove('valid', 'invalid');
        }
    }
});

// Confirm password validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        showPopup('Passwords do not match. Please try again.', 'error');
        return false;
    }
    
    // Check if all requirements are met
    let allValid = true;
    for (let key in requirements) {
        if (!requirements[key].regex.test(password)) {
            allValid = false;
            break;
        }
    }
    
    if (!allValid) {
        e.preventDefault();
        showPopup('Please meet all password requirements.', 'error');
        return false;
    }
});
</script>

</body>
</html>