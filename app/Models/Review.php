<?php
/**
 * PakAutoParts - Review & Feedback Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Review extends Model
{
    protected static string $table = 'reviews';

    public static function forProduct(int $productId): array
    {
        return Database::select(
            "SELECT * FROM reviews WHERE product_id = :pid AND is_approved = 1 ORDER BY created_at DESC",
            ['pid' => $productId]
        );
    }

    public static function getAverageRating(int $productId): array
    {
        $row = Database::selectOne(
            "SELECT COUNT(*) as count, AVG(rating) as avg_rating FROM reviews WHERE product_id = :pid AND is_approved = 1",
            ['pid' => $productId]
        );

        return [
            'count'   => (int)($row['count'] ?? 0),
            'average' => round((float)($row['avg_rating'] ?? 5.0), 1),
        ];
    }
}

