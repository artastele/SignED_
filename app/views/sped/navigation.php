<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPED Navigation - SignED</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css">
    <style>
        /* SPED Navigation Styles */
        .sped-nav-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #fef2f2, #eff6ff);
            font-family: Arial, sans-serif;
            padding: 2rem;
        }

        .sped-nav-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .sped-nav-title {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #111827;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sped-nav-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .sped-nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sped-nav-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
            border: 1px solid #e5e7eb;
        }

        .sped-nav-section-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #111827;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sped-nav-section-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        .sped-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sped-nav-item {
            margin-bottom: 0.75rem;
        }

        .sped-nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
            transition: all 0.3s ease;
            position: relative;
        }

        .sped-nav-link:hover {
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(185, 28, 60, 0.3);
        }

        .sped-nav-link-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sped-nav-link-icon {
            width: 24px;
            height: 24px;
            background: #e5e7eb;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .sped-nav-link:hover .sped-nav-link-icon {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .sped-nav-link-text {
            font-weight: 600;
        }

        .sped-nav-badge {
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            min-width: 1.2rem;
            text-align: center;
            font-weight: bold;
        }

        .sped-nav-link:hover .sped-nav-badge {
            background: rgba(255,255,255,0.3);
        }

        .sped-nav-submenu {
            margin-top: 0.5rem;
            padding-left: 1rem;
            border-left: 2px solid #e5e7eb;
        }

        .sped-nav-submenu-item {
            margin-bottom: 0.5rem;
        }

        .sped-nav-submenu-link {
            display: block;
            padding: 0.5rem 0.75rem;
            background: #f3f4f6;
            border-radius: 6px;
            text-decoration: none;
            color: #6b7280;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .sped-nav-submenu-link:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .sped-back-btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #B91C3C, #1E40AF);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .sped-back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(185, 28, 60, 0.3);
        }

        @media (max-width: 768px) {
            .sped-nav-container {
                padding: 1rem;
            }
            
            .sped-nav-grid {
                grid-template-columns: 1fr;
            }
            
            .sped-nav-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<div class="sped-nav-container">
    <a href="<?php echo URLROOT; ?>/sped/dashboard" class="sped-back-btn">← Back to Dashboard</a>
    
    <div class="sped-nav-header">
        <h1 class="sped-nav-title">SPED Navigation</h1>
        <p class="sped-nav-subtitle">Quick access to all SPED workflow functions</p>
    </div>

    <div class="sped-nav-grid">
        <?php if (isset($data['navigation'])): ?>
            <?php 
            // Group navigation items by category
            $categories = [
                'Dashboard' => ['dashboard'],
                'Enrollment Management' => ['verification', 'enrollments'],
                'Assessment & IEP' => ['assessment', 'iep', 'meetings', 'approval'],
                'Learning & Materials' => ['materials', 'progress'],
                'Administration' => ['users', 'statistics', 'audit', 'reports'],
                'User Account' => ['profile', 'logout']
            ];
            
            foreach ($categories as $categoryName => $categoryIcons):
                $categoryItems = array_filter($data['navigation'], function($item) use ($categoryIcons) {
                    return isset($item['icon']) && in_array($item['icon'], $categoryIcons);
                });
                
                if (!empty($categoryItems)):
            ?>
                <div class="sped-nav-section">
                    <h2 class="sped-nav-section-title">
                        <div class="sped-nav-section-icon">
                            <?php 
                            $categoryIconMap = [
                                'Dashboard' => '🏠',
                                'Enrollment Management' => '📋',
                                'Assessment & IEP' => '📄',
                                'Learning & Materials' => '📚',
                                'Administration' => '⚙️',
                                'User Account' => '👤'
                            ];
                            echo $categoryIconMap[$categoryName] ?? '📋';
                            ?>
                        </div>
                        <?php echo $categoryName; ?>
                    </h2>
                    
                    <ul class="sped-nav-list">
                        <?php foreach ($categoryItems as $item): ?>
                            <li class="sped-nav-item">
                                <a href="<?php echo $item['url']; ?>" 
                                   class="sped-nav-link <?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                                    <div class="sped-nav-link-content">
                                        <div class="sped-nav-link-icon">
                                            <?php 
                                            $iconMap = [
                                                'dashboard' => '🏠',
                                                'verification' => '✓',
                                                'assessment' => '📝',
                                                'iep' => '📄',
                                                'meetings' => '📅',
                                                'approval' => '✅',
                                                'materials' => '📚',
                                                'progress' => '📊',
                                                'users' => '👥',
                                                'statistics' => '📈',
                                                'audit' => '🔍',
                                                'reports' => '📋',
                                                'enrollments' => '📋',
                                                'profile' => '👤',
                                                'logout' => '🚪'
                                            ];
                                            echo $iconMap[$item['icon']] ?? '📋';
                                            ?>
                                        </div>
                                        <span class="sped-nav-link-text"><?php echo htmlspecialchars($item['title']); ?></span>
                                    </div>
                                    <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                                        <span class="sped-nav-badge"><?php echo $item['badge']; ?></span>
                                    <?php endif; ?>
                                </a>
                                
                                <?php if (isset($item['submenu']) && !empty($item['submenu'])): ?>
                                    <div class="sped-nav-submenu">
                                        <?php foreach ($item['submenu'] as $subItem): ?>
                                            <div class="sped-nav-submenu-item">
                                                <a href="<?php echo $subItem['url']; ?>" class="sped-nav-submenu-link">
                                                    <?php echo htmlspecialchars($subItem['title']); ?>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        <?php else: ?>
            <div class="sped-nav-section">
                <h2 class="sped-nav-section-title">
                    <div class="sped-nav-section-icon">❌</div>
                    No Navigation Available
                </h2>
                <p>Navigation items could not be loaded. Please contact your system administrator.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>