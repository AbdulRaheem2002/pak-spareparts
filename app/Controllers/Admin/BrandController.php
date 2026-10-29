<?php
/**
 * PakAutoParts - Admin Brand Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index(Request $request): Response
    {
        $brands = Brand::withProductCount();

        return $this->view('admin/brands/index', [
            'page_title' => 'Parts Brands & Manufacturers',
            'brands'     => $brands,
        ], 'layouts/admin');
    }

    public function save(Request $request): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $id = (int)$request->post('id', 0);
        $name = trim((string)$request->post('name'));
        $slug = slugify((string)$request->post('slug') ?: $name);
        $desc = trim((string)$request->post('description'));
        $popular = (int)($request->post('is_popular') ? 1 : 0);

        if (empty($name)) {
            Session::flash('error', 'Brand name is required.');
            return $this->back();
        }

        if ($id > 0) {
            Database::update('brands', [
                'name'        => $name,
                'slug'        => $slug,
                'description' => $desc,
                'is_popular'  => $popular,
            ], 'id = :id', ['id' => $id]);
            Session::flash('success', "Brand '{$name}' updated.");
        } else {
            Database::insert('brands', [
                'name'        => $name,
                'slug'        => $slug,
                'description' => $desc,
                'is_popular'  => $popular,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
            Session::flash('success', "Brand '{$name}' created.");
        }

        return $this->redirect('/admin/brands');
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $brandId = (int)$id;
        $inUse = Database::selectOne("SELECT COUNT(*) as cnt FROM products WHERE brand_id = :id", ['id' => $brandId]);
        if (!empty($inUse['cnt'])) {
            Session::flash('error', "Cannot delete brand: assigned to {$inUse['cnt']} products.");
            return $this->back();
        }

        Database::delete('brands', 'id = :id', ['id' => $brandId]);
        Session::flash('success', 'Brand deleted.');
        return $this->redirect('/admin/brands');
    }
}

