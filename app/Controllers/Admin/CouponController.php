<?php
/**
 * PakAutoParts - Admin Coupon Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index(Request $request): Response
    {
        $coupons = Coupon::all('id DESC');

        return $this->view('admin/coupons/index', [
            'page_title' => 'Coupons & Discount Codes',
            'coupons'    => $coupons,
        ], 'layouts/admin');
    }

    public function save(Request $request): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $id = (int)$request->post('id', 0);
        $code = strtoupper(trim((string)$request->post('code')));
        $type = $request->post('discount_type', 'percent');
        $value = (float)$request->post('discount_value', 0);
        $minSpend = (float)$request->post('min_spend', 0);
        $maxDiscount = $request->filled('max_discount') ? (float)$request->post('max_discount') : null;
        $usageLimit = $request->filled('usage_limit') ? (int)$request->post('usage_limit') : null;
        $expiry = $request->filled('expiry_date') ? trim((string)$request->post('expiry_date')) : null;
        $active = (int)($request->post('is_active') ? 1 : 0);

        if (empty($code) || $value <= 0) {
            Session::flash('error', 'Please provide a coupon code and discount value.');
            return $this->back();
        }

        $data = [
            'code'           => $code,
            'discount_type'  => $type,
            'discount_value' => $value,
            'min_spend'      => $minSpend,
            'max_discount'   => $maxDiscount,
            'usage_limit'    => $usageLimit,
            'expiry_date'    => $expiry,
            'is_active'      => $active,
        ];

        if ($id > 0) {
            Database::update('coupons', $data, 'id = :id', ['id' => $id]);
            Session::flash('success', "Coupon '{$code}' updated.");
        } else {
            Database::insert('coupons', $data);
            Session::flash('success', "Coupon '{$code}' created.");
        }

        return $this->redirect('/admin/coupons');
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        Database::delete('coupons', 'id = :id', ['id' => (int)$id]);
        Session::flash('success', 'Coupon deleted.');
        return $this->redirect('/admin/coupons');
    }
}

