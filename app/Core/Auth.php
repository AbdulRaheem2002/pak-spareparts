<?php
/**
 * PakAutoParts - Authentication & Role Manager
 * Handles password hashing, brute-force lockout, session isolation, and audit trails.
 */

declare(strict_types=1);

namespace App\Core;

class Auth
{
    private const SESSION_USER_KEY = '_auth_user_id';

    public static function attempt(string $email, string $password): bool
    {
        Session::start();
        $email = trim(strtolower($email));
        $ip = (new Request())->ip();

        // 1. Check rate limits and lockouts
        if (self::isLockedOut($email, $ip)) {
            return false;
        }

        // 2. Query user from database
        $user = Database::selectOne(
            "SELECT id, name, email, password_hash, role, failed_logins, lockout_until FROM users WHERE email = :email LIMIT 1",
            ['email' => $email]
        );

        if (!$user) {
            self::recordFailedAttempt($email, $ip);
            return false;
        }

        // 3. Check database-level lockout
        if (!empty($user['lockout_until']) && strtotime($user['lockout_until']) > time()) {
            return false;
        }

        // 4. Verify password
        if (!password_verify($password, $user['password_hash'])) {
            self::recordFailedAttempt($email, $ip, (int)$user['id'], (int)$user['failed_logins']);
            return false;
        }

        // 5. Successful login: reset failed counters and regenerate session
        Database::update(
            'users',
            ['failed_logins' => 0, 'lockout_until' => null, 'updated_at' => date('Y-m-d H:i:s')],
            'id = :id',
            ['id' => $user['id']]
        );

        RateLimiter::clear("login_{$email}");
        RateLimiter::clear("login_ip_{$ip}");

        Session::regenerate();
        Session::set(self::SESSION_USER_KEY, (int)$user['id']);
        Session::set('_auth_user_role', $user['role']);
        Session::set('_auth_user_name', $user['name']);

        // Log audit event
        self::logAudit((int)$user['id'], 'LOGIN_SUCCESS', 'users', (int)$user['id'], "Logged in successfully from IP: {$ip}");

        return true;
    }

    public static function check(): bool
    {
        Session::start();
        return Session::has(self::SESSION_USER_KEY) && Session::get(self::SESSION_USER_KEY) > 0;
    }

    public static function id(): ?int
    {
        Session::start();
        $id = Session::get(self::SESSION_USER_KEY);
        return $id ? (int)$id : null;
    }

    public static function user(): ?array
    {
        $id = self::id();
        if (!$id) {
            return null;
        }

        static $cachedUser = null;
        if ($cachedUser && $cachedUser['id'] === $id) {
            return $cachedUser;
        }

        $cachedUser = Database::selectOne(
            "SELECT id, name, email, role, created_at FROM users WHERE id = :id LIMIT 1",
            ['id' => $id]
        );

        return $cachedUser;
    }

    public static function role(): string
    {
        $user = self::user();
        return $user['role'] ?? 'guest';
    }

    public static function isOwner(): bool
    {
        return self::role() === 'owner';
    }

    public static function hasRole(string ...$roles): bool
    {
        $userRole = self::role();
        if ($userRole === 'owner') {
            return true; // Owner inherits all permissions
        }

        return in_array($userRole, $roles, true);
    }

    public static function logout(): void
    {
        $userId = self::id();
        if ($userId) {
            $ip = (new Request())->ip();
            self::logAudit($userId, 'LOGOUT', 'users', $userId, "User logged out from IP: {$ip}");
        }

        Session::destroy();
    }

    private static function isLockedOut(string $email, string $ip): bool
    {
        return RateLimiter::tooManyAttempts("login_{$email}", 10, 300)
            || RateLimiter::tooManyAttempts("login_ip_{$ip}", 20, 300);
    }

    private static function recordFailedAttempt(string $email, string $ip, ?int $userId = null, int $failedCount = 0): void
    {
        RateLimiter::hit("login_{$email}", 300);
        RateLimiter::hit("login_ip_{$ip}", 300);

        if ($userId) {
            $newCount = $failedCount + 1;
            $data = ['failed_logins' => $newCount];

            if ($newCount >= 5) {
                $data['lockout_until'] = date('Y-m-d H:i:s', time() + 900); // 15 mins lockout
                self::logAudit($userId, 'ACCOUNT_LOCKED', 'users', $userId, "Account locked after 5 failed attempts from IP: {$ip}");
            }

            Database::update('users', $data, 'id = :id', ['id' => $userId]);
        }
    }

    public static function logAudit(?int $userId, string $action, string $entityType, ?int $entityId, string $details): void
    {
        try {
            $req = new Request();
            Database::insert('audit_logs', [
                'user_id'     => $userId,
                'action'      => $action,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'details'     => $details,
                'ip_address'  => $req->ip(),
                'user_agent'  => substr($req->userAgent(), 0, 255),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Non-critical audit logging failure fallback
        }
    }
}

