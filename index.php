<?php
/**
 * Homepage
 * Displays latest lost and found items
 */
require_once 'init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EWU Lost & Found - The official lost and found platform for East West University. Report and find lost items easily.">
    <title>EWU Lost & Found - Reuniting You With What Matters</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600&family=Sedgwick+Ave&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <header class="hero">
        <div class="container">
            <h1>Lost or Found Something <br><span>on Campus?</span></h1>
            <p style="font-family: 'Inter', sans-serif; letter-spacing: 0;">An official digital platform for reporting and recovering lost items within the East West University campus.</p>
            
            <form action="lost.php" method="GET" class="search-wrapper">
                <span class="search-icon">
                    <i class="ri-search-line"></i>
                </span>
                <input type="text" name="search" class="search-input" placeholder="Search lost items...">
            </form>
        </div>
    </header>

    <main class="container">
        <div class="section-header">
            <div class="header-content">
                <h2>Latest Report</h2>
                <p>Real-time feed of lost and found items.</p>
            </div>
            <a href="reports.php" class="view-all">View all</a>
        </div>
        
        <div class="items-grid">
            <?php
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
                                    <span class="card-location">📍 <?php echo htmlspecialchars($row['location']); ?></span>
                                    <span class="card-date">📅 <?php echo date('d F, Y', strtotime($row['event_date'])); ?></span>
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

        <!-- How It Works Section -->
        <section style="margin-top: 5rem; margin-bottom: 5rem;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2rem; font-weight: 800; color: #23336a; margin-bottom: 0.75rem;">How It Works</h2>
                <p style="color: var(--text-secondary); font-size: 1rem;">Simple steps to find or report items</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <!-- Step 1 -->
                <div style="text-align: center; padding: 2rem 1.5rem;">
                    <div style="width: 70px; height: 70px; background: #23336a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="ri-login-circle-line" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-head); margin-bottom: 0.75rem;">Sign in with Student Email</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">Only verified EWU students can access the platform.</p>
                </div>

                <!-- Step 2 -->
                <div style="text-align: center; padding: 2rem 1.5rem;">
                    <div style="width: 70px; height: 70px; background: #23336a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="ri-file-add-line" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-head); margin-bottom: 0.75rem;">Report Lost or Found Item</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">Submit item details with optional image.</p>
                </div>

                <!-- Step 3 -->
                <div style="text-align: center; padding: 2rem 1.5rem;">
                    <div style="width: 70px; height: 70px; background: #23336a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="ri-search-2-line" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-head); margin-bottom: 0.75rem;">Browse & Search Listings</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">Find matching items using filters.</p>
                </div>

                <!-- Step 4 -->
                <div style="text-align: center; padding: 2rem 1.5rem;">
                    <div style="width: 70px; height: 70px; background: #23336a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="ri-mail-send-line" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-head); margin-bottom: 0.75rem;">Contact & Recover</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">Reach out securely to recover the item.</p>
                </div>
            </div>
        </section>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
