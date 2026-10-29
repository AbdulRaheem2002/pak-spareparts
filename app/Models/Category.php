<?php
/**
 * PakAutoParts - Category Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function activeWithCounts(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.sort_order ASC, c.name ASC";
        return Database::select($sql);
    }
}

