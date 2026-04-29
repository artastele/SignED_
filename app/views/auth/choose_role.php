<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Role - SignED</title>
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
        .role-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .role-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        .role-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }
        .role-card.selected {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .role-card input[type="radio"] {
            display: none;
        }
        .role-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .role-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .role-description {
            font-size: 12px;
            color: #6b7280;
        }
        @media (max-width: 600px) {
            .role-cards {
                grid-template-columns: 1fr;
            }
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
            <h1>Select Role</h1>
            <p>Choose how you will use SignED</p>
        </div>

        <p class="subtitle">Select your role to continue to the correct dashboard.</p>

        <form action="<?php echo URLROOT; ?>/auth/saveRole" method="POST" class="auth-form" id="roleForm">
            <div class="role-cards">
                <label class="role-card" data-role="teacher">
                    <input type="radio" name="role" value="teacher" required>
                    <div class="role-icon">👨‍🏫</div>
                    <div class="role-title">Teacher</div>
                    <div class="role-description">Regular classroom teacher</div>
                </label>

                <label class="role-card" data-role="parent">
                    <input type="radio" name="role" value="parent" required>
                    <div class="role-icon">👨‍👩‍👧</div>
                    <div class="role-title">Parent</div>
                    <div class="role-description">Parent or guardian</div>
                </label>

                <label class="role-card" data-role="sped_teacher">
                    <input type="radio" name="role" value="sped_teacher" required>
                    <div class="role-icon">🎓</div>
                    <div class="role-title">SPED Teacher</div>
                    <div class="role-description">Special education teacher</div>
                </label>

                <label class="role-card" data-role="guidance">
                    <input type="radio" name="role" value="guidance" required>
                    <div class="role-icon">💼</div>
                    <div class="role-title">Guidance</div>
                    <div class="role-description">Guidance counselor</div>
                </label>

                <label class="role-card" data-role="principal">
                    <input type="radio" name="role" value="principal" required>
                    <div class="role-icon">🏛️</div>
                    <div class="role-title">Principal</div>
                    <div class="role-description">School principal</div>
                </label>
            </div>

            <div class="alert alert-info" style="margin-top: 15px; padding: 10px; background: #eff6ff; border: 1px solid #3b82f6; border-radius: 6px; font-size: 13px;">
                <strong>Note:</strong> Learner accounts are automatically created when enrollment is approved. Admin accounts are created manually by system administrators.
            </div>

            <button type="submit" class="btn full">Continue</button>
        </form>

        <a class="btn logout full-btn-space" href="<?php echo URLROOT; ?>/auth/logout">Logout</a>
    </div>
</div>

<script>
// Role card selection
const roleCards = document.querySelectorAll('.role-card');
const roleForm = document.getElementById('roleForm');

roleCards.forEach(card => {
    card.addEventListener('click', function() {
        // Remove selected class from all cards
        roleCards.forEach(c => c.classList.remove('selected'));
        
        // Add selected class to clicked card
        this.classList.add('selected');
        
        // Check the radio button
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
    });
});

// Form validation
roleForm.addEventListener('submit', function(e) {
    const selectedRole = document.querySelector('input[name="role"]:checked');
    
    if (!selectedRole) {
        e.preventDefault();
        showPopup('Please select a role to continue.', 'error');
        return false;
    }
});
</script>

</body>
</html>