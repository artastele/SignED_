<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SignED</title>
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
    </style>
</head>
<body class="auth-body">

<!-- Include Simple Pop-up -->
<?php include '../app/views/partials/simple_popup.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <img src="<?php echo URLROOT; ?>/assets/images/SIGNED%20LOGO.png" alt="SignED Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <h1 style="color: #1e4072; margin: 0; display: none;">SignED</h1>
        </div>

        <div class="brand-box">
            <h1>Welcome Back!</h1>
            <p>Sign in to continue to your account</p>
        </div>

        <form action="<?php echo URLROOT; ?>/auth/login" method="POST" class="auth-form">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn full">Login</button>
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
            Don't have an account?
            <a href="<?php echo URLROOT; ?>/auth/register">Register here</a>
        </p>
    </div>
</div>

</body>
</html>
