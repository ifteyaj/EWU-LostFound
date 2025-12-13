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
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 8rem; padding-bottom: 4rem;">
        <a href="index.php" class="back-link" style="display:inline-block; margin-bottom:1.5rem; color:#64748b; font-weight:500; text-decoration:none; transition: color 0.2s;">
            ← Back to Home
        </a>

        <div class="section-header" style="margin-bottom: 2rem;">
            <div>
                <h2>Lost Items Database</h2>
                <p>Browsable list of all items reported missing.</p>
            </div>
            
            <div class="glass-panel" style="padding: 1.5rem; display: inline-block; width: 100%;">
                <form action="" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <div class="search-wrapper" style="margin: 0; max-width: 100%; position: relative;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94A3B8;"><path d="M18.031 16.6168L22.3137 20.8995L20.8995 22.3137L16.6168 18.031C15.0769 19.263 13.124 20 11 20C6.032 20 2 15.968 2 11C2 6.032 6.032 2 11 2C15.968 2 20 6.032 20 11C20 13.124 19.263 15.0769 18.031 16.6168ZM16.0247 15.8748C17.2475 14.6146 18 12.8956 18 11C18 7.1325 14.8675 4 11 4C7.1325 4 4 7.1325 4 11C4 14.8675 7.1325 18 11 18C12.8956 18 14.6146 17.2475 15.8748 16.0247L16.0247 15.8748Z"></path></svg>
                            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search lost items..." style="border: 1px solid var(--glass-border); padding: 0.8rem 2rem 0.8rem 3rem;">
                        </div>
                    </div>
                    
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
                    
                    <?php if(!empty($_GET['search']) || !empty($_GET['category'])): ?>
                        <a href="lost.php" style="color: var(--text-muted); font-weight: 500; font-size: 0.9rem; padding: 0 1rem;">Clear Filters</a>
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
                    $conditions[] = "(item_name LIKE ? OR description LIKE ? OR last_location LIKE ?)";
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                    $types .= "sss";
                }

                $whereClause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

                // 1. Get Total Count
                $countSql = "SELECT COUNT(*) as total FROM lost_items" . $whereClause;
                $countStmt = $conn->prepare($countSql);
                if (!empty($params)) {
                    $countStmt->bind_param($types, ...$params);
                }
                $countStmt->execute();
                $totalResult = $countStmt->get_result();
                $totalItems = $totalResult->fetch_assoc()['total'];
                $totalPages = ceil($totalItems / $limit);

                // 2. Get Items
                $sql = "SELECT * FROM lost_items" . $whereClause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                
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
                        <a href="item.php?type=lost&id=<?php echo $row['id']; ?>" class="item-card">
                            <div class="card-img">
                                <?php if($img_src): ?>
                                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                                <?php else: ?>
                                    <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); background:var(--bg-light); font-size: 2rem;">
                                        <i class="ri-box-3-line"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="status-badge status-lost">LOST</span>
                            </div>
                            <div class="card-content">
                                <div class="card-category"><?php echo htmlspecialchars($row['category']); ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                                <div class="card-meta" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <span class="card-location" style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.14876 5.63604 4.63404C9.15076 1.11932 14.8513 1.11932 18.366 4.63404C21.8807 8.14876 21.8807 13.8492 18.366 17.364L12 23.7279ZM16.9518 15.9497C19.6855 13.2161 19.6855 8.78392 16.9518 6.05025C14.2181 3.31658 9.78596 3.31658 7.05228 6.05025C4.31861 8.78392 4.31861 13.2161 7.05228 15.9497L12 20.8975L16.9518 15.9497ZM12 13C10.8954 13 10 12.1046 10 11C10 9.89543 10.8954 9 12 9C13.1046 9 14 9.89543 14 11C14 12.1046 13.1046 13 12 13Z"></path></svg>
                                        <?php echo htmlspecialchars($row['last_location']); ?>
                                    </span>
                                    <span class="card-date" style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H7V1H9V3H15V1H17V3ZM4 9V19H20V9H4ZM6 11H8V13H6V11ZM11 11H13V13H11V11ZM16 11H18V13H16V11Z"></path></svg>
                                        <?php echo date('d F, Y', strtotime($row['date_lost'])); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='empty-state' style='grid-column: 1/-1;'><h3>No lost items match your criteria.</h3></div>";
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
