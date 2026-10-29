<?php
/**
 * PakAutoParts - Admin Order Management & Printing Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->get('status');
        $q = trim((string)$request->get('q', ''));
        $payment = $request->get('payment_method');
        $page = (int)$request->get('page', 1);

        $where = ['1=1'];
        $params = [];

        if (!empty($status)) {
            $where[] = 'status = :st';
            $params['st'] = $status;
        }

        if (!empty($payment)) {
            $where[] = 'payment_method = :pm';
            $params['pm'] = $payment;
        }

        if ($q !== '') {
            $where[] = '(order_number LIKE :q OR customer_name LIKE :q OR customer_phone LIKE :q)';
            $params['q'] = "%{$q}%";
        }

        $whereSql = implode(' AND ', $where);
        $total = (int)(Database::selectOne("SELECT COUNT(*) as cnt FROM orders WHERE {$whereSql}", $params)['cnt'] ?? 0);

        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $orders = Database::select(
            "SELECT * FROM orders WHERE {$whereSql} ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return $this->view('admin/orders/index', [
            'page_title' => 'Customer Orders & Fulfillment',
            'orders'     => $orders,
            'total'      => $total,
            'page'       => $page,
            'lastPage'   => (int)ceil($total / $perPage),
            'status'     => $status,
            'payment'    => $payment,
            'q'          => $q,
        ], 'layouts/admin');
    }

    public function show(Request $request, string $id): Response
    {
        $order = Database::selectOne("SELECT * FROM orders WHERE id = :id LIMIT 1", ['id' => (int)$id]);
        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $this->redirect('/admin/orders');
        }

        $items = Order::getItems((int)$order['id']);
        $history = Order::getHistory((int)$order['id']);

        return $this->view('admin/orders/show', [
            'page_title' => "Order #{$order['order_number']}",
            'order'      => $order,
            'items'      => $items,
            'history'    => $history,
        ], 'layouts/admin');
    }

    public function updateStatus(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $orderId = (int)$id;
        $status = $request->post('status');
        $courier = trim((string)$request->post('courier_name', ''));
        $tracking = trim((string)$request->post('tracking_number', ''));
        $comment = trim((string)$request->post('comment', 'Status updated by store manager'));

        $updateData = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($courier !== '') $updateData['courier_name'] = $courier;
        if ($tracking !== '') $updateData['tracking_number'] = $tracking;

        Database::update('orders', $updateData, 'id = :id', ['id' => $orderId]);

        Database::insert('order_status_history', [
            'order_id'   => $orderId,
            'status'     => $status,
            'comment'    => $comment . ($tracking ? " (Tracking: {$tracking} via {$courier})" : ""),
            'created_by' => 'Store Staff',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', "Order status updated to {$status}.");
        return $this->back();
    }

    public function invoiceA4(Request $request, string $id): Response
    {
        $order = Database::selectOne("SELECT * FROM orders WHERE id = :id LIMIT 1", ['id' => (int)$id]);
        if (!$order) die("Order not found");
        $items = Order::getItems((int)$order['id']);

        return $this->view('admin/orders/invoice_a4', [
            'order' => $order,
            'items' => $items,
        ], null); // Render without admin wrapper for crisp printing
    }

    public function invoiceThermal(Request $request, string $id): Response
    {
        $order = Database::selectOne("SELECT * FROM orders WHERE id = :id LIMIT 1", ['id' => (int)$id]);
        if (!$order) die("Order not found");
        $items = Order::getItems((int)$order['id']);

        return $this->view('admin/orders/invoice_thermal', [
            'order' => $order,
            'items' => $items,
        ], null);
    }

    public function packingSlip(Request $request, string $id): Response
    {
        $order = Database::selectOne("SELECT * FROM orders WHERE id = :id LIMIT 1", ['id' => (int)$id]);
        if (!$order) die("Order not found");
        $items = Order::getItems((int)$order['id']);

        return $this->view('admin/orders/packing_slip', [
            'order' => $order,
            'items' => $items,
        ], null);
    }

    public function exportCsv(Request $request): void
    {
        $orders = Database::select(
            "SELECT o.order_number, o.customer_name, o.customer_phone, o.customer_email, o.delivery_city,
                    o.delivery_address, o.payment_method, o.payment_status, o.subtotal, o.shipping_fee,
                    o.discount_amount, o.total, o.status, o.courier_name, o.tracking_number, o.created_at
             FROM orders o
             ORDER BY o.id DESC"
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=pakautoparts_orders_' . date('Y-m-d') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Order #', 'Customer Name', 'Phone', 'Email', 'City', 'Address', 'Payment', 'Payment Status', 'Subtotal', 'Shipping', 'Discount', 'Total (PKR)', 'Status', 'Courier', 'Tracking #', 'Order Date']);

        foreach ($orders as $o) {
            fputcsv($out, [
                $o['order_number'],
                $o['customer_name'],
                $o['customer_phone'],
                $o['customer_email'],
                $o['delivery_city'],
                $o['delivery_address'],
                $o['payment_method'],
                $o['payment_status'],
                $o['subtotal'],
                $o['shipping_fee'],
                $o['discount_amount'],
                $o['total'],
                $o['status'],
                $o['courier_name'],
                $o['tracking_number'],
                $o['created_at'],
            ]);
        }

        fclose($out);
        exit;
    }
}

