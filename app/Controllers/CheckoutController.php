<?php
/**
 * PakAutoParts - Checkout & Order Processing Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\ImageHandler;
use App\Core\RateLimiter;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;

class CheckoutController extends Controller
{
    public function index(Request $request): Response
    {
        // Load shipping rates by city
        $rates = Database::select("SELECT * FROM shipping_rates ORDER BY city ASC");

        return $this->view('checkout/index', [
            'title'         => 'Checkout | PakAutoParts',
            'shippingRates' => $rates,
            'breadcrumbs'   => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Shopping Cart', 'url' => '/cart'],
                ['title' => 'Checkout', 'url' => ''],
            ]
        ]);
    }

    public function process(Request $request): Response
    {
        $ip = $request->ip();
        if (RateLimiter::tooManyAttempts("checkout_ip_{$ip}", 5, 300)) {
            Session::flash('error', 'Too many checkout attempts. Please wait a few minutes.');
            return $this->back();
        }

        // 1. Validate Form Input
        $errors = $this->validate($request->all(), [
            'customer_name'    => 'required|min:3|max:80',
            'customer_phone'   => 'required|min:10|max:20',
            'delivery_city'    => 'required|min:2|max:60',
            'delivery_address' => 'required|min:5|max:255',
            'payment_method'   => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            return $this->back();
        }

        // 2. Decode Cart Items from Request
        $cartJson = $request->post('cart_data', '[]');
        $rawItems = json_decode((string)$cartJson, true);

        if (empty($rawItems) || !is_array($rawItems)) {
            Session::flash('error', 'Your shopping cart is empty.');
            return $this->redirect('/cart');
        }

        // 3. Authoritatively recalculate items and subtotal from DB
        $validatedItems = [];
        $subtotal = 0.0;

        foreach ($rawItems as $raw) {
            $productId = (int)($raw['product_id'] ?? 0);
            $qty = max(1, (int)($raw['qty'] ?? 1));

            $product = Product::findWithDetails($productId);
            if (!$product || (int)$product['is_active'] !== 1) {
                continue;
            }

            // Check stock
            if ((int)$product['stock_quantity'] < $qty) {
                Session::flash('error', "Not enough stock for {$product['name']}. Available: {$product['stock_quantity']}");
                return $this->redirect('/cart');
            }

            $unitPrice = Product::getEffectivePrice($product, $qty);
            $itemSubtotal = $unitPrice * $qty;
            $subtotal += $itemSubtotal;

            $validatedItems[] = [
                'product_id'   => (int)$product['id'],
                'name'         => $product['name'],
                'sku'          => $product['sku'],
                'oem_number'   => $product['oem_number'],
                'price'        => $unitPrice,
                'qty'          => $qty,
                'subtotal'     => $itemSubtotal,
            ];
        }

        if (empty($validatedItems)) {
            Session::flash('error', 'No valid items found in your order.');
            return $this->redirect('/cart');
        }

        // 4. Validate Coupon if applied
        $couponCode = strtoupper(trim((string)$request->post('coupon_code', '')));
        $discountAmount = 0.0;

        if ($couponCode !== '') {
            $couponResult = Coupon::validateCoupon($couponCode, $subtotal);
            if ($couponResult['valid']) {
                $discountAmount = (float)$couponResult['discount_amount'];
            }
        }

        // 5. Determine City Shipping Rates & COD Fees
        $city = trim((string)$request->post('delivery_city'));
        $shippingRow = Database::selectOne("SELECT * FROM shipping_rates WHERE city = :c LIMIT 1", ['c' => $city]);

        $standardShipping = $shippingRow ? (float)$shippingRow['standard_rate'] : (float)setting('default_shipping_fee', 250);
        $freeThreshold = $shippingRow ? (float)$shippingRow['free_threshold'] : (float)setting('free_shipping_min', 5000);
        $codFeeAmount = (float)($shippingRow['cod_fee'] ?? setting('cod_fee', 50));

        $shippingFee = ($subtotal >= $freeThreshold) ? 0.0 : $standardShipping;
        $paymentMethod = strtolower((string)$request->post('payment_method'));
        $codFee = ($paymentMethod === 'cod') ? $codFeeAmount : 0.0;

        $total = max(0, $subtotal - $discountAmount + $shippingFee + $codFee);

        // 6. Handle Payment Proof Screenshot Upload (if bank transfer / wallet)
        $paymentProofUrl = null;
        if (in_array($paymentMethod, ['bank_transfer', 'jazzcash', 'easypaisa'], true)) {
            $file = $request->file('payment_proof');
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                try {
                    $upload = ImageHandler::processUpload($file, 'payments', false);
                    $paymentProofUrl = $upload['url'];
                } catch (\Throwable $e) {
                    Session::flash('error', 'Payment screenshot error: ' . $e->getMessage());
                    return $this->back();
                }
            }
        }

        // 7. Assemble Order Record
        $normalizedPhone = normalize_pak_phone((string)$request->post('customer_phone'));
        $orderData = [
            'customer_name'       => trim((string)$request->post('customer_name')),
            'customer_email'      => trim((string)$request->post('customer_email')) ?: null,
            'customer_phone'      => $normalizedPhone,
            'delivery_city'       => $city,
            'delivery_address'    => trim((string)$request->post('delivery_address')),
            'notes'               => trim((string)$request->post('notes')) ?: null,
            'payment_method'      => $paymentMethod,
            'payment_status'      => $paymentProofUrl ? 'verified' : 'pending',
            'payment_proof_image' => $paymentProofUrl,
            'shipping_fee'        => $shippingFee,
            'cod_fee'             => $codFee,
            'discount_amount'     => $discountAmount,
            'subtotal'            => $subtotal,
            'total'               => $total,
            'coupon_code'         => $couponCode ?: null,
            'status'              => 'Pending',
            'created_at'          => date('Y-m-d H:i:s'),
        ];

        // 8. Execute Atomic Order Creation & Stock Lock
        $result = Order::createWithItems($orderData, $validatedItems);

        if (!$result['success']) {
            Session::flash('error', 'Order placement failed: ' . $result['error']);
            return $this->redirect('/cart');
        }

        // Increment coupon count if used
        if ($couponCode !== '') {
            Database::query("UPDATE coupons SET usage_count = usage_count + 1 WHERE code = :c", ['c' => $couponCode]);
        }

        RateLimiter::hit("checkout_ip_{$ip}", 300);

        // 9. If Buy via WhatsApp: build formatted wa.me link
        $orderNumber = $result['order_number'];
        if ($paymentMethod === 'whatsapp') {
            $orderData['order_number'] = $orderNumber;
            $msg = Order::generateWhatsAppMessage($orderData, $validatedItems);
            $ownerWa = setting('contact_whatsapp', '923001234567');
            $waUrl = "https://wa.me/{$ownerWa}?text=" . urlencode($msg);

            // Redirect user directly to WhatsApp confirmation
            return $this->redirect($waUrl);
        }

        return $this->redirect("/order/success/{$orderNumber}");
    }

    public function success(Request $request, string $orderNumber): Response
    {
        $order = Database::selectOne("SELECT * FROM orders WHERE order_number = :num LIMIT 1", ['num' => $orderNumber]);
        if (!$order) {
            return $this->redirect('/');
        }

        $items = Order::getItems((int)$order['id']);

        return $this->view('checkout/success', [
            'title'       => "Order {$orderNumber} Confirmed | PakAutoParts",
            'order'       => $order,
            'items'       => $items,
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Order Success', 'url' => ''],
            ]
        ]);
    }
}

