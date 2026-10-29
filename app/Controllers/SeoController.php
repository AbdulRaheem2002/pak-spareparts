<?php
/**
 * PakAutoParts - Technical SEO Engine (Sitemap & Robots)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class SeoController extends Controller
{
    public function sitemap(Request $request): Response
    {
        $products = Database::select("SELECT slug, updated_at, created_at FROM products WHERE is_active = 1");
        $categories = Database::select("SELECT slug, created_at FROM categories WHERE is_active = 1");
        $brands = Database::select("SELECT slug, created_at FROM brands");
        $vehicles = Database::select("SELECT make, model, year_start FROM vehicles");
        $posts = Database::select("SELECT slug, updated_at, published_at FROM blog_posts WHERE is_published = 1");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static routes
        $staticUrls = [
            ['loc' => url(), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => url('parts'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => url('blog'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('order/track'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => url('request-part'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        foreach ($staticUrls as $s) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($s['loc']) . "</loc>\n";
            $xml .= "    <changefreq>{$s['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$s['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        // Products
        foreach ($products as $p) {
            $date = date('Y-m-d', strtotime($p['updated_at'] ?: $p['created_at']));
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars(url("product/{$p['slug']}")) . "</loc>\n";
            $xml .= "    <lastmod>{$date}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.9</priority>\n";
            $xml .= "  </url>\n";
        }

        // Categories
        foreach ($categories as $c) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars(url("category/{$c['slug']}")) . "</loc>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // Brands
        foreach ($brands as $b) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars(url("brand/{$b['slug']}")) . "</loc>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        // Vehicle Fitment Pages
        foreach ($vehicles as $v) {
            $loc = url("parts/" . urlencode($v['make']) . "/" . urlencode($v['model']) . "/" . $v['year_start']);
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // Blog Posts
        foreach ($posts as $bp) {
            $date = date('Y-m-d', strtotime($bp['updated_at'] ?: $bp['published_at']));
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars(url("blog/{$bp['slug']}")) . "</loc>\n";
            $xml .= "    <lastmod>{$date}</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return new Response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function robots(Request $request): Response
    {
        $sitemapUrl = url('sitemap.xml');
        $txt = "User-agent: *\n";
        $txt .= "Disallow: /admin/\n";
        $txt .= "Disallow: /cart\n";
        $txt .= "Disallow: /checkout\n";
        $txt .= "Disallow: /order/success/\n";
        $txt .= "Disallow: /*?*q=\n";
        $txt .= "Disallow: /*?*page=\n";
        $txt .= "Allow: /\n\n";
        $txt .= "Sitemap: {$sitemapUrl}\n";

        return new Response($txt, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}

