<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWU Lost & Found - Web3 Edition</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="index.php">EWU Lost&Found</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="lost.php">Latest Items</a></li>
                <li><a href="#">My Account</a></li>
                <!-- CTA -->
                <li><a href="post_item.php" class="btn-pill">Report Lost Item</a></li>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1>Reuniting You With<br>What Matters.</h1>
            <p>Report lost items or help others find theirs. A community-driven platform for East West University students.</p>
            
            <div class="search-wrapper">
                <input type="text" class="glass-input" placeholder="Search for IDs, Keys, Electronics...">
            </div>
        </div>
    </header>

    <main class="container">
        <h2 style="margin-bottom: 2rem; font-weight: 500;">Latest Reports</h2>
        
        <div class="items-grid">
            <?php
            include 'config/db.php';
            // Combined query for latest items (simple view)
            if ($conn && !$conn->connect_error) {
                // Determine status badge helper
                function get_badge($type) {
                    return $type == 'lost' ? '<span class="status-badge status-lost">Lost</span>' : '<span class="status-badge status-found">Found</span>';
                }

                $sql = "(SELECT id, item_name, last_location as location, 'lost' as type, image, date_lost as event_date, created_at FROM lost_items) 
                        UNION 
                        (SELECT id, item_name, found_location as location, 'found' as type, image, date_found as event_date, created_at FROM found_items) 
                        ORDER BY created_at DESC LIMIT 6";
                
                $result = $conn->query($sql);
                
                if ($result) {
                    while($row = $result->fetch_assoc()) {
                        $img = !empty($row['image']) ? 'uploads/'.$row['image'] : null;
                        ?>
                        <div class="item-card glass-card">
                            <div class="card-img">
                                <?php if($img): ?>
                                    <img src="<?php echo $img; ?>" alt="Item">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.3);">No Image</div>
                                <?php endif; ?>
                            </div>
                            <?php echo get_badge($row['type']); ?>
                            <h3 class="card-title" style="margin-top: 0.5rem;"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                            <span class="card-location">Location: <?php echo htmlspecialchars($row['location']); ?></span>
                            <span class="card-location" style="margin-top:0;">Date: <?php echo date('M d, Y', strtotime($row['event_date'])); ?></span>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
    </main>
</body>
</html>
