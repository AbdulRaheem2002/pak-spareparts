<?php
/**
 * PakAutoParts - Shared Hosting File-based Rate Limiter
 * Zero external dependencies (No Redis required).
 */

declare(strict_types=1);

namespace App\Core;

class RateLimiter
{
    private static function getStorageDir(): string
    {
        $dir = Config::get('paths.cache') . '/rate_limits';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private static function getFilePath(string $key): string
    {
        $hash = sha1($key);
        return self::getStorageDir() . '/' . $hash . '.json';
    }

    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds = 60): bool
    {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return false;
        }

        $data = json_decode((string)file_get_contents($file), true);
        if (!$data || !isset($data['expires']) || !isset($data['attempts'])) {
            return false;
        }

        if (time() > $data['expires']) {
            @unlink($file);
            return false;
        }

        return $data['attempts'] >= $maxAttempts;
    }

    public static function hit(string $key, int $decaySeconds = 60): int
    {
        $file = self::getFilePath($key);
        $now = time();

        if (file_exists($file)) {
            $data = json_decode((string)file_get_contents($file), true);
            if ($data && isset($data['expires']) && $now <= $data['expires']) {
                $attempts = (int)($data['attempts'] ?? 0) + 1;
                $payload = [
                    'attempts' => $attempts,
                    'expires'  => $data['expires']
                ];
                file_put_contents($file, json_encode($payload), LOCK_EX);
                return $attempts;
            }
        }

        // New counter window
        $payload = [
            'attempts' => 1,
            'expires'  => $now + $decaySeconds
        ];
        file_put_contents($file, json_encode($payload), LOCK_EX);
        return 1;
    }

    public static function remaining(string $key, int $maxAttempts): int
    {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return $maxAttempts;
        }

        $data = json_decode((string)file_get_contents($file), true);
        if (!$data || !isset($data['expires']) || time() > $data['expires']) {
            return $maxAttempts;
        }

        $attempts = (int)($data['attempts'] ?? 0);
        return max(0, $maxAttempts - $attempts);
    }

    public static function clear(string $key): void
    {
        $file = self::getFilePath($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}

