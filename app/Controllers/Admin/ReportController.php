<?php
/**
 * PakAutoParts - Admin Inventory & Financial Reports Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        // 1. Low Stock Parts (Urgent Re-order List)
        $lowStockParts = Database::select(
            "SELECT p.*, b.name as brand_name, c.name as category_name
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.stock_quantity <= p.low_stock_threshold AND p.is_active = 1
             ORDER BY p.stock_quantity ASC"
        );

        // 2. Inventory Valuation
        $valRow = Database::selectOne(
            "SELECT 
                COUNT(*) as total_skus,
                SUM(stock_quantity) as total_units,
                SUM(stock_quantity * price) as retail_valuation,
                SUM(stock_quantity * COALESCE(cost_price, price * 0.7)) as estimated_cost_valuation
             FROM products
             WHERE is_active = 1"
        );

        // 3. Fast-Moving Parts (by units ordered)
        $fastMoving = Database::select(
            "SELECT oi.product_name, oi.sku, oi.oem_number, SUM(oi.quantity) as total_qty_sold, SUM(oi.subtotal) as total_revenue
             FROM order_items oi
             GROUP BY oi.product_name, oi.sku, oi.oem_number
             ORDER BY total_qty_sold DESC
             LIMIT 10"
        );

        return $this->view('admin/reports/index', [
            'page_title'    => 'Inventory Valuation & Low Stock Report',
            'lowStockParts' => $lowStockParts,
            'valuation'     => $valRow,
            'fastMoving'    => $fastMoving,
        ], 'layouts/admin');
    }
}

