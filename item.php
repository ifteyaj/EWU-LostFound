<?php
include 'config/db.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$type = $_GET['type'];
$table = ($type == 'found') ? 'found_items' : 'lost_items';
$badge_class = ($type == 'found') ? 'badge-found' : 'badge-status';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> - Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="index.php">EWU Lost&Found</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="lost.php">Latest Items</a></li>
                <li><a href="#">My Account</a></li>
                <!-- CTA -->
                <li><a href="post_item.php" class="btn-pill">Report Lost Item</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        <a href="javascript:history.back()" style="color:var(--text-grey); display:inline-flex; align-items:center; gap:0.5rem; margin-bottom: 1.5rem;">
            &larr; Back to List
        </a>

        <div class="glass-card" style="padding: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <div class="detail-image-container">
                <?php if($img_src): ?>
                    <img src="<?php echo $img_src; ?>" alt="Item Image" style="width:100%; border-radius: 12px; height: auto; object-fit: cover;">
                <?php else: ?>
                    <div style="width:100%; aspect-ratio: 4/3; background:rgba(0,0,0,0.2); border-radius: 12px; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.3); font-weight:500;">No Image Available</div>
                <?php endif; ?>
            </div>
            
            <div class="detail-info">
                <div style="margin-bottom: 1rem;">
                    <span class="<?php echo $type == 'found' ? 'status-badge status-found' : 'status-badge status-lost'; ?>"><?php echo $badge_text; ?></span>
                    <span style="background: rgba(255,255,255,0.1); color: var(--text-white); padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-left: 0.5rem;">
                        <?php echo htmlspecialchars($item['category']); ?>
                    </span>
                </div>
                
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($item['item_name']); ?></h1>
                
                <div style="font-size: 1.1rem; color: var(--text-grey); line-height: 1.8; margin-bottom: 2rem;">
                    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                </div>

                <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-radius: 16px; border: var(--glass-border);">
                    <h3 style="margin-bottom: 1rem; font-size: 1rem; color: var(--text-white);">Item Details</h3>
                    
                    <?php if($type == 'lost'): ?>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:0.5rem;">
                            <span style="color:var(--text-grey);">Last Seen At</span>
                            <span><?php echo htmlspecialchars($item['last_location']); ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:0.5rem;">
                            <span style="color:var(--text-grey);">Date Lost</span>
                            <span><?php echo date('F d, Y', strtotime($item['date_lost'])); ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-grey);">Posted By</span>
                            <span><?php echo htmlspecialchars($item['student_name']); ?></span>
                        </div>
                    <?php else: ?>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:0.5rem;">
                            <span style="color:var(--text-grey);">Found At</span>
                            <span><?php echo htmlspecialchars($item['found_location']); ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:0.5rem;">
                            <span style="color:var(--text-grey);">Date Found</span>
                            <span><?php echo date('F d, Y', strtotime($item['date_found'])); ?></span>
                        </div>
                         <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-grey);">Finder</span>
                            <span><?php echo htmlspecialchars($item['finder_name']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 2rem;">
                    <a href="mailto:<?php echo htmlspecialchars($item['email']); ?>" class="btn-pill" style="text-align: center; width: 100%; display:block; border-radius: 12px;">
                        Contact via Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
