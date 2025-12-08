<?php
/**
 * Lost Items Listing Page
 */
require_once 'init.php';

// Require login to view this page
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse all lost items reported at East West University. Search and find your missing belongings.">
    <title>Lost Items - EWU Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h2>Lost Items Database</h2>
                <p>Browsable list of all items reported missing.</p>
            </div>
            
            <form action="" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <select name="category" class="form-control" style="width: auto; padding: 0.75rem;">
                    <option value="">All Categories</option>
                    <?php 
                    $selectedCat = $_GET['category'] ?? '';
                    foreach (ITEM_CATEGORIES as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($selectedCat == $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <div class="search-wrapper" style="margin:0; width:250px;">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search items..." style="padding: 0.75rem 1rem 0.75rem 2.5rem;">
                </div>
                
                <button type="submit" class="btn-pill btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 0.9rem;">Filter</button>
                
                <?php if(!empty($_GET['search']) || !empty($_GET['category'])): ?>
                    <a href="lost.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="items-grid">
            <?php
            if ($conn && !$conn->connect_error) {
                // Base query
                $sql = "SELECT * FROM lost_items";
                $conditions = [];
                $params = [];
                $types = "";

                // Filter by Category
                if (!empty($_GET['category'])) {
                    $conditions[] = "category = ?";
                    $params[] = $_GET['category'];
                    $types .= "s";
                }

                // Search by Text
                if (!empty($_GET['search'])) {
                    $searchTerm = "%" . $_GET['search'] . "%";
                    $conditions[] = "(item_name LIKE ? OR description LIKE ? OR last_location LIKE ?)";
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                    $types .= "sss";
                }

                // Combine conditions
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(" AND ", $conditions);
                }

                $sql .= " ORDER BY created_at DESC";

                // Prepare statement
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $img_src = !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : '';
                        ?>
                        <a href="item.php?type=lost&id=<?php echo $row['id']; ?>" class="item-card">
                            <div class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted);">No Image</div>
                                <?php endif; ?>
                                <span class="status-badge status-lost">LOST</span>
                            </div>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($row['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-location"><?php echo htmlspecialchars($row['last_location']); ?></span>
                                    <span class="card-date"><?php echo date('d F, Y', strtotime($row['date_lost'])); ?></span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='empty-state'><h3>No lost items reported yet.</h3></div>";
                }
            } else {
                echo "<div class='empty-state'><h3>Database connection error.</h3></div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
