<?php
/**
 * Application Bootstrap
 * Include this file at the top of every page
 */

// Load configuration
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/constants.php';

// Load core modules
require_once __DIR__ . '/includes/error_handler.php';
require_once __DIR__ . '/includes/security.php';

// Initialize secure session
initSecureSession();

// Load database connection
require_once __DIR__ . '/config/db.php';

// Load authentication module
require_once __DIR__ . '/includes/auth.php';
?>
