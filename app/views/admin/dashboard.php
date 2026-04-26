<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="topbar">
        <h1>Admin Dashboard</h1>
        <a class="btn logout" href="<?php echo URLROOT; ?>/auth/logout">Logout</a>
    </div>

    <div class="card">
        <h2>Welcome, Admin</h2>
        <p>This is your temporary admin dashboard.</p>
        <a class="btn" href="<?php echo URLROOT; ?>/admin/users">Manage Users</a>
    </div>
</div>

</body>
</html>