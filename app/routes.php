<?php
/**
 * PakAutoParts - Web Application & Admin Routes
 */

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\CatalogController;
use App\Controllers\ProductController;
use App\Controllers\GarageController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Controllers\PartRequestController;
use App\Controllers\BlogController;
use App\Controllers\SeoController;

// ==============================================================================
// PUBLIC STOREFRONT ROUTES
// ==============================================================================

Router::get('/', [HomeController::class, 'index'], [], 'home');

// Catalog & Fitment Navigation
Router::get('/parts', [CatalogController::class, 'index'], [], 'catalog');
Router::get('/category/{slug}', [CatalogController::class, 'category'], [], 'catalog.category');
Router::get('/brand/{slug}', [CatalogController::class, 'brand'], [], 'catalog.brand');
Router::get('/parts/{make}/{model}/{year}', [CatalogController::class, 'vehicle'], [], 'catalog.vehicle');
Router::get('/search/suggest', [CatalogController::class, 'suggest'], [], 'search.suggest');

// Product Detail & Reviews
Router::get('/product/{slug}', [ProductController::class, 'show'], [], 'product.show');
Router::post('/reviews', [ProductController::class, 'storeReview'], ['csrf'], 'reviews.store');
Router::post('/stock-notification', [ProductController::class, 'notifyStock'], ['csrf'], 'stock.notify');

// My Garage Vehicle Persistence
Router::get('/garage/options', [GarageController::class, 'options'], [], 'garage.options');
Router::post('/garage/select', [GarageController::class, 'select'], [], 'garage.select');
Router::post('/garage/clear', [GarageController::class, 'clear'], [], 'garage.clear');

// Cart & Calculations
Router::get('/cart', [CartController::class, 'index'], [], 'cart.index');
Router::post('/cart/sync', [CartController::class, 'sync'], [], 'cart.sync');
Router::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'], [], 'cart.coupon');

// Checkout & Order Placement
Router::get('/checkout', [CheckoutController::class, 'index'], [], 'checkout.index');
Router::post('/checkout/process', [CheckoutController::class, 'process'], ['csrf'], 'checkout.process');
Router::get('/order/success/{order_number}', [CheckoutController::class, 'success'], [], 'checkout.success');

// Order Tracking (Rate limited)
Router::get('/order/track', [OrderController::class, 'track'], [], 'order.track');
Router::post('/order/track', [OrderController::class, 'lookup'], [], 'order.lookup');

// Can't Find Your Part? Request Form
Router::get('/request-part', [PartRequestController::class, 'index'], [], 'part.request.index');
Router::post('/request-part', [PartRequestController::class, 'store'], ['csrf'], 'part.request.store');

// Blog & Articles
Router::get('/blog', [BlogController::class, 'index'], [], 'blog.index');
Router::get('/blog/{slug}', [BlogController::class, 'show'], [], 'blog.show');

// Technical SEO
Router::get('/sitemap.xml', [SeoController::class, 'sitemap'], [], 'seo.sitemap');
Router::get('/robots.txt', [SeoController::class, 'robots'], [], 'seo.robots');

// ==============================================================================
// ADMIN PANEL ROUTES (/admin)
// ==============================================================================

// Admin Authentication
Router::get('/admin/login', [App\Controllers\Admin\AuthController::class, 'showLogin'], [], 'admin.login');
Router::post('/admin/login', [App\Controllers\Admin\AuthController::class, 'login'], ['csrf'], 'admin.login.submit');
Router::post('/admin/logout', [App\Controllers\Admin\AuthController::class, 'logout'], ['auth', 'csrf'], 'admin.logout');

// Dashboard & Analytics
Router::get('/admin', [App\Controllers\Admin\DashboardController::class, 'index'], ['auth'], 'admin.dashboard');
Router::get('/admin/dashboard', [App\Controllers\Admin\DashboardController::class, 'index'], ['auth'], 'admin.dashboard.alias');

// Products Management
Router::get('/admin/products', [App\Controllers\Admin\ProductController::class, 'index'], ['auth'], 'admin.products');
Router::get('/admin/products/create', [App\Controllers\Admin\ProductController::class, 'create'], ['auth'], 'admin.products.create');
Router::post('/admin/products/store', [App\Controllers\Admin\ProductController::class, 'store'], ['auth', 'csrf'], 'admin.products.store');
Router::get('/admin/products/edit/{id}', [App\Controllers\Admin\ProductController::class, 'edit'], ['auth'], 'admin.products.edit');
Router::post('/admin/products/update/{id}', [App\Controllers\Admin\ProductController::class, 'update'], ['auth', 'csrf'], 'admin.products.update');
Router::post('/admin/products/delete/{id}', [App\Controllers\Admin\ProductController::class, 'delete'], ['auth', 'csrf'], 'admin.products.delete');
Router::post('/admin/products/upload-image', [App\Controllers\Admin\ProductController::class, 'uploadImage'], ['auth', 'csrf'], 'admin.products.upload_image');
Router::post('/admin/products/delete-image/{id}', [App\Controllers\Admin\ProductController::class, 'deleteImage'], ['auth', 'csrf'], 'admin.products.delete_image');
Router::get('/admin/products/export', [App\Controllers\Admin\ProductController::class, 'exportCsv'], ['auth'], 'admin.products.export');
Router::post('/admin/products/import', [App\Controllers\Admin\ProductController::class, 'importCsv'], ['auth', 'csrf'], 'admin.products.import');

// Orders Management & Printable Invoices
Router::get('/admin/orders', [App\Controllers\Admin\OrderController::class, 'index'], ['auth'], 'admin.orders');
Router::get('/admin/orders/show/{id}', [App\Controllers\Admin\OrderController::class, 'show'], ['auth'], 'admin.orders.show');
Router::post('/admin/orders/status/{id}', [App\Controllers\Admin\OrderController::class, 'updateStatus'], ['auth', 'csrf'], 'admin.orders.status');
Router::get('/admin/orders/invoice/{id}', [App\Controllers\Admin\OrderController::class, 'invoiceA4'], ['auth'], 'admin.orders.invoice');
Router::get('/admin/orders/thermal/{id}', [App\Controllers\Admin\OrderController::class, 'invoiceThermal'], ['auth'], 'admin.orders.thermal');
Router::get('/admin/orders/packing-slip/{id}', [App\Controllers\Admin\OrderController::class, 'packingSlip'], ['auth'], 'admin.orders.packing_slip');
Router::get('/admin/orders/export', [App\Controllers\Admin\OrderController::class, 'exportCsv'], ['auth'], 'admin.orders.export');

// Categories & Brands
Router::get('/admin/categories', [App\Controllers\Admin\CategoryController::class, 'index'], ['auth'], 'admin.categories');
Router::post('/admin/categories/save', [App\Controllers\Admin\CategoryController::class, 'save'], ['auth', 'csrf'], 'admin.categories.save');
Router::post('/admin/categories/delete/{id}', [App\Controllers\Admin\CategoryController::class, 'delete'], ['auth', 'csrf'], 'admin.categories.delete');

Router::get('/admin/brands', [App\Controllers\Admin\BrandController::class, 'index'], ['auth'], 'admin.brands');
Router::post('/admin/brands/save', [App\Controllers\Admin\BrandController::class, 'save'], ['auth', 'csrf'], 'admin.brands.save');
Router::post('/admin/brands/delete/{id}', [App\Controllers\Admin\BrandController::class, 'delete'], ['auth', 'csrf'], 'admin.brands.delete');

// Vehicles Compatibility Manager
Router::get('/admin/vehicles', [App\Controllers\Admin\VehicleController::class, 'index'], ['auth'], 'admin.vehicles');
Router::post('/admin/vehicles/save', [App\Controllers\Admin\VehicleController::class, 'save'], ['auth', 'csrf'], 'admin.vehicles.save');
Router::post('/admin/vehicles/delete/{id}', [App\Controllers\Admin\VehicleController::class, 'delete'], ['auth', 'csrf'], 'admin.vehicles.delete');

// Coupons & Discounts
Router::get('/admin/coupons', [App\Controllers\Admin\CouponController::class, 'index'], ['auth'], 'admin.coupons');
Router::post('/admin/coupons/save', [App\Controllers\Admin\CouponController::class, 'save'], ['auth', 'csrf'], 'admin.coupons.save');
Router::post('/admin/coupons/delete/{id}', [App\Controllers\Admin\CouponController::class, 'delete'], ['auth', 'csrf'], 'admin.coupons.delete');

// Customers & Reviews Moderation
Router::get('/admin/customers', [App\Controllers\Admin\CustomerController::class, 'index'], ['auth'], 'admin.customers');
Router::get('/admin/customers/export', [App\Controllers\Admin\CustomerController::class, 'exportCsv'], ['auth'], 'admin.customers.export');

Router::get('/admin/reviews', [App\Controllers\Admin\ReviewController::class, 'index'], ['auth'], 'admin.reviews');
Router::post('/admin/reviews/toggle/{id}', [App\Controllers\Admin\ReviewController::class, 'toggleApproval'], ['auth', 'csrf'], 'admin.reviews.toggle');
Router::post('/admin/reviews/delete/{id}', [App\Controllers\Admin\ReviewController::class, 'delete'], ['auth', 'csrf'], 'admin.reviews.delete');

// Part Requests
Router::get('/admin/part-requests', [App\Controllers\Admin\PartRequestController::class, 'index'], ['auth'], 'admin.part_requests');
Router::post('/admin/part-requests/status/{id}', [App\Controllers\Admin\PartRequestController::class, 'updateStatus'], ['auth', 'csrf'], 'admin.part_requests.status');

// Blog Manager (Quill Editor)
Router::get('/admin/blog', [App\Controllers\Admin\BlogController::class, 'index'], ['auth'], 'admin.blog');
Router::get('/admin/blog/create', [App\Controllers\Admin\BlogController::class, 'create'], ['auth'], 'admin.blog.create');
Router::post('/admin/blog/store', [App\Controllers\Admin\BlogController::class, 'store'], ['auth', 'csrf'], 'admin.blog.store');
Router::get('/admin/blog/edit/{id}', [App\Controllers\Admin\BlogController::class, 'edit'], ['auth'], 'admin.blog.edit');
Router::post('/admin/blog/update/{id}', [App\Controllers\Admin\BlogController::class, 'update'], ['auth', 'csrf'], 'admin.blog.update');
Router::post('/admin/blog/delete/{id}', [App\Controllers\Admin\BlogController::class, 'delete'], ['auth', 'csrf'], 'admin.blog.delete');

// Reports & Low Stock
Router::get('/admin/reports', [App\Controllers\Admin\ReportController::class, 'index'], ['auth'], 'admin.reports');

// System Settings & Database Backup
Router::get('/admin/settings', [App\Controllers\Admin\SettingController::class, 'index'], ['auth'], 'admin.settings');
Router::post('/admin/settings/save', [App\Controllers\Admin\SettingController::class, 'save'], ['auth', 'csrf'], 'admin.settings.save');
Router::get('/admin/backup-db', [App\Controllers\Admin\SettingController::class, 'backupDb'], ['auth', 'role:owner'], 'admin.backup_db');

