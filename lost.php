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
        <!-- Search Form -->
        <form action="" method="GET" class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h2>Lost Items Database</h2>
                <p>Browsable list of all items reported missing.</p>
            </div>
            
            <div class="search-wrapper" style="margin:0; width:300px;">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Search items..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                       style="padding: 0.75rem 1rem 0.75rem 2.5rem;">
                <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <a href="lost.php" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:var(--text-muted);">&times;</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="items-grid">
            <?php
            require_once 'includes/pagination.php';

            // Pagination setup
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $itemsPerPage = 12;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            // Build Query Params
            $urlParams = [];
            if (!empty($search)) {
                $urlParams['search'] = $search;
            }

            if ($conn && !$conn->connect_error) {
                // 1. Get Total Count
                $countSql = "SELECT COUNT(*) as total FROM lost_items";
                $whereClause = "";
                $params = [];
                $types = "";

                if (!empty($search)) {
                    $whereClause = " WHERE item_name LIKE ? OR category LIKE ? OR description LIKE ?";
                    $searchTerm = "%{$search}%";
                    $params = [$searchTerm, $searchTerm, $searchTerm];
                    $types = "sss";
                }

                $stmt = $conn->prepare($countSql . $whereClause);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $totalResult = $stmt->get_result();
                $totalItems = $totalResult->fetch_assoc()['total'];
                $stmt->close();

                // 2. Initialize Pagination
                $pagination = new Pagination($totalItems, $itemsPerPage, $page, $urlParams);
                $limit = $pagination->getLimit();
                $offset = $pagination->getOffset();

                // 3. Fetch Items
                $sql = "SELECT * FROM lost_items" . $whereClause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $types .= "ii";
                    $params[] = $limit;
                    $params[] = $offset;
                    $stmt->bind_param($types, ...$params);
                } else {
                    $stmt->bind_param("ii", $limit, $offset);
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
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); background:var(--bg-light);">
                                        <span style="font-size:2rem;">📦</span>
                                    </div>
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
                    echo "<div class='empty-state'><h3>No lost items found matching your search.</h3></div>";
                }
                $stmt->close();
            } else {
                echo "<div class='empty-state'><h3>Database connection error.</h3></div>";
            }
            ?>
        </div>

        <?php 
        // Display Pagination Links
        if (isset($pagination)) {
            echo $pagination->getLinks(); 
        }
        ?>
    </div>
</body>
</html>
