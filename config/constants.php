<?php
/**
 * Application Constants
 * Centralized configuration values
 */

require_once __DIR__ . '/env.php';

// Application
define('APP_NAME', Env::get('APP_NAME', 'EWU Lost & Found'));
define('APP_URL', Env::get('APP_URL', 'http://localhost/EWU-LostFound'));
define('APP_ENV', Env::get('APP_ENV', 'development'));
define('APP_DEBUG', Env::isDebug());

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');
define('INCLUDES_PATH', ROOT_PATH . '/includes');

// File Upload Settings
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Session
define('SESSION_LIFETIME', (int) Env::get('SESSION_LIFETIME', 120) * 60); // Convert to seconds

// Rate Limiting
define('RATE_LIMIT_REQUESTS', (int) Env::get('RATE_LIMIT_REQUESTS', 60));
define('RATE_LIMIT_WINDOW', (int) Env::get('RATE_LIMIT_WINDOW', 60));

// Categories (centralized for consistency)
define('ITEM_CATEGORIES', [
    'Electronics',
    'Books & Notes',
    'ID Cards & Documents',
    'Accessories',
    'Clothing',
    'Keys',
    'Bags & Wallets',
    'Sports Equipment',
    'Stationery',
    'Others'
]);

// Item Status
define('STATUS_PENDING', 'pending');
define('STATUS_CLAIMED', 'claimed');
define('STATUS_RESOLVED', 'resolved');
?>
