<?php
/**
 * PakAutoParts - Global Helper Functions
 */

declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Session;

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('format_pkr')) {
    function format_pkr(float|int|string|null $amount): string
    {
        $num = (float)($amount ?? 0);
        $symbol = Config::get('currency.symbol', 'Rs. ');
        return $symbol . number_format($num, 0, '.', ',');
    }
}

if (!function_exists('normalize_pak_phone')) {
    /**
     * Normalizes Pakistani mobile number (03XX-XXXXXXX or 03XXXXXXXXX) to +923XXXXXXXXX
     */
    function normalize_pak_phone(string $phone): string
    {
        // Strip non-numeric characters except leading +
        $clean = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($clean, '+92')) {
            return $clean;
        }

        if (str_starts_with($clean, '92') && strlen($clean) === 12) {
            return '+' . $clean;
        }

        if (str_starts_with($clean, '03') && strlen($clean) === 11) {
            return '+92' . substr($clean, 1);
        }

        if (str_starts_with($clean, '3') && strlen($clean) === 10) {
            return '+92' . $clean;
        }

        return $clean;
    }
}

if (!function_exists('whatsapp_clean_phone')) {
    /**
     * Generates clean digit-only phone for wa.me links: 923001234567
     */
    function whatsapp_clean_phone(string $phone): string
    {
        $normalized = normalize_pak_phone($phone);
        return ltrim($normalized, '+');
    }
}

if (!function_exists('format_phone_display')) {
    /**
     * Formats phone number for display: 0300-1234567
     */
    function format_phone_display(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 92, replace with 0
        if (str_starts_with($digits, '92') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2);
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '03')) {
            return substr($digits, 0, 4) . '-' . substr($digits, 4);
        }

        return $phone;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim(Config::get('app.url', ''), '/');
        $path = ltrim($path, '/');
        return $path === '' ? $base : "{$base}/{$path}";
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $cleanPath = ltrim($path, '/');
        $url = url($cleanPath);

        // In production, append timestamp for aggressive cache-busting
        $localPath = Config::get('paths.public') . '/' . $cleanPath;
        if (file_exists($localPath)) {
            $v = filemtime($localPath);
            return "{$url}?v={$v}";
        }

        return $url;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $oldData = Session::getFlash('_old_input', []);
        return $oldData[$key] ?? $default;
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        return empty($text) ? 'item-' . time() : $text;
    }
}

if (!function_exists('trans')) {
    function trans(string $key, array $replace = []): string
    {
        static $lang = null;
        if ($lang === null) {
            $langFile = dirname(__DIR__) . '/Lang/en.php';
            $lang = file_exists($langFile) ? require $langFile : [];
        }

        $translation = $lang[$key] ?? $key;
        foreach ($replace as $placeholder => $value) {
            $translation = str_replace(':' . $placeholder, (string)$value, $translation);
        }

        return $translation;
    }
}

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}

