<?php
/**
 * Navigation Bar Component
 * Automatically handles active states and authentication links
 */

// Determine current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="container">
        <div class="nav-links">
            <li class="logo-item">
                <a href="<?php echo APP_URL; ?>/index.php" class="logo">
                    <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="EWU Lost & Found">
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                    Home
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/lost.php" class="<?php echo ($currentPage == 'lost.php') ? 'active' : ''; ?>">
                    Lost Items
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/found.php" class="<?php echo ($currentPage == 'found.php') ? 'active' : ''; ?>">
                    Found Items
                </a>
            </li>

            <?php if (isLoggedIn()): ?>
                <li>
                    <a href="<?php echo APP_URL; ?>/profile.php" class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
                        Hi, <?php echo htmlspecialchars(getUserDisplayName()); ?>
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?php echo APP_URL; ?>/admin/index.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="<?php echo APP_URL; ?>/auth/logout.php" style="color: var(--status-lost-bg);">Logout</a></li>
            <?php else: ?>
                <li>
                    <a href="<?php echo APP_URL; ?>/auth/login.php" class="<?php echo ($currentPage == 'login.php') ? 'active' : ''; ?>">
                        Login
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo APP_URL; ?>/post_item.php" class="btn-pill">Report Item</a>
            </li>
        </div>
    </div>
</nav>
