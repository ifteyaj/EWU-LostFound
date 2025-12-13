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
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 10rem; padding-bottom: 4rem;">
        <!-- Profile Header with Gradient Background -->
        <div style="background: #23336a; border-radius: 24px; padding: 3rem 2rem; margin-bottom: 3rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; transform: translate(30%, -30%);"></div>
            <div style="position: absolute; bottom: 0; left: 0; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; transform: translate(-30%, 30%);"></div>
            
            <div style="position: relative; z-index: 10; display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                <!-- Avatar -->
                <div style="width: 100px; height: 100px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; color: #23336a; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                
                <!-- User Info -->
                <div style="flex: 1;">
                    <h1 style="color: white; font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    <div style="display: flex; gap: 2rem; flex-wrap: wrap; color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                        <div><i class="ri-user-line"></i> <?php echo htmlspecialchars($user['student_id']); ?></div>
                        <div><i class="ri-mail-line"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                        <div><i class="ri-calendar-line"></i> Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></div>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <a href="<?php echo APP_URL; ?>/auth/logout.php" 
                   class="btn-pill" 
                   style="background: rgba(255,255,255,0.2); 
                          color: white; 
                          border: 2px solid rgba(255,255,255,0.3); 
                          padding: 0.8rem 2rem; 
                          font-weight: 600;
                          backdrop-filter: blur(10px);
                          transition: all 0.3s ease;"
                   onmouseover="this.style.background='rgba(255,255,255,0.3)'; this.style.borderColor='rgba(255,255,255,0.5)';"
                   onmouseout="this.style.background='rgba(255,255,255,0.2)'; this.style.borderColor='rgba(255,255,255,0.3)';">
                    <i class="ri-logout-box-line"></i> Logout
                </a>
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

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <!-- Lost Reports -->
            <div style="background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #E2E8F0;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px;">Lost Reports</div>
                        <div style="font-size: 2.5rem; font-weight: 800; color: #EF4444; margin-top: 0.5rem;"><?php echo count($lostItems); ?></div>
                    </div>
                    <div style="width: 50px; height: 50px; background: #23336a; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-alarm-warning-line" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                </div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0;">Items you've reported as lost</p>
            </div>

            <!-- Found Reports -->
            <div style="background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #E2E8F0;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px;">Found Reports</div>
                        <div style="font-size: 2.5rem; font-weight: 800; color: #10B981; margin-top: 0.5rem;"><?php echo count($foundItems); ?></div>
                    </div>
                    <div style="width: 50px; height: 50px; background: #23336a; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-checkbox-circle-line" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                </div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0;">Items you've found and reported</p>
            </div>

            <!-- Account Status -->
            <div style="background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #E2E8F0;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px;">Account Status</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: var(--text-head); margin-top: 0.5rem;">
                            <?php if($user['is_verified']): ?>
                                <span style="color: #10B981;">✅ Verified</span>
                            <?php else: ?>
                                <span style="color: #EF4444;">⚠️ Unverified</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="width: 50px; height: 50px; background: #23336a; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-shield-check-line" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                </div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0;">
                    Last login: <?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?>
                </p>
            </div>
        </div>

        <!-- My Reports Section -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #23336a; margin: 0;">My Reports</h2>
                <a href="post_item.php" class="btn-pill btn-primary" style="padding: 0.8rem 1.8rem;">
                    <i class="ri-add-line"></i> New Report
                </a>
            </div>
            
            <?php if(empty($lostItems) && empty($foundItems)): ?>
                <div style="text-align: center; padding: 4rem 2rem; background: #F8FAFC; border-radius: 16px; border: 2px dashed #E2E8F0;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
                    <h3 style="color: var(--text-head); margin-bottom: 0.5rem;">No Reports Yet</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Start helping the community by reporting lost or found items.</p>
                    <a href="post_item.php" class="btn-pill btn-primary">Report Your First Item</a>
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
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size: 3rem; background: #F8FAFC;">📦</div>
                                <?php endif; ?>
                                <span class="status-badge status-lost">LOST</span>
                            </a>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($item['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-date"><?php echo date('d M, Y', strtotime($item['date_lost'])); ?></span>
                                    <span style="font-weight: 600; font-size: 0.75rem; text-transform:uppercase; padding: 0.25rem 0.5rem; border-radius: 4px; background: <?php echo ($item['status'] == 'resolved') ? '#D1FAE5' : '#F3F4F6'; ?>; color: <?php echo ($item['status'] == 'resolved') ? '#10B981' : '#6B7280'; ?>;">
                                        <?php echo htmlspecialchars($item['status']); ?>
                                    </span>
                                </div>
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light); display: flex; gap: 0.5rem;">
                                    <?php if($item['status'] != 'resolved'): ?>
                                    <a href="handlers/manage_item.php?action=resolve&type=lost&id=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>"
                                       class="btn-pill" style="flex: 1; font-size: 0.8rem; padding: 0.6rem; background: #10B981; color: white; border: none;">
                                        <i class="ri-check-line"></i> Mark Found
                                    </a>
                                    <?php endif; ?>
                                    <a href="handlers/manage_item.php?action=delete&type=lost&id=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>" 
                                       onclick="return confirm('Are you sure you want to delete this item?');"
                                       class="btn-pill" style="flex: 1; font-size: 0.8rem; padding: 0.6rem; background: #FEE2E2; color: #EF4444; border: none;">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </a>
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
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size: 3rem; background: #F8FAFC;">📦</div>
                                <?php endif; ?>
                                <span class="status-badge status-found">FOUND</span>
                            </a>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($item['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-date"><?php echo date('d M, Y', strtotime($item['date_found'])); ?></span>
                                    <span style="font-weight: 600; font-size: 0.75rem; text-transform:uppercase; padding: 0.25rem 0.5rem; border-radius: 4px; background: <?php echo ($item['status'] == 'resolved') ? '#D1FAE5' : '#F3F4F6'; ?>; color: <?php echo ($item['status'] == 'resolved') ? '#10B981' : '#6B7280'; ?>;">
                                        <?php echo htmlspecialchars($item['status']); ?>
                                    </span>
                                </div>
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light); display: flex; gap: 0.5rem;">
                                    <?php if($item['status'] != 'resolved'): ?>
                                    <a href="handlers/manage_item.php?action=resolve&type=found&id=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>"
                                       class="btn-pill" style="flex: 1; font-size: 0.8rem; padding: 0.6rem; background: #10B981; color: white; border: none;">
                                        <i class="ri-check-line"></i> Mark Returned
                                    </a>
                                    <?php endif; ?>
                                    <a href="handlers/manage_item.php?action=delete&type=found&id=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>" 
                                       onclick="return confirm('Are you sure you want to delete this item?');"
                                       class="btn-pill" style="flex: 1; font-size: 0.8rem; padding: 0.6rem; background: #FEE2E2; color: #EF4444; border: none;">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </a>
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
