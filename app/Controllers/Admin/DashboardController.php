<?php
/**
 * PakAutoParts - Admin Dashboard Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // 1. KPI Statistics
        $revRow = Database::selectOne("SELECT SUM(total) as revenue FROM orders WHERE status NOT IN ('Cancelled', 'Returned')");
        $totalRevenue = (float)($revRow['revenue'] ?? 0);

        $totalOrders = (int)(Database::selectOne("SELECT COUNT(*) as cnt FROM orders")['cnt'] ?? 0);
        $pendingOrders = (int)(Database::selectOne("SELECT COUNT(*) as cnt FROM orders WHERE status = 'Pending'")['cnt'] ?? 0);
        $lowStockCount = (int)(Database::selectOne("SELECT COUNT(*) as cnt FROM products WHERE stock_quantity <= low_stock_threshold AND is_active = 1")['cnt'] ?? 0);
        $blogViews = (int)(Database::selectOne("SELECT SUM(view_count) as views FROM blog_posts")['views'] ?? 0);

        // 2. Chart 1: Last 30 Days Revenue Line Chart
        $revenueData = Database::select(
            "SELECT DATE(created_at) as order_date, SUM(total) as daily_total
             FROM orders
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND status NOT IN ('Cancelled', 'Returned')
             GROUP BY DATE(created_at)
             ORDER BY order_date ASC"
        );

        $chartDates = [];
        $chartAmounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $chartDates[] = date('d M', strtotime($d));
            $found = 0;
            foreach ($revenueData as $row) {
                if ($row['order_date'] === $d) {
                    $found = (float)$row['daily_total'];
                    break;
                }
            }
            $chartAmounts[] = $found;
        }

        // 3. Chart 2: Top 5 Best Selling Products
        $topProducts = Database::select(
            "SELECT product_name, SUM(quantity) as units_sold
             FROM order_items
             GROUP BY product_name
             ORDER BY units_sold DESC
             LIMIT 5"
        );

        // 4. Chart 3: Order Status Distribution (Doughnut)
        $statusDistribution = Database::select(
            "SELECT status, COUNT(*) as count FROM orders GROUP BY status"
        );

        // 5. Recent 8 Orders
        $recentOrders = Database::select("SELECT * FROM orders ORDER BY id DESC LIMIT 8");

        return $this->view('admin/dashboard/index', [
            'page_title'        => 'Store Overview & Analytics',
            'totalRevenue'      => $totalRevenue,
            'totalOrders'       => $totalOrders,
            'pendingOrders'     => $pendingOrders,
            'lowStockCount'     => $lowStockCount,
            'blogViews'         => $blogViews,
            'chartDates'        => $chartDates,
            'chartAmounts'      => $chartAmounts,
            'topProducts'       => $topProducts,
            'statusDistribution'=> $statusDistribution,
            'recentOrders'      => $recentOrders,
        ], 'layouts/admin');
    }
}

