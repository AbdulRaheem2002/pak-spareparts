<?php
/**
 * PakAutoParts - Catalog & Fitment Navigation Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Vehicle;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'q'             => $request->get('q', ''),
            'category_id'   => $request->get('category_id'),
            'category_slug' => $request->get('category'),
            'brand_id'      => $request->get('brand_id'),
            'condition'     => $request->get('condition'),
            'min_price'     => $request->get('min_price'),
            'max_price'     => $request->get('max_price'),
            'in_stock'      => $request->get('in_stock'),
            'sort'          => $request->get('sort', 'default'),
            'vehicle_id'    => $request->get('vehicle_id') ?: ($_COOKIE['my_garage_id'] ?? null),
        ];

        $page = (int)$request->get('page', 1);
        $catalog = Product::searchCatalog($filters, $page, 24);

        // Fetch filter sidebar data
        $categories = Category::activeWithCounts();
        $brands = Brand::withProductCount();
        $vehicles = Vehicle::all('make ASC, model ASC');

        // Check active garage vehicle
        $activeVehicle = null;
        if (!empty($filters['vehicle_id'])) {
            $activeVehicle = Vehicle::find((int)$filters['vehicle_id']);
            if ($activeVehicle) {
                foreach ($catalog['data'] as &$p) {
                    $p['fits_active_vehicle'] = true;
                }
            }
        }

        // Build breadcrumbs
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Catalog', 'url' => '/parts'],
        ];

        $pageTitle = 'All Auto Spare Parts & Accessories in Pakistan';
        if (!empty($filters['q'])) {
            $pageTitle = "Search: {$filters['q']} Price in Pakistan | PakAutoParts";
            $breadcrumbs[] = ['title' => 'Search: ' . $filters['q'], 'url' => ''];
        }

        return $this->view('catalog/index', [
            'title'        => $pageTitle,
            'catalog'      => $catalog,
            'filters'      => $filters,
            'categories'   => $categories,
            'brands'       => $brands,
            'vehicles'     => $vehicles,
            'activeVehicle'=> $activeVehicle,
            'breadcrumbs'  => $breadcrumbs,
        ]);
    }

    public function category(Request $request, string $slug): Response
    {
        $category = Category::findBy('slug', $slug);
        if (!$category) {
            return $this->redirect('/parts');
        }

        $_GET['category_id'] = $category['id'];
        $response = $this->index($request);

        return $response;
    }

    public function brand(Request $request, string $slug): Response
    {
        $brand = Brand::findBy('slug', $slug);
        if (!$brand) {
            return $this->redirect('/parts');
        }

        $_GET['brand_id'] = $brand['id'];
        return $this->index($request);
    }

    public function vehicle(Request $request, string $make, string $model, string $year): Response
    {
        $yearInt = (int)$year;
        $vehicle = Vehicle::findMatch($make, $model, $yearInt);

        if (!$vehicle) {
            // Fallback to make and model search
            $filters = ['q' => "{$make} {$model}"];
            $catalog = Product::searchCatalog($filters, 1, 24);
        } else {
            $filters = ['vehicle_id' => $vehicle['id']];
            $catalog = Product::searchCatalog($filters, 1, 24);
            foreach ($catalog['data'] as &$p) {
                $p['fits_active_vehicle'] = true;
            }
        }

        $pageTitle = "{$make} {$model} ({$year}) Spare Parts Price in Pakistan | 100% Guaranteed Fitment";
        $metaDesc = "Buy genuine & OEM {$make} {$model} {$year} replacement spare parts online in Pakistan. Oil filters, brake pads, shocks, and plugs with Cash on Delivery.";

        return $this->view('catalog/vehicle', [
            'title'           => $pageTitle,
            'meta_title'      => $pageTitle,
            'meta_description'=> $metaDesc,
            'make'            => $make,
            'model'           => $model,
            'year'            => $year,
            'vehicle'         => $vehicle,
            'catalog'         => $catalog,
            'breadcrumbs'     => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Parts by Vehicle', 'url' => '/parts'],
                ['title' => "{$make} {$model} {$year}", 'url' => ''],
            ],
            'json_ld'         => [
                '@context' => 'https://schema.org',
                '@type'    => 'CollectionPage',
                'name'     => $pageTitle,
                'description' => $metaDesc,
                'url'      => url("parts/{$make}/{$model}/{$year}")
            ]
        ]);
    }

    public function suggest(Request $request): Response
    {
        $q = trim((string)$request->get('q', ''));
        if (strlen($q) < 2) {
            return $this->json(['results' => []]);
        }

        $results = Product::searchCatalog(['q' => $q], 1, 6);

        return $this->json([
            'query'   => $q,
            'results' => $results['data'] ?? [],
        ]);
    }
}

