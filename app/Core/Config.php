<?php
/**
 * PakAutoParts - Configuration Manager
 */

declare(strict_types=1);

namespace App\Core;

class Config
{
    private static array $items = [];
    private static bool $loaded = false;

    public static function load(array $config = []): void
    {
        if (empty($config)) {
            $configFile = dirname(__DIR__, 2) . '/config/config.php';
            if (file_exists($configFile)) {
                $config = require $configFile;
            }
        }
        self::$items = $config;
        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::load();
        }

        if (strpos($key, '.') === false) {
            return self::$items[$key] ?? $default;
        }

        $array = self::$items;
        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    public static function set(string $key, mixed $value): void
    {
        if (!self::$loaded) {
            self::load();
        }

        $keys = explode('.', $key);
        $array = &self::$items;

        while (count($keys) > 1) {
            $segment = array_shift($keys);
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }
            $array = &$array[$segment];
        }

        $array[array_shift($keys)] = $value;
    }

    public static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }
        return self::$items;
    }
}

