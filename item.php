<?php
include 'config/db.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$type = $_GET['type'];
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
    die("Item not found or database error.");
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <div class="logo-icon">🏠</div>
                <div class="logo-text">
                    <span>EWU</span>
                    LOST &<br>FOUND
                </div>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#">My Account</a></li>
                <li><button class="theme-toggle" title="Toggle theme">🌙</button></li>
                <li><a href="post_item.php" class="btn-pill">Report Item</a></li>
            </ul>
        </div>
    </nav>

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
                    <div class="detail-buttons">
                        <?php if($type == 'lost'): ?>
                            <a href="mailto:<?php echo htmlspecialchars($item['email']); ?>?subject=Found Your Item: <?php echo urlencode($item['item_name']); ?>" class="btn-pill btn-lg">
                                I Found This!
                            </a>
                        <?php else: ?>
                            <a href="mailto:<?php echo htmlspecialchars($item['email']); ?>?subject=Claiming Lost Item: <?php echo urlencode($item['item_name']); ?>" class="btn-pill btn-lg">
                                This is Mine!
                            </a>
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
