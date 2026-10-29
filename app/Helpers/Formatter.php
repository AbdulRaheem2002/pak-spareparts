<?php
/**
 * PakAutoParts - Data Formatter
 */

declare(strict_types=1);

namespace App\Helpers;

class Formatter
{
    public static function orderNumber(int|string $id): string
    {
        $numericId = (int)preg_replace('/[^0-9]/', '', (string)$id);
        $year = date('Y');
        return sprintf('SP-%s-%06d', $year, $numericId);
    }

    public static function date(?string $datetime, string $format = 'd M Y, h:i A'): string
    {
        if (!$datetime) {
            return '—';
        }
        $timestamp = strtotime($datetime);
        return $timestamp ? date($format, $timestamp) : $datetime;
    }

    public static function relativeTime(?string $datetime): string
    {
        if (!$datetime) {
            return '—';
        }
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = (int)floor($diff / 60);
            return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = (int)floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $days = (int)floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
        return date('d M Y', $timestamp);
    }

    public static function readingTime(string $content): int
    {
        $words = str_word_count(strip_tags($content));
        return max(1, (int)ceil($words / 200)); // 200 wpm standard
    }

    public static function truncate(string $text, int $limit = 100, string $end = '...'): string
    {
        $clean = strip_tags($text);
        if (mb_strlen($clean) <= $limit) {
            return $clean;
        }
        return rtrim(mb_substr($clean, 0, $limit)) . $end;
    }

    public static function conditionBadge(string $condition): string
    {
        $map = [
            'Genuine'     => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800',
            'OEM'         => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800',
            'Aftermarket' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800',
            'Used'        => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        ];

        $cls = $map[$condition] ?? 'bg-slate-100 text-slate-700 border-slate-300';
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border ' . $cls . '">' . htmlspecialchars($condition, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    public static function orderStatusBadge(string $status): string
    {
        $map = [
            'Pending'    => 'bg-amber-100 text-amber-800 border-amber-200',
            'Confirmed'  => 'bg-blue-100 text-blue-800 border-blue-200',
            'Dispatched' => 'bg-purple-100 text-purple-800 border-purple-200',
            'Delivered'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Cancelled'  => 'bg-rose-100 text-rose-800 border-rose-200',
            'Returned'   => 'bg-gray-100 text-gray-800 border-gray-300',
        ];

        $cls = $map[$status] ?? 'bg-slate-100 text-slate-700 border-slate-300';
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ' . $cls . '">' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</span>';
    }
}

