<?php
/**
 * PakBikeParts - Global Application Configuration
 * Works on Apache / cPanel / hPanel / Localhost (XAMPP / Laragon)
 */

declare(strict_types=1);

// 1. Automatically load .env file if present in project root
$rootPath = dirname(__DIR__);
$envFile = $rootPath . '/.env';
if (file_exists($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($k, $_SERVER) && !array_key_exists($k, $_ENV)) {
                putenv("{$k}={$v}");
                $_ENV[$k] = $v;
                $_SERVER[$k] = $v;
            }
        }
    }
}

// 2. Detect environment
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
    || (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'localhost'));

$appEnv = getenv('APP_ENV') ?: ($isLocalhost ? 'dev' : 'prod');
$appDebug = getenv('APP_DEBUG') !== false 
    ? filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) 
    : ($appEnv === 'dev');

// 3. Determine Base URL dynamically
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($scheme . '://' . $host . $scriptDir, '/');

$config = [
    'app' => [
        'name'        => getenv('APP_NAME') ?: 'PakBikeParts',
        'tagline'     => 'Pakistan\'s Premier Motorcycle Spare Parts Network',
        'url'         => getenv('APP_URL') ?: $baseUrl,
        'env'         => getenv('APP_ENV') ?: 'prod', // 'prod' for live Hostinger deployment
        'debug'       => $appDebug,
        'key'         => getenv('APP_KEY') ?: 'base64_pakautoparts_secure_key_2026_x99a',
        'timezone'    => 'Asia/Karachi',
        'locale'      => 'en',
    ],

    'db' => [
        'driver'    => 'mysql',
        'host'      => getenv('DB_HOST') ?: 'localhost',
        'port'      => (int)(getenv('DB_PORT') ?: 3306),
        'database'  => getenv('DB_DATABASE') ?: 'u676397674_spareparts_db',
        'username'  => getenv('DB_USERNAME') ?: 'u676397674_abdulraheem',
        'password'  => getenv('DB_PASSWORD') ?: 'YOUR_DATABASE_PASSWORD_HERE',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options'   => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ],
    ],

    'currency' => [
        'code'      => 'PKR',
        'symbol'    => 'Rs. ',
        'position'  => 'prefix', // prefix or suffix
        'decimals'  => 0,
        'thousands' => ',',
    ],

    'contact' => [
        'phone_display'  => '0300-1234567',
        'phone_raw'      => '+923001234567',
        'whatsapp_num'   => '923001234567',
        'email'          => 'support@pakbikeparts.pk',
        'address'        => 'Plaza #28, Auto & Bike Market, Montgomery Road, Lahore, Pakistan',
        'opening_hours'  => 'Mon - Sat: 9:00 AM - 8:00 PM',
    ],

    'shipping' => [
        'default_fee'          => 250,
        'cod_fee'              => 50,
        'free_shipping_min'    => 3000,
        'estimated_days'       => '2-4 Working Days',
    ],

    'session' => [
        'name'     => 'PAP_SESS_ID',
        'lifetime' => 86400 * 7, // 7 days
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    'security' => [
        'csrf_token_name'     => 'csrf_token',
        'max_login_attempts'  => 5,
        'lockout_time_mins'   => 15,
        'rate_limits' => [
            'login'        => ['limit' => 5, 'window' => 300], // 5 attempts per 5 mins
            'tracking'     => ['limit' => 15, 'window' => 60], // 15 tracks per min
            'part_request' => ['limit' => 5, 'window' => 600], // 5 requests per 10 mins
            'review'       => ['limit' => 3, 'window' => 3600], // 3 reviews per hour
            'api'          => ['limit' => 60, 'window' => 60], // 60 calls per min
        ],
    ],

    'uploads' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
        'allowed_exts'  => ['jpg', 'jpeg', 'png', 'webp'],
        'quality_webp'  => 82,
        'thumb_width'   => 320,
        'thumb_height'  => 320,
        'large_width'   => 1000,
        'large_height'  => 1000,
    ],

    'paths' => [
        'root'     => dirname(__DIR__),
        'app'      => dirname(__DIR__) . '/app',
        'config'   => dirname(__DIR__) . '/config',
        'public'   => dirname(__DIR__) . '/public',
        'storage'  => dirname(__DIR__) . '/storage',
        'cache'    => dirname(__DIR__) . '/storage/cache',
        'logs'     => dirname(__DIR__) . '/storage/logs',
        'uploads'  => dirname(__DIR__) . '/public/uploads',
    ],
];

// 4. Merge optional local config override (git-ignored, persists across git deployments)
$localConfigFile = __DIR__ . '/local.php';
if (file_exists($localConfigFile)) {
    $localConfig = require $localConfigFile;
    if (is_array($localConfig)) {
        $config = array_replace_recursive($config, $localConfig);
    }
}

return $config;
