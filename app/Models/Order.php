<?php
/**
 * PakAutoParts - Order Model
 * High-reliability order management, atomic stock reservation, and courier tracking.
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Helpers\Formatter;

class Order extends Model
{
    protected static string $table = 'orders';

    public static function createWithItems(array $orderData, array $cartItems): array
    {
        Database::beginTransaction();

        try {
            // 1. Calculate next sequence number for SP-YYYY-XXXXXX
            $year = date('Y');
            $lastOrder = Database::selectOne("SELECT id FROM orders ORDER BY id DESC LIMIT 1");
            $nextSeq = ($lastOrder ? (int)$lastOrder['id'] : 0) + 1;
            $orderNumber = Formatter::orderNumber($nextSeq);
            $orderData['order_number'] = $orderNumber;

            // 2. Insert main order
            $orderId = Database::insert('orders', $orderData);

            // 3. Deduct stock and insert order items inside transaction
            foreach ($cartItems as $item) {
                $productId = (int)$item['product_id'];
                $qty = (int)$item['qty'];

                // Deduct stock with row lock
                $deducted = Product::deductStockWithLock($productId, $qty);
                if (!$deducted) {
                    throw new \RuntimeException("Insufficient stock for item: " . ($item['name'] ?? 'Product'));
                }

                // Insert item record
                Database::insert('order_items', [
                    'order_id'     => $orderId,
                    'product_id'   => $productId,
                    'product_name' => $item['name'],
                    'sku'          => $item['sku'],
                    'oem_number'   => $item['oem_number'],
                    'unit_price'   => $item['price'],
                    'quantity'     => $qty,
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            // 4. Log initial order history
            Database::insert('order_status_history', [
                'order_id'   => $orderId,
                'status'     => $orderData['status'] ?? 'Pending',
                'comment'    => 'Order placed successfully by customer via ' . strtoupper($orderData['payment_method'] ?? 'COD'),
                'created_by' => 'System',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            Database::commit();

            return [
                'success'      => true,
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
            ];
        } catch (\Throwable $e) {
            Database::rollBack();
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    public static function findByNumberAndPhone(string $orderNumber, string $phone): ?array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $order = Database::selectOne(
            "SELECT * FROM orders WHERE order_number = :onum LIMIT 1",
            ['onum' => trim($orderNumber)]
        );

        if (!$order) {
            return null;
        }

        $orderCleanPhone = preg_replace('/[^0-9]/', '', $order['customer_phone']);
        // Match last 7 digits for Pakistani phone resilience
        if (substr($cleanPhone, -7) !== substr($orderCleanPhone, -7)) {
            return null;
        }

        $order['items'] = self::getItems((int)$order['id']);
        $order['history'] = self::getHistory((int)$order['id']);

        return $order;
    }

    public static function getItems(int $orderId): array
    {
        return Database::select("SELECT * FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);
    }

    public static function getHistory(int $orderId): array
    {
        return Database::select(
            "SELECT * FROM order_status_history WHERE order_id = :oid ORDER BY created_at ASC",
            ['oid' => $orderId]
        );
    }

    public static function updateStatus(int $orderId, string $newStatus, string $comment = '', string $updatedBy = 'Admin'): void
    {
        Database::update('orders', [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $orderId]);

        Database::insert('order_status_history', [
            'order_id'   => $orderId,
            'status'     => $newStatus,
            'comment'    => $comment,
            'created_by' => $updatedBy,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function generateWhatsAppMessage(array $order, array $items): string
    {
        $lines = [];
        $lines[] = "⚡ *NEW AUTO SPARE PARTS ORDER*";
        $lines[] = "📋 *Order ID:* " . $order['order_number'];
        $lines[] = "👤 *Customer:* " . $order['customer_name'];
        $lines[] = "📞 *Phone:* " . $order['customer_phone'];
        $lines[] = "📍 *City:* " . $order['delivery_city'];
        $lines[] = "🏠 *Address:* " . $order['delivery_address'];
        $lines[] = "💳 *Payment:* " . strtoupper($order['payment_method']);
        $lines[] = "";
        $lines[] = "📦 *ORDER ITEMS:*";

        foreach ($items as $idx => $item) {
            $num = $idx + 1;
            $lines[] = "{$num}. *{$item['product_name']}*";
            $lines[] = "   OEM: {$item['oem_number']} | Qty: {$item['quantity']}x | Subtotal: Rs. " . number_format((float)$item['subtotal'], 0);
        }

        $lines[] = "";
        $lines[] = "💰 *Subtotal:* Rs. " . number_format((float)$order['subtotal'], 0);
        if ((float)$order['discount_amount'] > 0) {
            $lines[] = "🏷️ *Discount:* -Rs. " . number_format((float)$order['discount_amount'], 0);
        }
        $lines[] = "🚚 *Shipping Fee:* Rs. " . number_format((float)$order['shipping_fee'], 0);
        if ((float)$order['cod_fee'] > 0) {
            $lines[] = "💵 *COD Fee:* Rs. " . number_format((float)$order['cod_fee'], 0);
        }
        $lines[] = "💎 *TOTAL PAYABLE:* Rs. " . number_format((float)$order['total'], 0);
        $lines[] = "";
        $lines[] = "Please confirm this order for immediate dispatch via courier. Thank you!";

        return implode("\n", $lines);
    }
}

