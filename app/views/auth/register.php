<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/auth-modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="modern-auth-body">

<?php include '../app/views/partials/simple_popup.php'; ?>

<div class="modern-auth-wrapper">
    <!-- Left Side - Branding -->
    <div class="auth-branding">
        <div class="branding-content">
            <div class="logo-section">
                <img src="<?php echo URLROOT; ?>/assets/images/SIGNED%20LOGO.png" alt="SignED Logo" class="brand-logo" onerror="this.style.display='none';">
                <h1 class="brand-title">SignED</h1>
            </div>
            <h2 class="branding-headline">Join Our SPED Community</h2>
            <p class="branding-description">Create your account and start managing special education programs with ease and efficiency.</p>
            
            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-user-shield"></i>
                    <span>Secure & Private</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <span>Quick Setup</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <span>Multi-Role Support</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Mobile Friendly</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Register Form -->
    <div class="auth-form-section register-section">
        <div class="form-container">
            <div class="form-header">
                <h2>Create Account</h2>
                <p>Fill in your details to get started</p>
            </div>

            <!-- Password Requirements -->
            <div class="password-requirements-modern">
                <div class="requirements-header">
                    <i class="fas fa-shield-alt"></i>
                    <span>Password Requirements</span>
                </div>
                <ul id="passwordChecklist">
                    <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                    <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter (A-Z)</li>
                    <li id="req-lowercase"><i class="fas fa-circle"></i> One lowercase letter (a-z)</li>
                    <li id="req-number"><i class="fas fa-circle"></i> One number (0-9)</li>
                    <li id="req-special"><i class="fas fa-circle"></i> One special character (!@#$%^&*)</li>
                </ul>
            </div>

            <form action="<?php echo URLROOT; ?>/auth/register" method="POST" class="modern-form" id="registerForm">
                <!-- Name Fields Row -->
                <div class="form-row">
                    <div class="input-group half">
                        <label for="first_name">First Name *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="first_name" name="first_name" placeholder="First name" required>
                        </div>
                    </div>

                    <div class="input-group half">
                        <label for="middle_name">Middle Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="middle_name" name="middle_name" placeholder="Middle name">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group half">
                        <label for="last_name">Last Name *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="last_name" name="last_name" placeholder="Last name" required>
                        </div>
                    </div>

                    <div class="input-group half">
                        <label for="suffix">Suffix</label>
                        <div class="input-wrapper">
                            <i class="fas fa-tag input-icon"></i>
                            <select id="suffix" name="suffix">
                                <option value="">None</option>
                                <option value="Jr.">Jr.</option>
                                <option value="Sr.">Sr.</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                                <option value="V">V</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Email Address *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Password *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength-modern">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <span>Create Account</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="divider-modern">
                <span>or sign up with</span>
            </div>

            <a href="<?php echo URLROOT; ?>/auth/googleLogin" class="btn-google-modern">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Sign up with Google</span>
            </a>

            <p class="form-footer">
                Already have an account? 
                <a href="<?php echo URLROOT; ?>/auth/login">Sign in here</a>
            </p>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = event.currentTarget.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Name normalization - auto-format name fields
['first_name', 'middle_name', 'last_name'].forEach(function(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('blur', function() {
            let name = this.value.trim();
            
            // Capitalize first letter of each word
            name = name.toLowerCase().replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
            
            // Remove extra spaces
            name = name.replace(/\s+/g, ' ');
            
            this.value = name;
        });
    }
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
        const icon = req.element.querySelector('i');
        
        if (req.regex.test(password)) {
            req.element.classList.add('valid');
            req.element.classList.remove('invalid');
            icon.classList.remove('fa-circle');
            icon.classList.add('fa-check-circle');
            validCount++;
        } else {
            req.element.classList.add('invalid');
            req.element.classList.remove('valid');
            icon.classList.remove('fa-check-circle');
            icon.classList.add('fa-circle');
        }
    }
    
    // Update strength bar
    strengthBar.className = 'strength-bar';
    if (password === '') {
        strengthBar.classList.remove('weak', 'medium', 'strong');
        strengthText.textContent = '';
    } else if (validCount <= 2) {
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
        for (let key in requirements) {
            requirements[key].element.classList.remove('valid', 'invalid');
            const icon = requirements[key].element.querySelector('i');
            icon.classList.remove('fa-check-circle');
            icon.classList.add('fa-circle');
        }
    }
});

// Form validation
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
