<?php
/**
 * PakAutoParts - CSRF Defense
 * Double-submit / session-bound token with timing attack mitigation.
 */

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        Session::start();
        $token = Session::get(self::SESSION_KEY);

        if (!$token || !is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function validate(?string $submittedToken = null): bool
    {
        Session::start();
        $stored = Session::get(self::SESSION_KEY);

        if (!$stored || !is_string($stored)) {
            return false;
        }

        if ($submittedToken === null) {
            $submittedToken = $_POST['csrf_token']
                ?? $_SERVER['HTTP_X_CSRF_TOKEN']
                ?? null;
        }

        if (!$submittedToken || !is_string($submittedToken)) {
            return false;
        }

        return hash_equals($stored, $submittedToken);
    }

    public static function regenerate(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }
}

