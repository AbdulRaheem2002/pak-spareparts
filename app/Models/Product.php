<?php
/**
 * PakAutoParts - Product Model
 * Advanced catalog filtering, part-number-first search, fitment checks, and wholesale tiers.
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Product extends Model
{
    protected static string $table = 'products';

    public static function findWithDetails(int $id): ?array
    {
        $sql = "SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id LIMIT 1";

        $product = Database::selectOne($sql, ['id' => $id]);
        if (!$product) {
            return null;
        }

        $product['images'] = self::getImages((int)$product['id']);
        $product['fitment'] = self::getFitments((int)$product['id']);
        $product['tiers'] = self::getPriceTiers((int)$product['id']);

        return $product;
    }

    public static function findBySlugWithDetails(string $slug): ?array
    {
        $sql = "SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = :slug AND p.is_active = 1 LIMIT 1";

        $product = Database::selectOne($sql, ['slug' => $slug]);
        if (!$product) {
            return null;
        }

        $product['images'] = self::getImages((int)$product['id']);
        $product['fitment'] = self::getFitments((int)$product['id']);
        $product['tiers'] = self::getPriceTiers((int)$product['id']);

        return $product;
    }

    public static function getImages(int $productId): array
    {
        return Database::select(
            "SELECT * FROM product_images WHERE product_id = :pid ORDER BY is_primary DESC, sort_order ASC, id ASC",
            ['pid' => $productId]
        );
    }

    public static function getFitments(int $productId): array
    {
        $sql = "SELECT pf.*, v.make, v.model, v.year_start, v.year_end, v.engine, v.fuel_type
                FROM product_fitment pf
                JOIN vehicles v ON pf.vehicle_id = v.id
                WHERE pf.product_id = :pid
                ORDER BY v.make ASC, v.model ASC, v.year_start DESC";

        return Database::select($sql, ['pid' => $productId]);
    }

    public static function getPriceTiers(int $productId): array
    {
        return Database::select(
            "SELECT * FROM product_price_tiers WHERE product_id = :pid ORDER BY min_qty ASC",
            ['pid' => $productId]
        );
    }

    public static function getEffectivePrice(array $product, int $quantity = 1): float
    {
        $basePrice = (float)$product['price'];
        $tiers = self::getPriceTiers((int)$product['id']);

        // Find highest matching tier
        foreach (array_reverse($tiers) as $tier) {
            if ($quantity >= (int)$tier['min_qty']) {
                return (float)$tier['unit_price'];
            }
        }

        return $basePrice;
    }

    public static function fitsVehicle(int $productId, int $vehicleId): bool
    {
        $row = Database::selectOne(
            "SELECT id FROM product_fitment WHERE product_id = :pid AND vehicle_id = :vid LIMIT 1",
            ['pid' => $productId, 'vid' => $vehicleId]
        );
        return $row !== null;
    }

    /**
     * Search with SKU / OEM Number exact ranking first!
     */
    public static function searchCatalog(array $filters = [], int $page = 1, int $perPage = 24): array
    {
        $whereClauses = ['p.is_active = 1'];
        $params = [];
        $joinFitment = false;

        // 1. Part Number / Keyword Search
        $searchQuery = trim($filters['q'] ?? '');
        $orderBy = 'p.is_featured DESC, p.id DESC';

        if ($searchQuery !== '') {
            $cleanCode = preg_replace('/[^a-zA-Z0-9]/', '', $searchQuery);
            $whereClauses[] = "(
                p.sku = :exact_code OR 
                p.oem_number = :exact_oem OR 
                p.cross_references LIKE :cross_ref OR 
                MATCH(p.name, p.sku, p.oem_number, p.cross_references, p.short_description) AGAINST(:ft_query IN BOOLEAN MODE) OR
                p.name LIKE :like_query
            )";
            $params['exact_code'] = $searchQuery;
            $params['exact_oem'] = $searchQuery;
            $params['cross_ref'] = "%{$searchQuery}%";
            $params['ft_query'] = "+{$searchQuery}*";
            $params['like_query'] = "%{$searchQuery}%";

            // Prioritize exact SKU/OEM matches on top of search results
            $orderBy = "CASE WHEN p.sku = '{$searchQuery}' OR p.oem_number = '{$searchQuery}' THEN 1 ELSE 2 END ASC, p.id DESC";
        }

        // 2. Category Filter
        if (!empty($filters['category_id'])) {
            $whereClauses[] = 'p.category_id = :cat_id';
            $params['cat_id'] = (int)$filters['category_id'];
        } elseif (!empty($filters['category_slug'])) {
            $cat = Database::selectOne("SELECT id FROM categories WHERE slug = :cslug LIMIT 1", ['cslug' => $filters['category_slug']]);
            if ($cat) {
                $whereClauses[] = 'p.category_id = :cat_id';
                $params['cat_id'] = (int)$cat['id'];
            }
        }

        // 3. Brand Filter
        if (!empty($filters['brand_id'])) {
            $whereClauses[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = (int)$filters['brand_id'];
        }

        // 4. Condition Filter (Genuine, OEM, Aftermarket, Used)
        if (!empty($filters['condition'])) {
            $whereClauses[] = 'p.condition_type = :cond';
            $params['cond'] = $filters['condition'];
        }

        // 5. Price Range
        if (!empty($filters['min_price'])) {
            $whereClauses[] = 'p.price >= :min_price';
            $params['min_price'] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $whereClauses[] = 'p.price <= :max_price';
            $params['max_price'] = (float)$filters['max_price'];
        }

        // 6. In-Stock Only
        if (!empty($filters['in_stock'])) {
            $whereClauses[] = 'p.stock_quantity > 0';
        }

        // 7. Vehicle Fitment / "My Garage" Filter
        if (!empty($filters['vehicle_id'])) {
            $joinFitment = true;
            $whereClauses[] = 'pf.vehicle_id = :vehicle_id';
            $params['vehicle_id'] = (int)$filters['vehicle_id'];
        }

        // Custom Sort options
        if (!empty($filters['sort'])) {
            $orderBy = match ($filters['sort']) {
                'price_asc'  => 'p.price ASC',
                'price_desc' => 'p.price DESC',
                'name_asc'   => 'p.name ASC',
                'newest'     => 'p.id DESC',
                default      => $orderBy,
            };
        }

        $fromSql = "FROM products p
                    LEFT JOIN brands b ON p.brand_id = b.id
                    LEFT JOIN categories c ON p.category_id = c.id";

        if ($joinFitment) {
            $fromSql .= " INNER JOIN product_fitment pf ON p.id = pf.product_id";
        }

        $whereSql = implode(' AND ', $whereClauses);

        // Count total matching
        $countRow = Database::selectOne("SELECT COUNT(DISTINCT p.id) as total {$fromSql} WHERE {$whereSql}", $params);
        $total = (int)($countRow['total'] ?? 0);

        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $totalPages = (int)ceil($total / $perPage);

        // Fetch products with primary image
        $selectSql = "SELECT DISTINCT p.*, b.name as brand_name, c.name as category_name,
                      (SELECT thumbnail_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                      {$fromSql}
                      WHERE {$whereSql}
                      ORDER BY {$orderBy}
                      LIMIT {$perPage} OFFSET {$offset}";

        $items = Database::select($selectSql, $params);

        return [
            'data'         => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => $totalPages,
            'has_more'     => $page < $totalPages,
        ];
    }

    /**
     * Deducts stock inside an active transaction with SELECT ... FOR UPDATE row locking
     */
    public static function deductStockWithLock(int $productId, int $qty): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT id, stock_quantity FROM products WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $productId]);
        $row = $stmt->fetch();

        if (!$row || (int)$row['stock_quantity'] < $qty) {
            return false;
        }

        $newQty = (int)$row['stock_quantity'] - $qty;
        $upStmt = $db->prepare("UPDATE products SET stock_quantity = :qty WHERE id = :id");
        $upStmt->execute(['qty' => $newQty, 'id' => $productId]);

        return true;
    }
}

