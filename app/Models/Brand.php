<?php
/**
 * PakAutoParts - Brand Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Brand extends Model
{
    protected static string $table = 'brands';

    public static function popular(): array
    {
        return Database::select("SELECT * FROM brands WHERE is_popular = 1 ORDER BY name ASC");
    }

    public static function withProductCount(): array
    {
        $sql = "SELECT b.*, COUNT(p.id) as product_count
                FROM brands b
                LEFT JOIN products p ON b.id = p.brand_id AND p.is_active = 1
                GROUP BY b.id
                ORDER BY b.name ASC";
        return Database::select($sql);
    }
}

