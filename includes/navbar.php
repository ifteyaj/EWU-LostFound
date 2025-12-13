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
        <!-- Logo -->
        <div class="logo-item">
            <a href="<?php echo APP_URL; ?>/index.php">
                <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="EWU Lost & Found">
            </a>
        </div>

        <ul class="nav-links">
            <li>
                <a href="<?php echo APP_URL; ?>/index.php" class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                    Home
                </a>
            </li>
            


            <!-- Right Side Actions -->
            <?php if (isLoggedIn()): ?>
                <li>
                    <a href="<?php echo APP_URL; ?>/profile.php" class="profile-avatar-link <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>" title="<?php echo htmlspecialchars(getUserDisplayName()); ?>">
                        <div class="profile-avatar">
                            <i class="ri-user-fill"></i>
                        </div>
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?php echo APP_URL; ?>/admin/index.php" class="nav-link">Admin</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li>
                    <a href="<?php echo APP_URL; ?>/auth/login.php" class="nav-link <?php echo ($currentPage == 'login.php') ? 'active' : ''; ?>">
                        Login
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo APP_URL; ?>/post_item.php" class="btn-pill btn-primary" style="margin-left: 1rem;">Report Item</a>
            </li>
        </ul>
    </div>
</nav>
