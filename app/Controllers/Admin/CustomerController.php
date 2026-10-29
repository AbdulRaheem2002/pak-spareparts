<?php
/**
 * PakAutoParts - Admin Customer Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $page = (int)$request->get('page', 1);
        $customers = Customer::allWithOrderSummary($page, 25);

        return $this->view('admin/customers/index', [
            'page_title' => 'Customer Directory & Order History',
            'customers'  => $customers,
        ], 'layouts/admin');
    }

    public function exportCsv(Request $request): void
    {
        $customers = Database::select(
            "SELECT customer_name, customer_phone, customer_email, delivery_city,
                    COUNT(id) as total_orders, SUM(total) as total_spent, MAX(created_at) as last_order
             FROM orders
             WHERE customer_phone IS NOT NULL AND customer_phone != ''
             GROUP BY customer_phone, customer_name, customer_email, delivery_city
             ORDER BY total_spent DESC"
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=pakautoparts_customers_' . date('Y-m-d') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Customer Name', 'Phone', 'Email', 'City', 'Total Orders', 'Total Spent (PKR)', 'Last Order Date']);

        foreach ($customers as $c) {
            fputcsv($out, [
                $c['customer_name'],
                $c['customer_phone'],
                $c['customer_email'],
                $c['delivery_city'],
                $c['total_orders'],
                $c['total_spent'],
                $c['last_order'],
            ]);
        }

        fclose($out);
        exit;
    }
}

