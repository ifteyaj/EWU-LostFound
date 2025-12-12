<?php
/**
 * Error Handler
 * Custom error and exception handling with logging
 */

require_once __DIR__ . '/../config/env.php';

// Ensure logs directory exists
$logsDir = dirname(__DIR__) . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

/**
 * Log an error message to file
 * 
 * @param string $message The error message
 * @param string $level Error level (ERROR, WARNING, INFO, DEBUG, CRITICAL)
 * @param array $context Additional context data
 */
function logError($message, $level = 'ERROR', $context = []) {
    $logsDir = dirname(__DIR__) . '/logs';
    $logFile = $logsDir . '/error_' . date('Y-m-d') . '.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $uri = $_SERVER['REQUEST_URI'] ?? 'N/A';
    
    $logEntry = "[{$timestamp}] [{$level}] [{$ip}] [{$uri}] {$message}";
    
    if (!empty($context)) {
        $logEntry .= " | Context: " . json_encode($context);
    }
    
    $logEntry .= PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Log application activity (non-error)
 */
function logActivity($message, $context = []) {
    $logsDir = dirname(__DIR__) . '/logs';
    $logFile = $logsDir . '/activity_' . date('Y-m-d') . '.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $uri = $_SERVER['REQUEST_URI'] ?? 'N/A';
    
    $logEntry = "[{$timestamp}] [{$ip}] [{$uri}] {$message}";
    
    if (!empty($context)) {
        $logEntry .= " | " . json_encode($context);
    }
    
    $logEntry .= PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Custom error handler
 */
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $errorTypes = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED'
    ];
    
    $level = $errorTypes[$errno] ?? 'UNKNOWN';
    $message = "{$errstr} in {$errfile} on line {$errline}";
    
    logError($message, $level);
    
    // Don't execute PHP's internal error handler for non-fatal errors in production
    if (!Env::isDebug() && !in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        return true;
    }
    
    // Let PHP handle it in debug mode
    return false;
}

/**
 * Custom exception handler
 */
function customExceptionHandler($exception) {
    $message = "Uncaught Exception: " . $exception->getMessage();
    $context = [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
    
    logError($message, 'CRITICAL', $context);
    
    if (Env::isDebug()) {
        echo "<h1>Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    } else {
        // Redirect to error page in production
        http_response_code(500);
        include dirname(__DIR__) . '/errors/500.php';
    }
    
    exit(1);
}

/**
 * Shutdown handler for fatal errors
 */
function shutdownHandler() {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = "Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}";
        logError($message, 'FATAL');
        
        if (!Env::isDebug()) {
            http_response_code(500);
            include dirname(__DIR__) . '/errors/500.php';
            exit(1);
        }
    }
}

/**
 * Display a user-friendly error page
 */
function showError($code = 500, $message = null) {
    http_response_code($code);
    
    $errorFile = dirname(__DIR__) . "/errors/{$code}.php";
    
    if (file_exists($errorFile)) {
        include $errorFile;
    } else {
        echo "<h1>Error {$code}</h1>";
        if ($message) {
            echo "<p>" . htmlspecialchars($message) . "</p>";
        }
    }
    
    exit;
}

// Register handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');
register_shutdown_function('shutdownHandler');

// Configure error display based on environment
if (Env::isDebug()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}
?>
