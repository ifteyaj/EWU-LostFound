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
                <span class="search-icon" style="display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M18.031 16.6168L22.3137 20.8995L20.8995 22.3137L16.6168 18.031C15.0769 19.263 13.124 20 11 20C6.032 20 2 15.968 2 11C2 6.032 6.032 2 11 2C15.968 2 20 6.032 20 11C20 13.124 19.263 15.0769 18.031 16.6168ZM16.0247 15.8748C17.2475 14.6146 18 12.8956 18 11C18 7.1325 14.8675 4 11 4C7.1325 4 4 7.1325 4 11C4 14.8675 7.1325 18 11 18C12.8956 18 14.6146 17.2475 15.8748 16.0247L16.0247 15.8748Z"></path></svg>
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
            <a href="reports.php" class="btn-pill" style="background: #F8FAFC; color: #334155; padding: 0.6rem 1.5rem; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;">View All</a>
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
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size: 2rem;">
                                        <i class="ri-box-3-line"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="status-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                            </div>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($row['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <div class="card-meta" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <span class="card-location" style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.14876 5.63604 4.63404C9.15076 1.11932 14.8513 1.11932 18.366 4.63404C21.8807 8.14876 21.8807 13.8492 18.366 17.364L12 23.7279ZM16.9518 15.9497C19.6855 13.2161 19.6855 8.78392 16.9518 6.05025C14.2181 3.31658 9.78596 3.31658 7.05228 6.05025C4.31861 8.78392 4.31861 13.2161 7.05228 15.9497L12 20.8975L16.9518 15.9497ZM12 13C10.8954 13 10 12.1046 10 11C10 9.89543 10.8954 9 12 9C13.1046 9 14 9.89543 14 11C14 12.1046 13.1046 13 12 13Z"></path></svg>
                                        <?php echo htmlspecialchars($row['location']); ?>
                                    </span>
                                    <span class="card-date" style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H7V1H9V3H15V1H17V3ZM4 9V19H20V9H4ZM6 11H8V13H6V11ZM11 11H13V13H11V11ZM16 11H18V13H16V11Z"></path></svg>
                                        <?php echo date('d F, Y', strtotime($row['event_date'])); ?>
                                    </span>
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
