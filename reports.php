<?php
/**
 * All Reports Listing Page
 * Unified view of Lost and Found items
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
    <meta name="description" content="Browse all lost and found reports at East West University.">
    <title>All Reports - EWU Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 10rem; padding-bottom: 5rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h2>All Reports</h2>
                <p>Real-time feed of all lost and found items.</p>
            </div>
            
            <div class="glass-panel" style="padding: 1.5rem; display: inline-block; width: 100%;">
                <form action="" method="GET" style="display: flex; gap: 1rem; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
                    
                    <select name="type" class="filter-dropdown">
                        <option value="">All Types</option>
                        <option value="lost" <?php echo (isset($_GET['type']) && $_GET['type'] == 'lost') ? 'selected' : ''; ?>>Lost</option>
                        <option value="found" <?php echo (isset($_GET['type']) && $_GET['type'] == 'found') ? 'selected' : ''; ?>>Found</option>
                    </select>

                    <select name="category" class="filter-dropdown">
                        <option value="">All Categories</option>
                        <?php 
                        $selectedCat = $_GET['category'] ?? '';
                        foreach (ITEM_CATEGORIES as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($selectedCat == $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit" class="btn-pill btn-primary" style="padding: 0.8rem 2rem;">Filter</button>
                    
                    <?php if(!empty($_GET['category']) || !empty($_GET['type'])): ?>
                        <a href="reports.php" style="color: var(--text-muted); font-weight: 500; font-size: 0.9rem; padding: 0 1rem;">Clear Filters</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="items-grid">
            <?php
            if ($conn && !$conn->connect_error) {
                // Pagination Setup
                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $limit = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 12;
                $offset = ($page - 1) * $limit;

                // Build Conditions
                $conditions = [];
                $params = [];
                $typesStr = "";

                // We need to construct filtering for the UNION query.
                // It is easier to construct WHERE clauses for individual sub-queries or wrap the whole result.
                // Wrapping is cleaner for code, but less performant on huge datasets. 
                // Given likely scale, we'll apply filters to subqueries or use specific logic.
                // Let's use a Common Table Expression (CTE) approach or simple UNION subquery alias logic for clarity.
                // MySQL 8 supports CTEs. If older MySQL, we just nest.

                $search = $_GET['search'] ?? '';
                $cat = $_GET['category'] ?? '';
                $typeFilter = $_GET['type'] ?? '';

                // Preparing individual WHERE clauses
                $lostWhere = [];
                $foundWhere = [];
                
                $termParams = [];
                $termTypes = "";

                if (!empty($cat)) {
                    $lostWhere[] = "category = ?";
                    $foundWhere[] = "category = ?";
                    $termParams[] = $cat; // For lost
                    $termParams[] = $cat; // For found
                    $termTypes .= "ss";
                }

                if (!empty($search)) {
                    $itemSearch = "%" . $search . "%";
                    $lostWhere[] = "(item_name LIKE ? OR description LIKE ? OR last_location LIKE ?)";
                    $foundWhere[] = "(item_name LIKE ? OR description LIKE ? OR found_location LIKE ?)";
                    
                    // Add 3 params for lost
                    $termParams[] = $itemSearch; $termParams[] = $itemSearch; $termParams[] = $itemSearch;
                    // Add 3 params for found
                    $termParams[] = $itemSearch; $termParams[] = $itemSearch; $termParams[] = $itemSearch;
                    
                    $termTypes .= "ssssss";
                }

                // Construct full query parts
                // Lost Part
                $lostReq = "SELECT id, item_name, category, last_location as location, 'lost' as type, image, date_lost as event_date, created_at, status FROM lost_items";
                if (!empty($lostWhere)) $lostReq .= " WHERE " . implode(" AND ", $lostWhere);
                
                // Found Part
                $foundReq = "SELECT id, item_name, category, found_location as location, 'found' as type, image, date_found as event_date, created_at, status FROM found_items";
                if (!empty($foundWhere)) $foundReq .= " WHERE " . implode(" AND ", $foundWhere);

                // Combine based on Type Filter
                if ($typeFilter == 'lost') {
                    $finalSql = $lostReq;
                    // Fix params: keep only lost params. 
                    // This dynamic param building is redundant with the complexity. 
                    // Let's simplify: simply build the query dynamically.
                } elseif ($typeFilter == 'found') {
                    $finalSql = $foundReq;
                } else {
                    $finalSql = "($lostReq) UNION ALL ($foundReq)";
                }

                // Re-calculating params correctly based on flow:
                $finalParams = [];
                $finalTypes = "";

                // Helper to add params
                $addParams = function($isLost) use ($cat, $search, &$finalParams, &$finalTypes) {
                    if (!empty($cat)) {
                        $finalParams[] = $cat;
                        $finalTypes .= "s";
                    }
                    if (!empty($search)) {
                        $s = "%$search%";
                        $finalParams[] = $s; $finalParams[] = $s; $finalParams[] = $s;
                        $finalTypes .= "sss";
                    }
                };

                if ($typeFilter == 'lost') {
                    $addParams(true);
                } elseif ($typeFilter == 'found') {
                    $addParams(false);
                } else {
                    $addParams(true);
                    $addParams(false);
                }

                // 1. Count Total
                $countSql = "SELECT COUNT(*) as total FROM ($finalSql) as combined_table";
                $countStmt = $conn->prepare($countSql);
                if (!empty($finalParams)) {
                    $countStmt->bind_param($finalTypes, ...$finalParams);
                }
                $countStmt->execute();
                $totalResult = $countStmt->get_result();
                $totalItems = $totalResult->fetch_assoc()['total'];
                $totalPages = ceil($totalItems / $limit);

                // 2. Fetch Items
                $dataSql = "SELECT * FROM ($finalSql) as combined_table ORDER BY created_at DESC LIMIT ? OFFSET ?";
                // Add pagination params
                $finalParams[] = $limit;
                $finalParams[] = $offset;
                $finalTypes .= "ii";

                $stmt = $conn->prepare($dataSql);
                if (!empty($finalParams)) {
                    $stmt->bind_param($finalTypes, ...$finalParams);
                }
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $img_src = !empty($row['image']) ? 'uploads/' . htmlspecialchars($row['image']) : '';
                        $badgeClass = $row['type'] == 'lost' ? 'status-lost' : 'status-found';
                        $badgeText = $row['type'] == 'lost' ? 'LOST' : 'FOUND';
                        ?>
                        <a href="item.php?type=<?php echo $row['type']; ?>&id=<?php echo $row['id']; ?>" class="item-card">
                            <div class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size: 2rem;">📦</div>
                                <?php endif; ?>
                                <span class="status-badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
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
                    echo "<div class='empty-state' style='grid-column: 1/-1;'><h3>No items match your criteria.</h3></div>";
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
            $queryParams = $_GET;
            unset($queryParams['page']);
            $queryString = http_build_query($queryParams);
            $baseUrl = "?" . ($queryString ? $queryString . "&" : "");

            if ($page > 1): ?>
                <a href="<?php echo $baseUrl . 'page=' . ($page - 1); ?>" class="btn-pill btn-outline">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?php echo $baseUrl . 'page=' . $i; ?>" class="btn-pill" 
                   style="<?php echo ($i == $page) ? '' : 'background: white; color: var(--text-dark); border: 1px solid var(--border-light);'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?php echo $baseUrl . 'page=' . ($page + 1); ?>" class="btn-pill btn-outline">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
