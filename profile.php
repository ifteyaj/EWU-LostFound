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

        <?php
        $userId = $user['id'];
        
        // Fetch Lost Items
        $lostItems = [];
        $stmt = $conn->prepare("SELECT * FROM lost_items WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $lostItems[] = $row;
        }
        $stmt->close();
        
        // Fetch Found Items
        $foundItems = [];
        $stmt = $conn->prepare("SELECT * FROM found_items WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $foundItems[] = $row;
        }
        $stmt->close();
        ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Stats</h3>
                <div style="display: flex; gap: 2rem;">
                    <div>
                        <div style="font-size: 2rem; font-weight: 700; color: var(--status-lost);"><?php echo count($lostItems); ?></div>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">Lost Reports</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 700; color: var(--status-found);"><?php echo count($foundItems); ?></div>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">Found Reports</div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3>Account Status</h3>
                <p>
                    <span style="font-weight: 600;">Verification:</span> 
                    <?php if($user['is_verified']): ?>
                        <span style="color: var(--status-found);">Verified ✅</span>
                    <?php else: ?>
                        <span style="color: var(--status-lost);">Unverified</span>
                    <?php endif; ?>
                </p>
                <p>
                    <span style="font-weight: 600;">Last Login:</span> 
                    <?php echo $user['last_login'] ? date('M d, Y h:i A', strtotime($user['last_login'])) : 'Never'; ?>
                </p>
            </div>
        </div>

        <div style="margin-top: 3rem;">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem; display: inline-block;">My Reports</h2>
            
            <?php if(empty($lostItems) && empty($foundItems)): ?>
                <div class="empty-state">
                    <h3>You haven't reported any items yet.</h3>
                    <a href="post_item.php" class="btn-pill" style="margin-top: 1rem;">Report an Item</a>
                </div>
            <?php else: ?>
                <div class="items-grid">
                    <?php foreach($lostItems as $item): 
                        $img_src = !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : '';
                    ?>
                        <div class="item-card">
                            <a href="item.php?type=lost&id=<?php echo $item['id']; ?>" class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted);">No Image</div>
                                <?php endif; ?>
                                <span class="status-badge status-lost">LOST</span>
                            </a>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($item['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-date"><?php echo date('d M, Y', strtotime($item['date_lost'])); ?></span>
                                    <span style="font-weight: 600; font-size: 0.8rem; text-transform:uppercase; color: <?php echo ($item['status'] == 'resolved') ? 'var(--status-found)' : 'var(--text-muted)'; ?>">
                                        <?php echo htmlspecialchars($item['status']); ?>
                                    </span>
                                </div>
                                <div style="margin-top: 1rem; border-top: 1px solid var(--border-light); padding-top: 1rem; display: flex; gap: 0.5rem;">
                                    <a href="handlers/manage_item.php?action=delete&type=lost&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this item?');"
                                       class="btn-pill btn-outline" style="font-size: 0.8rem; padding: 0.5rem; color: var(--status-lost); border-color: var(--status-lost);">
                                        Delete
                                    </a>
                                    <?php if($item['status'] != 'resolved'): ?>
                                    <a href="handlers/manage_item.php?action=resolve&type=lost&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>"
                                       class="btn-pill btn-outline" style="font-size: 0.8rem; padding: 0.5rem; color: var(--status-found); border-color: var(--status-found);">
                                        Mark Found
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach($foundItems as $item): 
                        $img_src = !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : '';
                    ?>
                        <div class="item-card">
                            <a href="item.php?type=found&id=<?php echo $item['id']; ?>" class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted);">No Image</div>
                                <?php endif; ?>
                                <span class="status-badge status-found">FOUND</span>
                            </a>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($item['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-date"><?php echo date('d M, Y', strtotime($item['date_found'])); ?></span>
                                    <span style="font-weight: 600; font-size: 0.8rem; text-transform:uppercase; color: <?php echo ($item['status'] == 'resolved') ? 'var(--status-found)' : 'var(--text-muted)'; ?>">
                                        <?php echo htmlspecialchars($item['status']); ?>
                                    </span>
                                </div>
                                <div style="margin-top: 1rem; border-top: 1px solid var(--border-light); padding-top: 1rem; display: flex; gap: 0.5rem;">
                                    <a href="handlers/manage_item.php?action=delete&type=found&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this item?');"
                                       class="btn-pill btn-outline" style="font-size: 0.8rem; padding: 0.5rem; color: var(--status-lost); border-color: var(--status-lost);">
                                        Delete
                                    </a>
                                    <?php if($item['status'] != 'resolved'): ?>
                                    <a href="handlers/manage_item.php?action=resolve&type=found&id=<?php echo $item['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>"
                                       class="btn-pill btn-outline" style="font-size: 0.8rem; padding: 0.5rem; color: var(--status-found); border-color: var(--status-found);">
                                        Mark Returned
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
