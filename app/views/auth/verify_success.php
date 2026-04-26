<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verified - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">

    <script>
        setTimeout(function () {
            window.location.href = "<?php echo URLROOT; ?>/user/dashboard";
        }, 3000);
    </script>
</head>
<body class="auth-body">

<div class="auth-container">
    <div class="auth-card" style="text-align:center;">

        <h1 style="color:#16a34a;">✔ Verification Successful</h1>
        <p class="subtitle">Your email is now verified.</p>

        <div class="notice-box">
            <p>Redirecting to your dashboard...</p>
            <a class="btn" href="<?php echo URLROOT; ?>/user/dashboard">
                Go Now
            </a>
        </div>

    </div>
</div>

</body>
</html>