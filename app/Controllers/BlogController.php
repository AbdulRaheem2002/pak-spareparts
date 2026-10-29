<?php
/**
 * PakAutoParts - Public Blog & Maintenance Guides Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\BlogPost;
use App\Models\Product;
use App\Core\Database;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $page = (int)$request->get('page', 1);
        $posts = BlogPost::published($page, 9);
        $categories = Database::select("SELECT * FROM blog_categories ORDER BY name ASC");

        return $this->view('blog/index', [
            'meta_title'       => 'Auto Maintenance Guides, Car Care & Spare Part Buying Advice | PakAutoParts',
            'meta_description' => 'Read expert auto maintenance guides, tips on identifying counterfeit Toyota and Honda parts in Pakistan, engine oil viscosities, and DIY repair tutorials.',
            'posts'            => $posts,
            'categories'       => $categories,
            'breadcrumbs'      => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Maintenance Guides & Blog', 'url' => ''],
            ]
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $post = BlogPost::findBySlug($slug);
        if (!$post) {
            return $this->redirect('/blog', 404);
        }

        // Increment view count
        BlogPost::incrementViews((int)$post['id']);

        // Generate Table of Contents automatically from <h2> and <h3> tags
        $content = $post['content'];
        $toc = [];
        $pattern = '/<(h[23])>(.*?)<\/\1>/i';
        $modifiedContent = preg_replace_callback($pattern, function ($matches) use (&$toc) {
            $tag = strtolower($matches[1]);
            $title = strip_tags($matches[2]);
            $anchor = slugify($title);
            $toc[] = [
                'tag'    => $tag,
                'title'  => $title,
                'anchor' => $anchor,
            ];
            return "<{$tag} id=\"{$anchor}\">{$matches[2]}</{$tag}>";
        }, $content);

        // Fetch Related Spare Parts based on category or tags
        $relatedProducts = Product::searchCatalog([], 1, 4);

        // Recent guides
        $recentPosts = Database::select(
            "SELECT id, title, slug, published_at, reading_time_min, featured_image
             FROM blog_posts
             WHERE is_published = 1 AND id != :id
             ORDER BY id DESC LIMIT 4",
            ['id' => (int)$post['id']]
        );

        $jsonLd = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $post['title'],
            'image'         => !empty($post['featured_image']) ? [url(ltrim($post['featured_image'], '/'))] : [url('assets/images/logo.svg')],
            'datePublished' => date('c', strtotime($post['published_at'] ?? $post['created_at'])),
            'dateModified'  => date('c', strtotime($post['updated_at'] ?? $post['created_at'])),
            'author'        => [
                '@type' => 'Organization',
                'name'  => 'PakAutoParts Editorial Team'
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => 'PakAutoParts',
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => url('assets/images/logo.svg')
                ]
            ],
            'description'   => $post['excerpt']
        ];

        return $this->view('blog/show', [
            'meta_title'      => $post['meta_title'] ?: $post['title'],
            'meta_description'=> $post['meta_description'] ?: $post['excerpt'],
            'canonical_url'   => url("blog/{$post['slug']}"),
            'post'            => $post,
            'content'         => $modifiedContent,
            'toc'             => $toc,
            'relatedProducts' => $relatedProducts['data'] ?? [],
            'recentPosts'     => $recentPosts,
            'json_ld'         => $jsonLd,
            'breadcrumbs'     => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Guides & Blog', 'url' => '/blog'],
                ['title' => $post['title'], 'url' => ''],
            ]
        ]);
    }
}

