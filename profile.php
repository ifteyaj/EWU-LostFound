<?php
/**
 * User Profile Page
 */
require_once 'init.php';

// Require login
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - EWU Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-header {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius-card);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: var(--bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--text-secondary);
        }
        .profile-info h1 {
            margin-bottom: 0.5rem;
        }
        .profile-meta {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .dashboard-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius-card);
            box-shadow: var(--card-shadow);
        }
        .dashboard-card h3 {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-light);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <div class="profile-meta">
                    <div>User ID: <?php echo htmlspecialchars($user['student_id']); ?></div>
                    <div>Email: <?php echo htmlspecialchars($user['email']); ?></div>
                    <div>Member since: <?php echo date('M Y', strtotime($user['created_at'])); ?></div>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Stats</h3>
                <p>You have reported <strong>0</strong> lost items.</p>
                <p>You have reported <strong>0</strong> found items.</p>
            </div>
            <div class="dashboard-card">
                <h3>Recent Activity</h3>
                <p style="color: var(--text-secondary);">No recent activity to show.</p>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
