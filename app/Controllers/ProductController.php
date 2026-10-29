<?php
/**
 * PakAutoParts - Product Details Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\RateLimiter;
use App\Core\Database;
use App\Models\Product;
use App\Models\Vehicle;
use App\Models\Review;

class ProductController extends Controller
{
    public function show(Request $request, string $slug): Response
    {
        $product = Product::findBySlugWithDetails($slug);
        if (!$product) {
            return $this->redirect('/parts', 404);
        }

        $productId = (int)$product['id'];

        // 1. Check Fitment with Active Garage Vehicle
        $garageVehicleId = (int)($_COOKIE['my_garage_id'] ?? 0);
        $activeVehicle = null;
        $fitsGarage = null;

        if ($garageVehicleId > 0) {
            $activeVehicle = Vehicle::find($garageVehicleId);
            if ($activeVehicle) {
                $fitsGarage = Product::fitsVehicle($productId, $garageVehicleId);
            }
        }

        // 2. Fetch Approved Reviews & Rating Summary
        $reviews = Review::forProduct($productId);
        $ratingSummary = Review::getAverageRating($productId);

        // 3. Related Products from same category
        $relatedProducts = Product::searchCatalog([
            'category_id' => $product['category_id'],
        ], 1, 4);

        // Filter out current product from related items
        $relatedItems = array_filter($relatedProducts['data'], fn($item) => (int)$item['id'] !== $productId);

        // 4. Construct JSON-LD Schema
        $jsonLd = [
            '@context'     => 'https://schema.org/',
            '@type'        => 'Product',
            'name'         => $product['name'],
            'image'        => array_column($product['images'], 'image_url') ?: [url('assets/images/placeholder.svg')],
            'description'  => strip_tags((string)$product['short_description']),
            'sku'          => $product['sku'],
            'mpn'          => $product['oem_number'],
            'brand'        => [
                '@type' => 'Brand',
                'name'  => $product['brand_name'] ?? 'Genuine'
            ],
            'offers'       => [
                '@type'         => 'Offer',
                'url'           => url("product/{$product['slug']}"),
                'priceCurrency' => 'PKR',
                'price'         => (float)$product['price'],
                'itemCondition' => $product['condition_type'] === 'Used' ? 'https://schema.org/UsedCondition' : 'https://schema.org/NewCondition',
                'availability'  => (int)$product['stock_quantity'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller'        => [
                    '@type' => 'Organization',
                    'name'  => 'PakAutoParts'
                ]
            ]
        ];

        if ($ratingSummary['count'] > 0) {
            $jsonLd['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string)$ratingSummary['average'],
                'reviewCount' => (string)$ratingSummary['count']
            ];
        }

        return $this->view('product/show', [
            'meta_title'      => $product['meta_title'] ?: "{$product['name']} Price in Pakistan | PakAutoParts",
            'meta_description'=> $product['meta_description'] ?: "Buy {$product['name']} (OEM: {$product['oem_number']}) in Pakistan. 100% Genuine with nationwide Cash on Delivery.",
            'product'         => $product,
            'activeVehicle'   => $activeVehicle,
            'fitsGarage'      => $fitsGarage,
            'reviews'         => $reviews,
            'ratingSummary'   => $ratingSummary,
            'relatedItems'    => $relatedItems,
            'json_ld'         => $jsonLd,
            'breadcrumbs'     => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => $product['category_name'] ?? 'Parts', 'url' => "/category/{$product['category_slug']}"],
                ['title' => $product['name'], 'url' => ''],
            ]
        ]);
    }

    public function storeReview(Request $request): Response
    {
        $ip = $request->ip();
        if (RateLimiter::tooManyAttempts("review_ip_{$ip}", 3, 3600)) {
            Session::flash('error', 'Too many reviews submitted. Please try again later.');
            return $this->back();
        }

        $productId = (int)$request->post('product_id');
        $product = Product::find($productId);
        if (!$product) {
            return $this->redirect('/parts');
        }

        $errors = $this->validate($request->all(), [
            'customer_name' => 'required|min:2|max:60',
            'rating'        => 'required|numeric',
            'comment'       => 'required|min:10|max:1000',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            return $this->back();
        }

        RateLimiter::hit("review_ip_{$ip}", 3600);

        Database::insert('reviews', [
            'product_id'     => $productId,
            'customer_name'  => trim((string)$request->post('customer_name')),
            'customer_email' => trim((string)$request->post('customer_email')),
            'customer_phone' => trim((string)$request->post('customer_phone')),
            'rating'         => min(5, max(1, (int)$request->post('rating'))),
            'comment'        => trim((string)$request->post('comment')),
            'is_approved'    => 1, // Automatically approve initial reviews or moderate via admin
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Thank you! Your verified review has been published.');
        return $this->back();
    }

    public function notifyStock(Request $request): Response
    {
        $productId = (int)$request->post('product_id');
        $phone = trim((string)$request->post('customer_phone'));
        $email = trim((string)$request->post('customer_email'));

        if (empty($phone) && empty($email)) {
            Session::flash('error', 'Please provide either a phone number or email.');
            return $this->back();
        }

        Database::insert('stock_notifications', [
            'product_id'     => $productId,
            'customer_email' => $email ?: null,
            'customer_phone' => $phone,
            'is_notified'    => 0,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Thank you! We will notify you via WhatsApp / SMS as soon as this part arrives in stock.');
        return $this->back();
    }
}

