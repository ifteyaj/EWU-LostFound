<?php
/**
 * Found Items Listing Page
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
    <meta name="description" content="Browse found items at East West University. Claim your lost belongings today.">
    <title>Found Items - EWU Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h2>Found Items</h2>
                <p>Items that have been found and are waiting to be claimed.</p>
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
                    <a href="found.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="items-grid">
            <?php
            if ($conn && !$conn->connect_error) {
                // Pagination Setup
                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $limit = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 12;
                $offset = ($page - 1) * $limit;

                // Base Conditions
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
                    $conditions[] = "(item_name LIKE ? OR description LIKE ? OR found_location LIKE ?)";
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                    $types .= "sss";
                }

                $whereClause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

                // 1. Get Total Count
                $countSql = "SELECT COUNT(*) as total FROM found_items" . $whereClause;
                $countStmt = $conn->prepare($countSql);
                if (!empty($params)) {
                    $countStmt->bind_param($types, ...$params);
                }
                $countStmt->execute();
                $totalResult = $countStmt->get_result();
                $totalItems = $totalResult->fetch_assoc()['total'];
                $totalPages = ceil($totalItems / $limit);

                // 2. Get Items
                $sql = "SELECT * FROM found_items" . $whereClause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                
                // Add pagination params
                $params[] = $limit;
                $params[] = $offset;
                $types .= "ii";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $img_src = !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : '';
                        ?>
                        <a href="item.php?type=found&id=<?php echo $row['id']; ?>" class="item-card">
                            <div class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size: 2rem;">📦</div>
                                <?php endif; ?>
                                <span class="status-badge status-found">FOUND</span>
                            </div>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($row['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-location">📍 <?php echo htmlspecialchars($row['found_location']); ?></span>
                                    <span class="card-date">📅 <?php echo date('d F, Y', strtotime($row['date_found'])); ?></span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='empty-state' style='grid-column: 1/-1;'><h3>No found items match your criteria.</h3></div>";
                }
            } else {
                echo "<div class='empty-state' style='grid-column: 1/-1;'><h3>Database connection error.</h3></div>";
            }
            ?>
        </div>

        <!-- Pagination UI -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination" style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
            <?php
            // Build query params for links
            $queryParams = $_GET;
            unset($queryParams['page']);
            $queryString = http_build_query($queryParams);
            $baseUrl = "?" . ($queryString ? $queryString . "&" : "");

            // Prev Button
            if ($page > 1): ?>
                <a href="<?php echo $baseUrl . 'page=' . ($page - 1); ?>" class="btn-pill btn-outline">Previous</a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?php echo $baseUrl . 'page=' . $i; ?>" class="btn-pill" 
                   style="<?php echo ($i == $page) ? '' : 'background: white; color: var(--text-dark); border: 1px solid var(--border-light);'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Next Button -->
            <?php if ($page < $totalPages): ?>
                <a href="<?php echo $baseUrl . 'page=' . ($page + 1); ?>" class="btn-pill btn-outline">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
