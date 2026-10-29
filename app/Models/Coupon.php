<?php
/**
 * PakAutoParts - Coupon & Discount Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Coupon extends Model
{
    protected static string $table = 'coupons';

    public static function validateCoupon(string $code, float $cartSubtotal): array
    {
        $code = strtoupper(trim($code));
        $coupon = Database::selectOne(
            "SELECT * FROM coupons WHERE code = :code AND is_active = 1 LIMIT 1",
            ['code' => $code]
        );

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code.'];
        }

        $today = date('Y-m-d');
        if (!empty($coupon['start_date']) && $today < $coupon['start_date']) {
            return ['valid' => false, 'message' => 'This coupon has not started yet.'];
        }

        if (!empty($coupon['expiry_date']) && $today > $coupon['expiry_date']) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($coupon['usage_limit'] !== null && (int)$coupon['usage_count'] >= (int)$coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'Coupon usage limit has been reached.'];
        }

        if ($cartSubtotal < (float)$coupon['min_spend']) {
            return [
                'valid'   => false,
                'message' => 'Minimum spend of Rs. ' . number_format((float)$coupon['min_spend'], 0) . ' required for this coupon.'
            ];
        }

        // Calculate discount
        $discount = 0.0;
        if ($coupon['discount_type'] === 'percent') {
            $discount = ($cartSubtotal * (float)$coupon['discount_value']) / 100.0;
            if ($coupon['max_discount'] !== null && $discount > (float)$coupon['max_discount']) {
                $discount = (float)$coupon['max_discount'];
            }
        } else {
            $discount = (float)$coupon['discount_value'];
        }

        $discount = min($discount, $cartSubtotal);

        return [
            'valid'          => true,
            'code'           => $coupon['code'],
            'discount_type'  => $coupon['discount_type'],
            'discount_value' => (float)$coupon['discount_value'],
            'discount_amount'=> round($discount, 2),
            'message'        => 'Coupon applied! Discount: Rs. ' . number_format($discount, 0)
        ];
    }
}

