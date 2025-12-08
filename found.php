<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found Items - EWU Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <img src="assets/img/logo.png" alt="EWU Lost & Found">
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#">My Account</a></li>
                <li><a href="post_item.php" class="btn-pill">Report Item</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h2>Found Items</h2>
                <p>Items that have been found and are waiting to be claimed.</p>
            </div>
            
            <div class="search-wrapper" style="margin:0; width:300px;">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search items..." style="padding: 0.75rem 1rem 0.75rem 2.5rem;">
            </div>
        </div>

        <div class="items-grid">
            <?php
            include 'config/db.php';
            
            if ($conn && !$conn->connect_error) {
                $sql = "SELECT * FROM found_items ORDER BY created_at DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $img_src = !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : '';
                        ?>
                        <a href="item.php?type=found&id=<?php echo $row['id']; ?>" class="item-card">
                            <div class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted);">No Image</div>
                                <?php endif; ?>
                                <span class="status-badge status-found">FOUND</span>
                            </div>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($row['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-location"><?php echo htmlspecialchars($row['found_location']); ?></span>
                                    <span class="card-date"><?php echo date('d F, Y', strtotime($row['date_found'])); ?></span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='empty-state'><h3>No found items reported yet.</h3></div>";
                }
            } else {
                echo "<div class='empty-state'><h3>Database connection error.</h3></div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
