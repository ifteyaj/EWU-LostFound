<?php
/**
 * Footer Component
 */
?>
<footer style="background: #FAFBFC; padding: 4rem 0 2rem; border-top: 1px solid var(--border-light); margin-top: auto;">
    <div class="container">
        <!-- Logo Section -->
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="EWU Lost & Found" style="height: 50px;">
        </div>
        
        <!-- Description -->
        <div style="text-align: center; margin-bottom: 2.5rem; max-width: 650px; margin-left: auto; margin-right: auto;">
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                The official lost and found platform for East West University.<br>Reuniting students with what matters most.
            </p>
        </div>
        
        <!-- Navigation Links -->
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div style="display: inline-flex; gap: 3rem; flex-wrap: wrap; justify-content: center;">
                <a href="<?php echo APP_URL; ?>/lost.php" style="color: var(--text-secondary); font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Lost Items</a>
                <a href="<?php echo APP_URL; ?>/found.php" style="color: var(--text-secondary); font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Found Items</a>
                <a href="<?php echo APP_URL; ?>/post_item.php" style="color: var(--text-secondary); font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Report Item</a>
                <a href="mailto:support@ewubd.edu" style="color: var(--text-secondary); font-size: 0.95rem; font-weight: 500; transition: color 0.2s;">Contact</a>
            </div>
        </div>
        
        <!-- Copyright -->
        <div style="text-align: center; padding-top: 2rem; border-top: 1px solid var(--border-light);">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                &copy; Ifteyaj. All rights reserved.
            </p>
        </div>
    </div>
    <script src="<?php echo APP_URL; ?>/assets/js/navbar.js"></script>
</footer>

