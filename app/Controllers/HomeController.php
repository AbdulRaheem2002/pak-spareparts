<?php
/**
 * PakAutoParts - Home Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\Product;
use App\Models\BlogPost;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        // 1. Featured Categories
        $categories = Category::activeWithCounts();

        // 2. Popular Brands
        $popularBrands = Brand::popular();

        // 3. Featured Parts
        $featuredParts = Product::searchCatalog(['is_featured' => 1], 1, 8);

        // 4. Best Sellers
        $bestSellers = Product::searchCatalog([], 1, 8);

        // 5. Popular Pakistan Vehicles for Quick Filter
        $popularVehicles = Vehicle::all('make ASC, model ASC', 12);
        $makes = Vehicle::getMakes();

        // 6. Latest Blog Guides
        $latestPosts = BlogPost::published(1, 3);

        // 7. Check if user has a garage vehicle set in cookie
        $garageVehicleId = (int)($_COOKIE['my_garage_id'] ?? 0);
        $activeVehicle = null;
        if ($garageVehicleId > 0) {
            $activeVehicle = Vehicle::find($garageVehicleId);
            if ($activeVehicle) {
                // Annotate featured parts with fitment
                foreach ($featuredParts['data'] as &$p) {
                    $p['fits_active_vehicle'] = Product::fitsVehicle((int)$p['id'], $garageVehicleId);
                }
                foreach ($bestSellers['data'] as &$p) {
                    $p['fits_active_vehicle'] = Product::fitsVehicle((int)$p['id'], $garageVehicleId);
                }
            }
        }

        return $this->view('home/index', [
            'meta_title'       => 'PakAutoParts | 100% Genuine & OEM Auto Spare Parts Pakistan',
            'meta_description' => 'Pakistan\'s premier online auto parts store. Genuine & OEM parts for Toyota, Honda, Suzuki, KIA & motorbikes. Fast nationwide cash on delivery.',
            'categories'       => $categories,
            'popularBrands'    => $popularBrands,
            'featuredParts'    => $featuredParts['data'] ?? [],
            'bestSellers'      => $bestSellers['data'] ?? [],
            'popularVehicles'  => $popularVehicles,
            'makes'            => $makes,
            'latestPosts'      => $latestPosts['data'] ?? [],
            'activeVehicle'    => $activeVehicle,
            'json_ld'          => [
                '@context' => 'https://schema.org',
                '@type'    => 'AutoPartsStore',
                'name'     => 'PakAutoParts',
                'image'    => url('assets/images/logo.svg'),
                'telephone'=> setting('contact_phone', '0300-1234567'),
                'priceRange'=> 'PKR',
                'address'  => [
                    '@type'          => 'PostalAddress',
                    'streetAddress'  => 'Montgomery Road Auto Market',
                    'addressLocality'=> 'Lahore',
                    'addressRegion'  => 'Punjab',
                    'addressCountry' => 'PK'
                ]
            ]
        ]);
    }
}

