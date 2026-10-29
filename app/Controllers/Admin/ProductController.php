<?php
/**
 * PakAutoParts - Admin Product Management Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\ImageHandler;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Vehicle;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $q = trim((string)$request->get('q', ''));
        $catId = $request->get('category_id');
        $lowStock = $request->get('low_stock');
        $page = (int)$request->get('page', 1);

        $where = ['1=1'];
        $params = [];

        if ($q !== '') {
            $where[] = "(p.name LIKE :q OR p.sku LIKE :q OR p.oem_number LIKE :q)";
            $params['q'] = "%{$q}%";
        }

        if (!empty($catId)) {
            $where[] = "p.category_id = :cat_id";
            $params['cat_id'] = (int)$catId;
        }

        if (!empty($lowStock)) {
            $where[] = "p.stock_quantity <= p.low_stock_threshold";
        }

        $whereSql = implode(' AND ', $where);
        $total = (int)(Database::selectOne("SELECT COUNT(*) as cnt FROM products p WHERE {$whereSql}", $params)['cnt'] ?? 0);

        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                (SELECT thumbnail_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as thumbnail
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE {$whereSql}
                ORDER BY p.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $products = Database::select($sql, $params);
        $categories = Category::all('name ASC');

        return $this->view('admin/products/index', [
            'page_title' => 'Product Inventory Management',
            'products'   => $products,
            'categories' => $categories,
            'total'      => $total,
            'page'       => $page,
            'lastPage'   => (int)ceil($total / $perPage),
            'q'          => $q,
            'catId'      => $catId,
            'lowStock'   => $lowStock,
        ], 'layouts/admin');
    }

    public function create(Request $request): Response
    {
        $categories = Category::all('name ASC');
        $brands = Brand::all('name ASC');
        $vehicles = Vehicle::all('make ASC, model ASC, year_start DESC');

        return $this->view('admin/products/form', [
            'page_title' => 'Add New Spare Part',
            'product'    => null,
            'categories' => $categories,
            'brands'     => $brands,
            'vehicles'   => $vehicles,
            'fitmentIds' => [],
            'tiers'      => [],
            'images'     => [],
        ], 'layouts/admin');
    }

    public function store(Request $request): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $errors = $this->validate($request->all(), [
            'name'           => 'required|min:3|max:200',
            'sku'            => 'required|min:2|max:60',
            'oem_number'     => 'required|min:2|max:100',
            'price'          => 'required|numeric',
            'category_id'    => 'required',
            'brand_id'       => 'required',
            'stock_quantity' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            return $this->back();
        }

        $name = trim((string)$request->post('name'));
        $slug = slugify($name);

        // Ensure unique slug
        $existing = Database::selectOne("SELECT id FROM products WHERE slug = :s LIMIT 1", ['s' => $slug]);
        if ($existing) {
            $slug .= '-' . time();
        }

        $productId = Database::insert('products', [
            'name'                => $name,
            'slug'                => $slug,
            'sku'                 => strtoupper(trim((string)$request->post('sku'))),
            'oem_number'          => strtoupper(trim((string)$request->post('oem_number'))),
            'cross_references'    => trim((string)$request->post('cross_references')) ?: null,
            'brand_id'            => (int)$request->post('brand_id'),
            'category_id'         => (int)$request->post('category_id'),
            'condition_type'      => $request->post('condition_type', 'Genuine'),
            'warranty'            => trim((string)$request->post('warranty', '7-Day Return / 1-Month Warranty')),
            'short_description'   => trim((string)$request->post('short_description')) ?: null,
            'description'         => $request->post('description', ''),
            'price'               => (float)$request->post('price'),
            'compare_at_price'    => $request->filled('compare_at_price') ? (float)$request->post('compare_at_price') : null,
            'cost_price'          => $request->filled('cost_price') ? (float)$request->post('cost_price') : null,
            'stock_quantity'      => (int)$request->post('stock_quantity', 0),
            'low_stock_threshold' => (int)$request->post('low_stock_threshold', 3),
            'is_featured'         => (int)($request->post('is_featured') ? 1 : 0),
            'is_active'           => (int)($request->post('is_active') ? 1 : 0),
            'weight_kg'           => (float)($request->post('weight_kg', 1.0)),
            'meta_title'          => trim((string)$request->post('meta_title')) ?: null,
            'meta_description'    => trim((string)$request->post('meta_description')) ?: null,
            'created_at'          => date('Y-m-d H:i:s'),
        ]);

        // Save Fitments
        $vehicleIds = (array)$request->post('vehicle_ids', []);
        foreach ($vehicleIds as $vid) {
            $vidInt = (int)$vid;
            if ($vidInt > 0) {
                Database::insert('product_fitment', [
                    'product_id' => $productId,
                    'vehicle_id' => $vidInt,
                    'notes'      => 'Confirmed OE fitment',
                ]);
            }
        }

        // Save Price Tiers
        $tierMinQtys = (array)$request->post('tier_min_qty', []);
        $tierPrices = (array)$request->post('tier_price', []);
        for ($i = 0; $i < count($tierMinQtys); $i++) {
            $tQty = (int)($tierMinQtys[$i] ?? 0);
            $tPrice = (float)($tierPrices[$i] ?? 0);
            if ($tQty > 1 && $tPrice > 0) {
                Database::insert('product_price_tiers', [
                    'product_id' => $productId,
                    'min_qty'    => $tQty,
                    'unit_price' => $tPrice,
                ]);
            }
        }

        // Handle Image Uploads
        $files = $_FILES['images'] ?? null;
        if ($files && is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if (!empty($files['name'][$i]) && $files['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    try {
                        $up = ImageHandler::processUpload($singleFile, 'products', true);
                        Database::insert('product_images', [
                            'product_id'    => $productId,
                            'image_url'     => $up['url'],
                            'thumbnail_url' => $up['thumbnail_url'],
                            'is_primary'    => ($i === 0) ? 1 : 0,
                            'sort_order'    => $i,
                        ]);
                    } catch (\Throwable $e) {}
                }
            }
        }

        Session::flash('success', "Part '{$name}' created successfully.");
        return $this->redirect('/admin/products');
    }

    public function edit(Request $request, string $id): Response
    {
        $product = Product::findWithDetails((int)$id);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            return $this->redirect('/admin/products');
        }

        $categories = Category::all('name ASC');
        $brands = Brand::all('name ASC');
        $vehicles = Vehicle::all('make ASC, model ASC, year_start DESC');
        $fitmentIds = array_column($product['fitment'], 'vehicle_id');
        $tiers = $product['tiers'];
        $images = $product['images'];

        return $this->view('admin/products/form', [
            'page_title' => "Edit Part: {$product['name']}",
            'product'    => $product,
            'categories' => $categories,
            'brands'     => $brands,
            'vehicles'   => $vehicles,
            'fitmentIds' => $fitmentIds,
            'tiers'      => $tiers,
            'images'     => $images,
        ], 'layouts/admin');
    }

    public function update(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $productId = (int)$id;
        $existing = Product::find($productId);
        if (!$existing) {
            Session::flash('error', 'Product not found.');
            return $this->redirect('/admin/products');
        }

        $name = trim((string)$request->post('name'));
        $newSlug = slugify((string)$request->post('slug') ?: $name);

        // If slug changed, record 301 redirect in url_redirects
        if ($newSlug !== $existing['slug']) {
            Database::query(
                "INSERT INTO url_redirects (old_slug, new_slug, entity_type) VALUES (:old, :new, 'product') ON DUPLICATE KEY UPDATE new_slug = :new",
                ['old' => "product/{$existing['slug']}", 'new' => "product/{$newSlug}"]
            );
        }

        Database::update('products', [
            'name'                => $name,
            'slug'                => $newSlug,
            'sku'                 => strtoupper(trim((string)$request->post('sku'))),
            'oem_number'          => strtoupper(trim((string)$request->post('oem_number'))),
            'cross_references'    => trim((string)$request->post('cross_references')) ?: null,
            'brand_id'            => (int)$request->post('brand_id'),
            'category_id'         => (int)$request->post('category_id'),
            'condition_type'      => $request->post('condition_type', 'Genuine'),
            'warranty'            => trim((string)$request->post('warranty')),
            'short_description'   => trim((string)$request->post('short_description')) ?: null,
            'description'         => $request->post('description', ''),
            'price'               => (float)$request->post('price'),
            'compare_at_price'    => $request->filled('compare_at_price') ? (float)$request->post('compare_at_price') : null,
            'cost_price'          => $request->filled('cost_price') ? (float)$request->post('cost_price') : null,
            'stock_quantity'      => (int)$request->post('stock_quantity', 0),
            'low_stock_threshold' => (int)$request->post('low_stock_threshold', 3),
            'is_featured'         => (int)($request->post('is_featured') ? 1 : 0),
            'is_active'           => (int)($request->post('is_active') ? 1 : 0),
            'weight_kg'           => (float)($request->post('weight_kg', 1.0)),
            'meta_title'          => trim((string)$request->post('meta_title')) ?: null,
            'meta_description'    => trim((string)$request->post('meta_description')) ?: null,
            'updated_at'          => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $productId]);

        // Update Fitments
        Database::delete('product_fitment', 'product_id = :pid', ['pid' => $productId]);
        $vehicleIds = (array)$request->post('vehicle_ids', []);
        foreach ($vehicleIds as $vid) {
            $vidInt = (int)$vid;
            if ($vidInt > 0) {
                Database::insert('product_fitment', [
                    'product_id' => $productId,
                    'vehicle_id' => $vidInt,
                    'notes'      => 'Confirmed OE fitment',
                ]);
            }
        }

        // Update Price Tiers
        Database::delete('product_price_tiers', 'product_id = :pid', ['pid' => $productId]);
        $tierMinQtys = (array)$request->post('tier_min_qty', []);
        $tierPrices = (array)$request->post('tier_price', []);
        for ($i = 0; $i < count($tierMinQtys); $i++) {
            $tQty = (int)($tierMinQtys[$i] ?? 0);
            $tPrice = (float)($tierPrices[$i] ?? 0);
            if ($tQty > 1 && $tPrice > 0) {
                Database::insert('product_price_tiers', [
                    'product_id' => $productId,
                    'min_qty'    => $tQty,
                    'unit_price' => $tPrice,
                ]);
            }
        }

        // Upload additional images if provided
        $files = $_FILES['images'] ?? null;
        if ($files && is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if (!empty($files['name'][$i]) && $files['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    try {
                        $up = ImageHandler::processUpload($singleFile, 'products', true);
                        Database::insert('product_images', [
                            'product_id'    => $productId,
                            'image_url'     => $up['url'],
                            'thumbnail_url' => $up['thumbnail_url'],
                            'is_primary'    => 0,
                            'sort_order'    => 99,
                        ]);
                    } catch (\Throwable $e) {}
                }
            }
        }

        Session::flash('success', "Part '{$name}' updated successfully.");
        return $this->redirect('/admin/products');
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $productId = (int)$id;
        $images = Product::getImages($productId);
        foreach ($images as $img) {
            ImageHandler::delete($img['image_url']);
        }

        Database::delete('products', 'id = :id', ['id' => $productId]);
        Session::flash('success', 'Product and associated images removed.');
        return $this->redirect('/admin/products');
    }

    public function deleteImage(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $imgId = (int)$id;
        $img = Database::selectOne("SELECT * FROM product_images WHERE id = :id LIMIT 1", ['id' => $imgId]);
        if ($img) {
            ImageHandler::delete($img['image_url']);
            Database::delete('product_images', 'id = :id', ['id' => $imgId]);
        }

        Session::flash('success', 'Image removed.');
        return $this->back();
    }

    public function exportCsv(Request $request): void
    {
        $products = Database::select(
            "SELECT p.id, p.name, p.sku, p.oem_number, p.cross_references, b.name as brand, c.name as category,
                    p.condition_type, p.price, p.compare_at_price, p.cost_price, p.stock_quantity, p.weight_kg
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN categories c ON p.category_id = c.id
             ORDER BY p.id ASC"
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=pakautoparts_products_' . date('Y-m-d') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Part Name', 'SKU', 'OEM Number', 'Cross References', 'Brand', 'Category', 'Condition', 'Price', 'Compare Price', 'Cost Price', 'Stock Qty', 'Weight (kg)']);

        foreach ($products as $p) {
            fputcsv($out, [
                $p['id'],
                $p['name'],
                $p['sku'],
                $p['oem_number'],
                $p['cross_references'],
                $p['brand'],
                $p['category'],
                $p['condition_type'],
                $p['price'],
                $p['compare_at_price'],
                $p['cost_price'],
                $p['stock_quantity'],
                $p['weight_kg'],
            ]);
        }

        fclose($out);
        exit;
    }

    public function importCsv(Request $request): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $file = $request->file('csv_file');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please upload a valid CSV file.');
            return $this->back();
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            Session::flash('error', 'Cannot read uploaded CSV file.');
            return $this->back();
        }

        $header = fgetcsv($handle);
        $importedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) continue;

            $name = trim($row[1] ?? '');
            $sku = strtoupper(trim($row[2] ?? ''));
            $oem = strtoupper(trim($row[3] ?? ''));
            $price = (float)($row[8] ?? 0);
            $stock = (int)($row[11] ?? 10);

            if ($name === '' || $sku === '' || $price <= 0) continue;

            $slug = slugify($name);
            $exists = Database::selectOne("SELECT id FROM products WHERE sku = :sku LIMIT 1", ['sku' => $sku]);

            if ($exists) {
                Database::update('products', [
                    'name'           => $name,
                    'price'          => $price,
                    'stock_quantity' => $stock,
                    'updated_at'     => date('Y-m-d H:i:s'),
                ], 'id = :id', ['id' => $exists['id']]);
            } else {
                Database::insert('products', [
                    'name'           => $name,
                    'slug'           => $slug . '-' . time(),
                    'sku'            => $sku,
                    'oem_number'     => $oem,
                    'brand_id'       => 1, // Default Toyota
                    'category_id'    => 1, // Default Engine
                    'price'          => $price,
                    'stock_quantity' => $stock,
                    'condition_type' => 'Genuine',
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
            }
            $importedCount++;
        }

        fclose($handle);
        Session::flash('success', "Bulk import complete. Processed {$importedCount} spare part records.");
        return $this->redirect('/admin/products');
    }
}

