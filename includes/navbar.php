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
            <!-- Left Side Links -->
            <li class="logo-item" style="margin-right: 2rem;">
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

            <!-- Right Side Actions (Pushed to right) -->
            <div style="margin-left: auto; display: flex; align-items: center; gap: 1.5rem;">
                <?php if (isLoggedIn()): ?>
                    <li style="list-style: none;">
                        <a href="<?php echo APP_URL; ?>/profile.php" class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
                            Hi, <?php echo htmlspecialchars(getUserDisplayName()); ?>
                        </a>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li style="list-style: none;"><a href="<?php echo APP_URL; ?>/admin/index.php">Admin</a></li>
                    <?php endif; ?>
                    <li style="list-style: none;"><a href="<?php echo APP_URL; ?>/auth/logout.php" style="color: #ef4444; font-weight: 600;">Logout</a></li>
                <?php else: ?>
                    <li style="list-style: none;">
                        <a href="<?php echo APP_URL; ?>/auth/login.php" class="<?php echo ($currentPage == 'login.php') ? 'active' : ''; ?>">
                            Login
                        </a>
                    </li>
                <?php endif; ?>

                <li style="list-style: none;">
                    <a href="<?php echo APP_URL; ?>/post_item.php" class="btn-pill" style="color: #fff !important;">Report Item</a>
                </li>
            </div>
        </div>
    </div>
</nav>
