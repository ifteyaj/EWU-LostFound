<?php
/**
 * Item Detail Page
 * Displays details of a single lost or found item
 */
require_once 'init.php';

// Require login to view this page
requireLogin();

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$type = $_GET['type'];

// Validate type
if (!in_array($type, ['lost', 'found'])) {
    showError(404, "Invalid item type.");
}

$table = ($type == 'found') ? 'found_items' : 'lost_items';
$badge_class = ($type == 'found') ? 'status-found' : 'status-lost';
$badge_text = ($type == 'found') ? 'FOUND' : 'LOST';

if ($conn && !$conn->connect_error) {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
} else {
    $item = null;
}

if (!$item) {
    showError(404, "Item not found.");
}

$img_src = !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : '';
$location = ($type == 'found') ? $item['found_location'] : $item['last_location'];
$event_date = ($type == 'found') ? $item['date_found'] : $item['date_lost'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> - EWU Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
        <a href="javascript:history.back()" class="back-link">
            ← Back to Items
        </a>

        <div class="detail-card">
            <div class="detail-image">
                <?php if($img_src): ?>
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-weight:500;">No Image Available</div>
                <?php endif; ?>
                <span class="status-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
            </div>
            
            <div class="detail-info">
                <div class="detail-header">
                    <div class="detail-category"><?php echo htmlspecialchars($item['category']); ?></div>
                    <div class="detail-date"><?php echo date('d F, Y', strtotime($event_date)); ?></div>
                </div>
                
                <h1 class="detail-title"><?php echo htmlspecialchars($item['item_name']); ?></h1>
                
                <div class="detail-section">
                    <div class="detail-icon">📍</div>
                    <div>
                        <div class="detail-label">Location</div>
                        <div class="detail-value"><?php echo htmlspecialchars($location); ?></div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-icon">≡</div>
                    <div>
                        <div class="detail-label">Description</div>
                        <div class="detail-value"><?php echo nl2br(htmlspecialchars($item['description'])); ?></div>
                    </div>
                </div>

                <div class="detail-actions">
                    <div class="detail-actions-label">ACTIONS</div>
                    <!-- University Office Instruction (Visible to All) -->
                    <div style="background: var(--bg-light); border: 1px solid var(--border-light); padding: 1.5rem; border-radius: 12px; text-align: center; width: 100%; margin-bottom: 1rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🏛️</div>
                        <h3 style="font-size: 1.1rem; color: var(--primary-navy); margin-bottom: 0.5rem;">University Lost & Found Office</h3>
                        <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem; line-height: 1.5;">
                            <?php if($type == 'lost'): ?>
                                <strong style="color: var(--text-dark);">Found this item?</strong><br>
                                Please hand it over to the <strong>Registrar's Office</strong> so the owner can collect it safely.
                            <?php else: ?>
                                <strong style="color: var(--text-dark);">Is this yours?</strong><br>
                                Please visit the <strong>Registrar's Office</strong> to verify ownership and claim your item.
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="detail-buttons">
                        <?php 
                        $currentUserId = getCurrentUserId();
                        $isOwner = ($item['user_id'] == $currentUserId);
                        
                        if ($isOwner): 
                        ?>
                            <!-- Owner Actions -->
                            <a href="handlers/manage_item.php?action=delete&type=<?php echo $type; ?>&id=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>" 
                               onclick="return confirm('Are you sure you want to delete this report? This action cannot be undone.');"
                               class="btn-pill btn-lg" style="background-color: var(--status-lost-bg); border-color: var(--status-lost-bg); color: white;">
                                Delete Report
                            </a>
                            
                            <?php if($item['status'] != 'resolved'): ?>
                                <a href="handlers/manage_item.php?action=resolve&type=<?php echo $type; ?>&id=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>"
                                   class="btn-pill btn-lg" style="background-color: var(--status-found-bg); border-color: var(--status-found-bg); color: white;">
                                    Mark as <?php echo ($type == 'lost') ? 'Found' : 'Returned'; ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <button class="btn-pill btn-outline" onclick="navigator.share ? navigator.share({title: '<?php echo htmlspecialchars($item['item_name']); ?>', url: window.location.href}) : alert('Link copied!')">
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
