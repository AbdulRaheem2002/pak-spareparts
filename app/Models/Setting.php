<?php
/**
 * PakAutoParts - Settings Model with File Caching
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Core\Config;

class Setting extends Model
{
    protected static string $table = 'settings';
    private static ?array $cache = null;

    private static function getCacheFile(): string
    {
        return Config::get('paths.cache') . '/settings_cache.json';
    }

    public static function loadAll(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $cacheFile = self::getCacheFile();
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
            $data = json_decode((string)file_get_contents($cacheFile), true);
            if (is_array($data)) {
                self::$cache = $data;
                return self::$cache;
            }
        }

        try {
            $rows = Database::select("SELECT setting_key, setting_value FROM `settings`");
            $data = [];
            foreach ($rows as $row) {
                $data[$row['setting_key']] = $row['setting_value'];
            }
            self::$cache = $data;
            @file_put_contents($cacheFile, json_encode($data), LOCK_EX);
            return self::$cache;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::loadAll();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general'): void
    {
        $exists = Database::selectOne("SELECT id FROM `settings` WHERE setting_key = :k LIMIT 1", ['k' => $key]);

        if ($exists) {
            Database::update('settings', [
                'setting_value' => $value,
                'updated_at'    => date('Y-m-d H:i:s'),
            ], 'setting_key = :k', ['k' => $key]);
        } else {
            Database::insert('settings', [
                'setting_group' => $group,
                'setting_key'   => $key,
                'setting_value' => $value,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        self::$cache = null;
        $cacheFile = self::getCacheFile();
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }
    }
}

