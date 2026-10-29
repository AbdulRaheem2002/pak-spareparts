<?php
/**
 * PakAutoParts - Admin Category Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = Category::activeWithCounts();

        return $this->view('admin/categories/index', [
            'page_title' => 'Product Categories',
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function save(Request $request): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $id = (int)$request->post('id', 0);
        $name = trim((string)$request->post('name'));
        $slug = slugify((string)$request->post('slug') ?: $name);
        $desc = trim((string)$request->post('description'));
        $sortOrder = (int)$request->post('sort_order', 0);

        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            return $this->back();
        }

        if ($id > 0) {
            Database::update('categories', [
                'name'        => $name,
                'slug'        => $slug,
                'description' => $desc,
                'sort_order'  => $sortOrder,
            ], 'id = :id', ['id' => $id]);
            Session::flash('success', "Category '{$name}' updated.");
        } else {
            Database::insert('categories', [
                'name'        => $name,
                'slug'        => $slug,
                'description' => $desc,
                'sort_order'  => $sortOrder,
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
            Session::flash('success', "Category '{$name}' created.");
        }

        return $this->redirect('/admin/categories');
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $catId = (int)$id;
        $inUse = Database::selectOne("SELECT COUNT(*) as cnt FROM products WHERE category_id = :id", ['id' => $catId]);
        if (!empty($inUse['cnt'])) {
            Session::flash('error', "Cannot delete category: contains {$inUse['cnt']} products.");
            return $this->back();
        }

        Database::delete('categories', 'id = :id', ['id' => $catId]);
        Session::flash('success', 'Category deleted.');
        return $this->redirect('/admin/categories');
    }
}

