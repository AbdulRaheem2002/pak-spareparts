<?php
/**
 * PakAutoParts - Cart Controller
 * Server-authoritative price & discount calculation (tamper-proof).
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Coupon;
use App\Core\Database;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('cart/index', [
            'title' => 'Shopping Cart | PakAutoParts',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Shopping Cart', 'url' => ''],
            ]
        ]);
    }

    /**
     * AJAX endpoint: receives client cart items [{product_id, qty}],
     * recomputes prices from DB, applies wholesale tiers, verifies stock.
     */
    public function sync(Request $request): Response
    {
        $rawItems = $request->json('items') ?? [];
        if (!is_array($rawItems)) {
            $rawItems = [];
        }

        $items = [];
        $subtotal = 0.0;
        $totalWeight = 0.0;

        foreach ($rawItems as $raw) {
            $productId = (int)($raw['product_id'] ?? 0);
            $qty = max(1, (int)($raw['qty'] ?? 1));

            if ($productId <= 0) continue;

            $product = Product::findWithDetails($productId);
            if (!$product || (int)$product['is_active'] !== 1) {
                continue;
            }

            // Calculate price based on wholesale volume tiers
            $unitPrice = Product::getEffectivePrice($product, $qty);
            $itemSubtotal = $unitPrice * $qty;
            $subtotal += $itemSubtotal;
            $totalWeight += ((float)($product['weight_kg'] ?? 1.0)) * $qty;

            $images = $product['images'] ?? [];
            $img = !empty($images[0]['thumbnail_url']) ? $images[0]['thumbnail_url'] : '/assets/images/placeholder.svg';

            $items[] = [
                'product_id'   => (int)$product['id'],
                'name'         => $product['name'],
                'slug'         => $product['slug'],
                'sku'          => $product['sku'],
                'oem_number'   => $product['oem_number'],
                'brand_name'   => $product['brand_name'] ?? 'Genuine',
                'image'        => $img,
                'price'        => $unitPrice,
                'original_price'=> (float)$product['price'],
                'has_tier_discount' => $unitPrice < (float)$product['price'],
                'stock'        => (int)$product['stock_quantity'],
                'qty'          => min($qty, (int)$product['stock_quantity']),
                'subtotal'     => $itemSubtotal,
            ];
        }

        // Check coupon if provided
        $couponCode = trim((string)$request->json('coupon_code', ''));
        $couponData = null;
        $discountAmount = 0.0;

        if ($couponCode !== '' && $subtotal > 0) {
            $res = Coupon::validateCoupon($couponCode, $subtotal);
            if ($res['valid']) {
                $couponData = $res;
                $discountAmount = (float)$res['discount_amount'];
            }
        }

        $freeShippingThreshold = (float)setting('free_shipping_min', 5000);
        $defaultShippingFee = (float)setting('default_shipping_fee', 250);
        $shippingFee = ($subtotal >= $freeShippingThreshold || $subtotal == 0) ? 0.0 : $defaultShippingFee;
        $total = max(0, $subtotal - $discountAmount + $shippingFee);

        return $this->json([
            'items'                 => $items,
            'subtotal'              => $subtotal,
            'discount'              => $discountAmount,
            'coupon'                => $couponData,
            'shipping'              => $shippingFee,
            'free_shipping_eligible'=> $subtotal >= $freeShippingThreshold,
            'free_shipping_min'     => $freeShippingThreshold,
            'total'                 => $total,
        ]);
    }

    public function applyCoupon(Request $request): Response
    {
        $code = trim((string)$request->json('code', ''));
        $subtotal = (float)$request->json('subtotal', 0);

        if (empty($code)) {
            return $this->json(['valid' => false, 'message' => 'Please enter a coupon code.']);
        }

        $result = Coupon::validateCoupon($code, $subtotal);
        return $this->json($result);
    }
}

