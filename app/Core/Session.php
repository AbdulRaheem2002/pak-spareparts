<?php
/**
 * PakAutoParts - Hardened Session Manager
 * Enforces SameSite, HttpOnly, secure flags, session fixation protection and flash messaging.
 */

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $sessionConfig = Config::get('session');

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        session_name($sessionConfig['name'] ?? 'PAP_SESS_ID');

        session_set_cookie_params([
            'lifetime' => (int)($sessionConfig['lifetime'] ?? 604800),
            'path'     => $sessionConfig['path'] ?? '/',
            'domain'   => $sessionConfig['domain'] ?? '',
            'secure'   => (bool)($sessionConfig['secure'] ?? false),
            'httponly' => true,
            'samesite' => $sessionConfig['samesite'] ?? 'Lax',
        ]);

        session_start();
        self::$started = true;

        // Session fixation protection: bind to User Agent signature
        $agentSig = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'none');
        if (!isset($_SESSION['_agent_sig'])) {
            $_SESSION['_agent_sig'] = $agentSig;
        } elseif (!hash_equals($_SESSION['_agent_sig'], $agentSig)) {
            // Potential hijack attempt: reset session
            self::destroy();
            session_start();
            $_SESSION['_agent_sig'] = $agentSig;
        }

        // Periodic ID regeneration every 30 minutes
        if (!isset($_SESSION['_last_regeneration'])) {
            $_SESSION['_last_regeneration'] = time();
        } elseif (time() - $_SESSION['_last_regeneration'] > 1800) {
            self::regenerate();
            $_SESSION['_last_regeneration'] = time();
        }
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        if (isset($_SESSION['_flash'][$key])) {
            $val = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $val;
        }
        return $default;
    }

    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
            self::$started = false;
        }
    }
}

