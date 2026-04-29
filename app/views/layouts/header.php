<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['page_title'] ?? 'SignED SPED System'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS; ?>/css/custom.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS; ?>/images/SIGNED%20LOGO.png">
    
    <!-- Bootstrap 5 JS Bundle (moved to head for better compatibility) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="bg-light">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient sticky-top shadow-sm">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo URLROOT; ?>">
                <img src="<?php echo ASSETS; ?>/images/SIGNED%20LOGO.png" 
                     alt="SignED Logo" 
                     height="60" 
                     class="me-2"
                     onerror="this.style.display='none'">
                <div>
                    <div class="fw-bold">SignED SPED</div>
                    <small class="d-none d-md-block" style="font-size: 0.7rem; opacity: 0.9;">Special Education Management</small>
                </div>
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Right Side Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Notifications -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <?php if (isset($data['unread_notifications']) && $data['unread_notifications'] > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $data['unread_notifications']; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 300px;">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-muted text-center py-3" href="#">No new notifications</a></li>
                        </ul>
                    </li>
                    
                    <!-- User Profile -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle me-2">
                                <?php echo strtoupper(substr($data['user_name'] ?? 'U', 0, 1)); ?>
                            </div>
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($data['user_name'] ?? 'User'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><h6 class="dropdown-header">
                                <?php echo htmlspecialchars($data['user_name'] ?? 'User'); ?><br>
                                <small class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $data['role'] ?? '')); ?></small>
                            </h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/user/profile"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/user/settings"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo URLROOT; ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Container -->
    <div class="container-fluid">
        <div class="row">
