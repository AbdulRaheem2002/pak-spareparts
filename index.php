<?php
/**
 * PakAutoParts - Front Controller & Application Bootstrap
 * High-performance, secure entry point for shared Apache / cPanel / hPanel environments.
 */

declare(strict_types=1);

// Define base path constants
define('PAP_START', microtime(true));
define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');
define('STORAGE_PATH', __DIR__ . '/storage');
define('PUBLIC_PATH', __DIR__ . '/public');

try {
    // Autoload core framework
    $autoloader = APP_PATH . '/Core/Autoloader.php';
    if (!file_exists($autoloader)) {
        throw new \RuntimeException("Autoloader not found at [{$autoloader}]. Please ensure project folders (app, config, public, storage) are extracted directly inside public_html.");
    }
    require_once $autoloader;
    \App\Core\Autoloader::register();

    // Load application configuration
    \App\Core\Config::load();

    // Environment & Error Handling Configuration
    $isDebug = (bool)\App\Core\Config::get('app.debug', false);
    $isDev = \App\Core\Config::get('app.env') === 'dev';

    if ($isDebug || $isDev) {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        error_reporting(E_ALL);
    }

    ini_set('log_errors', '1');
    @mkdir(STORAGE_PATH . '/logs', 0755, true);
    ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');
    date_default_timezone_set(\App\Core\Config::get('app.timezone', 'Asia/Karachi'));

    // Load global helpers
    require_once APP_PATH . '/Helpers/functions.php';

    // Security Headers (applied to all PHP responses)
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
    }

    // Start hardened session
    \App\Core\Session::start();

    // Initialize Request object
    $request = \App\Core\Request::capture();

    // Register Web & API Routes
    require_once APP_PATH . '/routes.php';

    // Dispatch router
    $response = \App\Core\Router::dispatch($request);
    $response->send();

} catch (\Throwable $e) {
    http_response_code(500);
    $msg = sprintf("[%s] Fatal Error: %s in %s on line %d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    @file_put_contents(STORAGE_PATH . '/logs/php_errors.log', $msg, FILE_APPEND | LOCK_EX);

    // Render clear diagnostic page instead of a blank 500
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Application Error - PakAutoParts</title>';
    echo '<style>body{font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;background:#0f172a;color:#f8fafc;padding:40px 20px;}';
    echo '.box{max-width:800px;margin:0 auto;background:#1e293b;border:1px solid #ef4444;border-radius:12px;padding:30px;box-shadow:0 10px 25px rgba(0,0,0,0.5);}';
    echo 'h1{color:#ef4444;font-size:22px;margin-top:0;}';
    echo 'pre{background:#090d16;padding:15px;border-radius:6px;overflow-x:auto;color:#cbd5e1;font-size:13px;border:1px solid #334155;}';
    echo '.help{background:#334155;padding:15px;border-radius:6px;margin-top:20px;font-size:14px;color:#cbd5e1;}';
    echo 'code{background:#090d16;padding:2px 6px;border-radius:4px;color:#f97316;}';
    echo '</style></head><body><div class="box">';
    echo '<h1>⚠️ Setup / Application Error</h1>';
    echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Location:</strong> ' . htmlspecialchars(basename($e->getFile())) . ' (Line ' . $e->getLine() . ')</p>';
    echo '<h3>Details / Stack Trace:</h3>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '<div class="help">💡 <strong>Quick Fix Checklist:</strong><br><br>';
    echo '1. <strong>Database Error:</strong> Double-check database username, password, and database name in <code>config/config.php</code>.<br>';
    echo '2. <strong>Missing Tables:</strong> Open Hostinger phpMyAdmin and verify <code>database/database.sql</code> is imported.<br>';
    echo '3. <strong>Permissions:</strong> Ensure <code>storage/</code> and <code>public/uploads/</code> are writable (chmod 755).</div>';
    echo '</div></body></html>';
    exit;
}
