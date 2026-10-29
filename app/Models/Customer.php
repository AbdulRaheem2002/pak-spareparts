<?php
/**
 * PakAutoParts - Customer Aggregation Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Customer extends Model
{
    public static function allWithOrderSummary(int $page = 1, int $perPage = 25): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $countRow = Database::selectOne(
            "SELECT COUNT(DISTINCT customer_phone) as total FROM orders WHERE customer_phone IS NOT NULL AND customer_phone != ''"
        );
        $total = (int)($countRow['total'] ?? 0);

        $sql = "SELECT 
                    customer_name, 
                    customer_phone, 
                    customer_email, 
                    delivery_city,
                    COUNT(id) as total_orders, 
                    SUM(total) as total_spent, 
                    MAX(created_at) as last_order_date
                FROM orders 
                WHERE customer_phone IS NOT NULL AND customer_phone != ''
                GROUP BY customer_phone, customer_name, customer_email, delivery_city
                ORDER BY total_spent DESC, last_order_date DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $items = Database::select($sql);

        return [
            'data'         => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / $perPage),
        ];
    }
}

