<?php
/**
 * Footer Component
 */
?>
<footer style="background: var(--white); padding: 4rem 0 2rem; border-top: 1px solid var(--border-light); margin-top: auto;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; margin-bottom: 3rem;">
            <div>
                <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="EWU Lost & Found" style="height: 40px; margin-bottom: 1rem; opacity: 0.8;">
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                    The official decentralized lost and found layer for East West University. 
                    Reuniting students with what matters most.
                </p>
            </div>
            
            <div>
                <h4 style="margin-bottom: 1.25rem; font-size: 1rem;">Quick Links</h4>
                <ul style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <li><a href="<?php echo APP_URL; ?>/lost.php" style="color: var(--text-secondary); font-size: 0.9rem;">Lost Items</a></li>
                    <li><a href="<?php echo APP_URL; ?>/found.php" style="color: var(--text-secondary); font-size: 0.9rem;">Found Items</a></li>
                    <li><a href="<?php echo APP_URL; ?>/post_item.php" style="color: var(--text-secondary); font-size: 0.9rem;">Report a Case</a></li>
                </ul>
            </div>
            
            <div>
                <h4 style="margin-bottom: 1.25rem; font-size: 1rem;">Support</h4>
                <ul style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <li><a href="#" style="color: var(--text-secondary); font-size: 0.9rem;">How it Works</a></li>
                    <li><a href="#" style="color: var(--text-secondary); font-size: 0.9rem;">Safety Tips</a></li>
                    <li><a href="mailto:support@ewubd.edu" style="color: var(--text-secondary); font-size: 0.9rem;">Contact Admin</a></li>
                </ul>
            </div>
        </div>
        
        <div style="text-align: center; padding-top: 2rem; border-top: 1px solid var(--border-light); color: var(--text-muted); font-size: 0.85rem;">
            &copy; <?php echo date('Y'); ?> East West University. All rights reserved.
        </div>
    </div>
    </div>
    <script src="<?php echo APP_URL; ?>/assets/js/navbar.js"></script>
</footer>

