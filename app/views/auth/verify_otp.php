<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/style.css?v=2.0">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/auth-modern.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 150px;
            height: auto;
        }
        .otp-input-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .otp-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .otp-input.filled {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .hidden-otp {
            display: none;
        }
    </style>
</head>
<body class="modern-auth-body">

<?php include '../app/views/partials/simple_popup.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <img src="<?php echo URLROOT; ?>/assets/images/SIGNED%20LOGO.png" alt="SignED Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <h1 style="color: #1e4072; margin: 0; display: none;">SignED</h1>
        </div>

        <div class="brand-box">
            <h1>OTP Verification</h1>
            <p>Check your email for the code</p>
        </div>

        <?php if (!empty($data['warning'])): ?>
            <p class="subtitle" style="color: #b45309; font-weight: 600;"><?php echo htmlspecialchars($data['warning']); ?></p>
        <?php endif; ?>

        <?php if (!empty($data['debug_otp'])): ?>
            <p class="subtitle" style="color: #0f766e; font-weight: 700;">Development OTP: <?php echo htmlspecialchars($data['debug_otp']); ?></p>
        <?php endif; ?>

        <p class="subtitle">Enter the 6-digit OTP sent to your email.</p>

        <form action="<?php echo URLROOT; ?>/auth/verifyOtp" method="POST" class="auth-form" id="otpForm">
            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    readonly
                    value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>OTP Code</label>
                <div class="otp-input-container">
                    <input type="text" class="otp-input" maxlength="1" data-index="0" />
                    <input type="text" class="otp-input" maxlength="1" data-index="1" />
                    <input type="text" class="otp-input" maxlength="1" data-index="2" />
                    <input type="text" class="otp-input" maxlength="1" data-index="3" />
                    <input type="text" class="otp-input" maxlength="1" data-index="4" />
                    <input type="text" class="otp-input" maxlength="1" data-index="5" />
                </div>
                <input type="hidden" name="otp" id="otpValue" required />
            </div>

            <button type="submit" class="btn full">Verify OTP</button>
        </form>

        <p class="auth-link">
            Wrong email?
            <a href="<?php echo URLROOT; ?>/auth/register">Register again</a>
        </p>
    </div>
</div>

<script>
// OTP Input handling
const otpInputs = document.querySelectorAll('.otp-input');
const otpValue = document.getElementById('otpValue');
const otpForm = document.getElementById('otpForm');

otpInputs.forEach((input, index) => {
    // Handle input
    input.addEventListener('input', function(e) {
        const value = this.value;
        
        // Only allow numbers
        if (!/^\d*$/.test(value)) {
            this.value = '';
            return;
        }
        
        // Mark as filled
        if (value) {
            this.classList.add('filled');
            
            // Move to next input
            if (index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        } else {
            this.classList.remove('filled');
        }
        
        // Update hidden input
        updateOtpValue();
    });
    
    // Handle backspace
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && index > 0) {
            otpInputs[index - 1].focus();
            otpInputs[index - 1].value = '';
            otpInputs[index - 1].classList.remove('filled');
            updateOtpValue();
        }
    });
    
    // Handle paste
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').trim();
        
        // Only allow 6-digit numbers
        if (/^\d{6}$/.test(pastedData)) {
            pastedData.split('').forEach((char, i) => {
                if (otpInputs[i]) {
                    otpInputs[i].value = char;
                    otpInputs[i].classList.add('filled');
                }
            });
            otpInputs[5].focus();
            updateOtpValue();
        }
    });
});

// Update hidden OTP value
function updateOtpValue() {
    const otp = Array.from(otpInputs).map(input => input.value).join('');
    otpValue.value = otp;
}

// Form validation
otpForm.addEventListener('submit', function(e) {
    const otp = otpValue.value;
    
    if (otp.length !== 6) {
        e.preventDefault();
        showPopup('Please enter all 6 digits of the OTP code.', 'error');
        return false;
    }
    
    if (!/^\d{6}$/.test(otp)) {
        e.preventDefault();
        showPopup('OTP must contain only numbers.', 'error');
        return false;
    }
});

// Auto-focus first input
otpInputs[0].focus();
</script>

</body>
</html>