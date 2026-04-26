<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
</head>
<body class="dashboard-body">

<div class="dashboard-wrapper">
    <div class="dashboard-card">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'User'); ?>!</h1>
        <p class="subtitle">Your email has been verified successfully.</p>

        <?php if (empty($_SESSION['role'])): ?>
            <div class="notice-box">
                <p>Please select your role to continue to your dashboard.</p>
                <a class="btn" href="<?php echo URLROOT; ?>/auth/chooseRole">Choose Role</a>
            </div>
        <?php else: ?>
            <div class="notice-box">
                <p>Your role is already set as <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>
            </div>
        <?php endif; ?>

        <a class="btn logout" href="<?php echo URLROOT; ?>/auth/logout">Logout</a>
    </div>
</div>

</body>
</html>