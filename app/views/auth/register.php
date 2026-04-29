<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/style.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/auth-modern.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="modern-auth-body">

<?php include '../app/views/partials/simple_popup.php'; ?>

<!-- Show error/success messages -->
<?php if (isset($_GET['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showPopup('<?php echo htmlspecialchars($_GET['error']); ?>', 'error');
        });
    </script>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showPopup('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');
        });
    </script>
<?php endif; ?>

<div class="modern-auth-wrapper">
    <!-- Left Side - Branding -->
    <div class="auth-branding">
        <div class="branding-content">
            <div class="logo-section">
                <img src="<?php echo URLROOT; ?>/public/assets/images/SIGNED%20LOGO.png" alt="SignED Logo" class="brand-logo" onerror="this.style.display='none';">
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

            <form action="<?php echo URLROOT; ?>/auth/register" method="POST" class="modern-form" id="registerForm">
                <!-- Name Fields Row -->
                <div class="form-row">
                    <div class="input-group half">
                        <label for="first_name">First Name *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="first_name" name="first_name" placeholder="First name" 
                                   value="<?php echo isset($_GET['first_name']) ? htmlspecialchars($_GET['first_name']) : ''; ?>" 
                                   required minlength="2" maxlength="100" autocomplete="given-name">
                        </div>
                        <small class="text-danger" id="first-name-error" style="display: none;"></small>
                    </div>

                    <div class="input-group half">
                        <label for="middle_name">Middle Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="middle_name" name="middle_name" placeholder="Middle name" 
                                   value="<?php echo isset($_GET['middle_name']) ? htmlspecialchars($_GET['middle_name']) : ''; ?>" 
                                   maxlength="100" autocomplete="additional-name">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group half">
                        <label for="last_name">Last Name *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="last_name" name="last_name" placeholder="Last name" 
                                   value="<?php echo isset($_GET['last_name']) ? htmlspecialchars($_GET['last_name']) : ''; ?>" 
                                   required minlength="2" maxlength="100" autocomplete="family-name">
                        </div>
                        <small class="text-danger" id="last-name-error" style="display: none;"></small>
                    </div>

                    <div class="input-group half">
                        <label for="suffix">Suffix</label>
                        <div class="input-wrapper">
                            <i class="fas fa-tag input-icon"></i>
                            <select id="suffix" name="suffix" autocomplete="honorific-suffix">
                                <option value="">None</option>
                                <option value="Jr." <?php echo (isset($_GET['suffix']) && $_GET['suffix'] == 'Jr.') ? 'selected' : ''; ?>>Jr.</option>
                                <option value="Sr." <?php echo (isset($_GET['suffix']) && $_GET['suffix'] == 'Sr.') ? 'selected' : ''; ?>>Sr.</option>
                                <option value="II" <?php echo (isset($_GET['suffix']) && $_GET['suffix'] == 'II') ? 'selected' : ''; ?>>II</option>
                                <option value="III" <?php echo (isset($_GET['suffix']) && $_GET['suffix'] == 'III') ? 'selected' : ''; ?>>III</option>
                                <option value="IV" <?php echo (isset($_GET['suffix']) && $_GET['suffix'] == 'IV') ? 'selected' : ''; ?>>IV</option>
                                <option value="V" <?php echo (isset($_GET['suffix']) && $_GET['suffix'] == 'V') ? 'selected' : ''; ?>>V</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Email Address *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="you@example.com" 
                               value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" 
                               required autocomplete="email">
                    </div>
                    <small class="text-danger" id="email-error" style="display: none;"></small>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Password *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Create a strong password" 
                               required minlength="8" autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength-modern">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                    <small class="password-requirements" style="color: #666; font-size: 0.85em; display: block; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i> Password must include:
                        <ul style="margin: 5px 0 0 20px; padding: 0;">
                            <li id="req-length" class="requirement">✗ At least 8 characters</li>
                            <li id="req-uppercase" class="requirement">✗ One uppercase letter</li>
                            <li id="req-lowercase" class="requirement">✗ One lowercase letter</li>
                            <li id="req-number" class="requirement">✗ One number</li>
                            <li id="req-special" class="requirement">✗ One special character (!@#$%^&*)</li>
                        </ul>
                    </small>
                    <small class="text-danger" id="password-error" style="display: none;"></small>
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" 
                               required autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="text-danger" id="confirm-password-error" style="display: none;"></small>
                </div>

                <button type="submit" class="btn-primary" id="registerBtn">
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

// Real-time password validation with visual feedback
const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('strengthBar');
const strengthText = document.getElementById('strengthText');

passwordInput.addEventListener('input', function() {
    const password = this.value;
    let validCount = 0;
    
    // Check each requirement and update UI
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };
    
    // Update requirement indicators
    document.getElementById('req-length').innerHTML = requirements.length ? 
        '<span style="color: #10b981;">✓</span> At least 8 characters' : 
        '<span style="color: #ef4444;">✗</span> At least 8 characters';
    document.getElementById('req-uppercase').innerHTML = requirements.uppercase ? 
        '<span style="color: #10b981;">✓</span> One uppercase letter' : 
        '<span style="color: #ef4444;">✗</span> One uppercase letter';
    document.getElementById('req-lowercase').innerHTML = requirements.lowercase ? 
        '<span style="color: #10b981;">✓</span> One lowercase letter' : 
        '<span style="color: #ef4444;">✗</span> One lowercase letter';
    document.getElementById('req-number').innerHTML = requirements.number ? 
        '<span style="color: #10b981;">✓</span> One number' : 
        '<span style="color: #ef4444;">✗</span> One number';
    document.getElementById('req-special').innerHTML = requirements.special ? 
        '<span style="color: #10b981;">✓</span> One special character (!@#$%^&*)' : 
        '<span style="color: #ef4444;">✗</span> One special character (!@#$%^&*)';
    
    // Count valid requirements
    validCount = Object.values(requirements).filter(Boolean).length;
    
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
    
    // Check ALL password requirements
    if (password.length < 8) {
        e.preventDefault();
        showPopup('Password must be at least 8 characters long.', 'error');
        return false;
    }
    
    if (!/[A-Z]/.test(password)) {
        e.preventDefault();
        showPopup('Password must include at least one uppercase letter.', 'error');
        return false;
    }
    
    if (!/[a-z]/.test(password)) {
        e.preventDefault();
        showPopup('Password must include at least one lowercase letter.', 'error');
        return false;
    }
    
    if (!/[0-9]/.test(password)) {
        e.preventDefault();
        showPopup('Password must include at least one number.', 'error');
        return false;
    }
    
    if (!/[^A-Za-z0-9]/.test(password)) {
        e.preventDefault();
        showPopup('Password must include at least one special character.', 'error');
        return false;
    }
});
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
        
        // Real-time validation
        field.addEventListener('input', function() {
            const errorElement = document.getElementById(fieldId.replace('_', '-') + '-error');
            if (this.value.trim().length > 0 && this.value.trim().length < 2) {
                errorElement.textContent = 'Must be at least 2 characters';
                errorElement.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                errorElement.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }
});

// Email validation
const emailInput = document.getElementById('email');
emailInput.addEventListener('blur', function() {
    const emailError = document.getElementById('email-error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(this.value)) {
        emailError.textContent = 'Please enter a valid email address';
        emailError.style.display = 'block';
        this.classList.add('is-invalid');
    } else {
        emailError.style.display = 'none';
        this.classList.remove('is-invalid');
    }
});

// Real-time password validation with visual feedback
const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('strengthBar');
const strengthText = document.getElementById('strengthText');

passwordInput.addEventListener('input', function() {
    const password = this.value;
    let validCount = 0;
    
    // Check each requirement and update UI
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };
    
    // Update requirement indicators
    document.getElementById('req-length').innerHTML = requirements.length ? 
        '<span style="color: #10b981;">✓</span> At least 8 characters' : 
        '<span style="color: #ef4444;">✗</span> At least 8 characters';
    document.getElementById('req-uppercase').innerHTML = requirements.uppercase ? 
        '<span style="color: #10b981;">✓</span> One uppercase letter' : 
        '<span style="color: #ef4444;">✗</span> One uppercase letter';
    document.getElementById('req-lowercase').innerHTML = requirements.lowercase ? 
        '<span style="color: #10b981;">✓</span> One lowercase letter' : 
        '<span style="color: #ef4444;">✗</span> One lowercase letter';
    document.getElementById('req-number').innerHTML = requirements.number ? 
        '<span style="color: #10b981;">✓</span> One number' : 
        '<span style="color: #ef4444;">✗</span> One number';
    document.getElementById('req-special').innerHTML = requirements.special ? 
        '<span style="color: #10b981;">✓</span> One special character (!@#$%^&*)' : 
        '<span style="color: #ef4444;">✗</span> One special character (!@#$%^&*)';
    
    // Count valid requirements
    validCount = Object.values(requirements).filter(Boolean).length;
    
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
});

// Confirm password validation
const confirmPasswordInput = document.getElementById('confirm_password');
confirmPasswordInput.addEventListener('input', function() {
    const confirmError = document.getElementById('confirm-password-error');
    const password = document.getElementById('password').value;
    
    if (this.value && this.value !== password) {
        confirmError.textContent = 'Passwords do not match';
        confirmError.style.display = 'block';
        this.classList.add('is-invalid');
    } else {
        confirmError.style.display = 'none';
        this.classList.remove('is-invalid');
    }
});

// Form validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const firstName = document.getElementById('first_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const registerBtn = document.getElementById('registerBtn');
    
    let isValid = true;
    let errorMessage = '';
    
    // Validate names
    if (firstName.length < 2) {
        errorMessage = 'First name must be at least 2 characters';
        isValid = false;
    } else if (lastName.length < 2) {
        errorMessage = 'Last name must be at least 2 characters';
        isValid = false;
    }
    
    // Validate email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        errorMessage = 'Please enter a valid email address';
        isValid = false;
    }
    
    // Validate password match
    if (password !== confirmPassword) {
        errorMessage = 'Passwords do not match. Please try again.';
        isValid = false;
    }
    
    // Check ALL password requirements
    if (password.length < 8) {
        errorMessage = 'Password must be at least 8 characters long.';
        isValid = false;
    } else if (!/[A-Z]/.test(password)) {
        errorMessage = 'Password must include at least one uppercase letter.';
        isValid = false;
    } else if (!/[a-z]/.test(password)) {
        errorMessage = 'Password must include at least one lowercase letter.';
        isValid = false;
    } else if (!/[0-9]/.test(password)) {
        errorMessage = 'Password must include at least one number.';
        isValid = false;
    } else if (!/[^A-Za-z0-9]/.test(password)) {
        errorMessage = 'Password must include at least one special character (!@#$%^&*).';
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        showPopup(errorMessage, 'error');
        return false;
    }
    
    // Disable button and show loading state
    registerBtn.disabled = true;
    registerBtn.innerHTML = '<span>Creating Account...</span><i class="fas fa-spinner fa-spin"></i>';
});
</script>

</body>
</html>
