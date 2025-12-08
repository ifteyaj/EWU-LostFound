<?php
/**
 * Environment Configuration Loader
 * Loads environment variables from .env file
 */

class Env {
    private static $loaded = false;
    private static $variables = [];

    /**
     * Load environment variables from .env file
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }

        $envFile = $path ?? dirname(__DIR__) . '/.env';

        if (!file_exists($envFile)) {
            // Fallback to default values if .env doesn't exist
            self::setDefaults();
            self::$loaded = true;
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes from value
                $value = trim($value, '"\'');

                self::$variables[$key] = $value;
                
                // Also set in $_ENV and putenv for compatibility
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        self::$loaded = true;
    }

    /**
     * Set default values when .env file doesn't exist
     */
    private static function setDefaults() {
        $defaults = [
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'APP_URL' => 'http://localhost/EWU-LostFound',
            'APP_NAME' => 'EWU Lost & Found',
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'ewu_lost_found',
            'DB_USER' => 'root',
            'DB_PASS' => '',
            'SESSION_LIFETIME' => '120',
            'SESSION_SECURE' => 'false',
            'RATE_LIMIT_REQUESTS' => '60',
            'RATE_LIMIT_WINDOW' => '60'
        ];

        foreach ($defaults as $key => $value) {
            self::$variables[$key] = $value;
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    /**
     * Get an environment variable
     * 
     * @param string $key The variable name
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Check if we're in debug mode
     */
    public static function isDebug() {
        return self::get('APP_DEBUG', 'false') === 'true';
    }

    /**
     * Check if we're in production
     */
    public static function isProduction() {
        return self::get('APP_ENV', 'development') === 'production';
    }

    /**
     * Check if we're in development
     */
    public static function isDevelopment() {
        return self::get('APP_ENV', 'development') === 'development';
    }
}

// Auto-load environment variables
Env::load();
?>
