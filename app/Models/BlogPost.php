<?php
/**
 * PakAutoParts - Blog Post Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class BlogPost extends Model
{
    protected static string $table = 'blog_posts';

    public static function published(int $page = 1, int $perPage = 9): array
    {
        return self::paginate(
            $page,
            $perPage,
            'is_published = 1',
            [],
            'published_at DESC, id DESC',
            'blog_posts.*, (SELECT name FROM blog_categories WHERE id = blog_posts.category_id) as category_name'
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        $sql = "SELECT bp.*, bc.name as category_name, bc.slug as category_slug
                FROM blog_posts bp
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                WHERE bp.slug = :slug AND bp.is_published = 1 LIMIT 1";

        return Database::selectOne($sql, ['slug' => $slug]);
    }

    public static function incrementViews(int $id): void
    {
        Database::query("UPDATE blog_posts SET view_count = view_count + 1 WHERE id = :id", ['id' => $id]);
    }
}

