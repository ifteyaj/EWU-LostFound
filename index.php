<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWU Lost & Found - Reuniting You With What Matters</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li class="logo-item"><a href="index.php" class="logo"><img src="assets/img/logo.png" alt="EWU Lost & Found"></a></li>
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="#">My Account</a></li>
                <li><a href="post_item.php" class="btn-pill">Report Item</a></li>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1><em>Reuniting You With</em><br><span class="highlight"><em>What Matters.</em></span></h1>
            <p>The official decentralized lost and found layer for East West University. Secure, transparent, and built for students.</p>
            
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search for lost items...">
            </div>
        </div>
    </header>

    <main class="container">
        <div class="section-header">
            <div>
                <h2>Latest Reports</h2>
                <p>Real-time feed of lost and found items.</p>
            </div>
            <a href="lost.php" class="view-all">View All →</a>
        </div>
        
        <div class="items-grid">
            <?php
            include 'config/db.php';
            
            if ($conn && !$conn->connect_error) {
                $sql = "(SELECT id, item_name, category, last_location as location, 'lost' as type, image, date_lost as event_date, created_at FROM lost_items) 
                        UNION 
                        (SELECT id, item_name, category, found_location as location, 'found' as type, image, date_found as event_date, created_at FROM found_items) 
                        ORDER BY created_at DESC LIMIT 4";
                
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $img = !empty($row['image']) ? 'uploads/'.$row['image'] : null;
                        $badge_class = $row['type'] == 'lost' ? 'status-lost' : 'status-found';
                        $badge_text = $row['type'] == 'lost' ? 'LOST' : 'FOUND';
                        ?>
                        <a href="item.php?type=<?php echo $row['type']; ?>&id=<?php echo $row['id']; ?>" class="item-card">
                            <div class="card-img">
                                <?php if($img): ?>
                                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted);">No Image</div>
                                <?php endif; ?>
                                <span class="status-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                            </div>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($row['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-location"><?php echo htmlspecialchars($row['location']); ?></span>
                                    <span class="card-date"><?php echo date('d F, Y', strtotime($row['event_date'])); ?></span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='empty-state'><h3>No items reported yet. Be the first to help!</h3></div>";
                }
            } else {
                echo "<div class='empty-state'><h3>Database connection error. Please try again later.</h3></div>";
            }
            ?>
        </div>
    </main>
</body>
</html>
