<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Items - EWU Lost & Found</title>
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
                <li><a href="lost.php" class="active">Latest Items</a></li>
                <li><a href="#">My Account</a></li>
                <!-- CTA -->
                <li><a href="post_item.php" class="btn-pill">Report Lost Item</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        <div class="section-header" style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 2rem;">
            <div>
                <h2>Lost Items Database</h2>
                <p style="color: var(--text-grey); margin-top: 0.5rem;">Browsable list of all items reported missing.</p>
            </div>
            
            <div class="search-wrapper" style="margin:0; width:300px;">
                <input type="text" class="glass-input" placeholder="Search items...">
            </div>
        </div>

        <div class="items-grid">
            <?php
            include 'config/db.php';
            
            if ($conn && !$conn->connect_error) {
                $sql = "SELECT * FROM lost_items ORDER BY created_at DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $img_src = !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : '';
                        ?>
                        <div class="item-card glass-card">
                            <div class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="Item Image">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.3);">No Image</div>
                                <?php endif; ?>
                            </div>
                            <span class="status-badge status-lost">Lost</span>
                            <h3 class="card-title" style="margin-top:0.5rem;"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                            <span class="card-location">Location: <?php echo htmlspecialchars($row['last_location']); ?></span>
                            <span class="card-location" style="margin-top:0;">Date: <?php echo date('M d, Y', strtotime($row['date_lost'])); ?></span>
                            
                            <div style="margin-top: 1rem;">
                                <a href="item.php?type=lost&id=<?php echo $row['id']; ?>" class="btn-pill" style="font-size:0.8rem; padding:0.5rem 1rem;">Details</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='glass-card' style='grid-column: 1/-1; text-align:center; padding: 4rem; color: var(--text-grey);'><h3>No lost items reported yet.</h3></div>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
