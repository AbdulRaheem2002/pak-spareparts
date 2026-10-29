<?php
/**
 * PakAutoParts - Admin Blog & Article Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\ImageHandler;
use App\Models\BlogPost;
use App\Helpers\Formatter;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = Database::select(
            "SELECT bp.*, bc.name as category_name
             FROM blog_posts bp
             LEFT JOIN blog_categories bc ON bp.category_id = bc.id
             ORDER BY bp.id DESC"
        );

        return $this->view('admin/blog/index', [
            'page_title' => 'Blog Posts & Maintenance Guides',
            'posts' => $posts,
        ], 'layouts/admin');
    }

    public function create(Request $request): Response
    {
        $categories = Database::select("SELECT * FROM blog_categories ORDER BY name ASC");

        return $this->view('admin/blog/form', [
            'page_title' => 'Write New Maintenance Guide / Blog Post',
            'post' => null,
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function store(Request $request): Response
    {
        if (!$this->validateCsrf())
            return $this->back();

        $errors = $this->validate($request->all(), [
            'title' => 'required|min:5|max:220',
            'content' => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            return $this->back();
        }

        $title = trim((string) $request->post('title'));
        $slug = slugify((string) $request->post('slug') ?: $title);
        $content = $request->post('content', '');
        $readingTime = Formatter::readingTime($content);

        // Featured Image Upload
        $featuredImgUrl = null;
        $file = $request->file('featured_image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            try {
                $up = ImageHandler::processUpload($file, 'blog', true);
                $featuredImgUrl = $up['url'];
            } catch (\Throwable $e) {
            }
        }

        Database::insert('blog_posts', [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => trim((string) $request->post('excerpt')) ?: Formatter::truncate($content, 180),
            'content' => $content,
            'featured_image' => $featuredImgUrl,
            'category_id' => $request->filled('category_id') ? (int) $request->post('category_id') : null,
            'tags' => trim((string) $request->post('tags')) ?: null,
            'reading_time_min' => $readingTime,
            'focus_keyword' => trim((string) $request->post('focus_keyword')) ?: null,
            'meta_title' => trim((string) $request->post('meta_title')) ?: $title,
            'meta_description' => trim((string) $request->post('meta_description')) ?: Formatter::truncate($content, 160),
            'is_published' => (int) ($request->post('is_published') ? 1 : 0),
            'published_at' => $request->post('is_published') ? date('Y-m-d H:i:s') : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', "Article '{$title}' published.");
        return $this->redirect('/admin/blog');
    }

    public function edit(Request $request, string $id): Response
    {
        $post = Database::selectOne("SELECT * FROM blog_posts WHERE id = :id LIMIT 1", ['id' => (int) $id]);
        if (!$post) {
            Session::flash('error', 'Article not found.');
            return $this->redirect('/admin/blog');
        }

        $categories = Database::select("SELECT * FROM blog_categories ORDER BY name ASC");

        return $this->view('admin/blog/form', [
            'page_title' => "Edit Article: {$post['title']}",
            'post' => $post,
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function update(Request $request, string $id): Response
    {
        if (!$this->validateCsrf())
            return $this->back();

        $postId = (int) $id;
        $title = trim((string) $request->post('title'));
        $slug = slugify((string) $request->post('slug') ?: $title);
        $content = $request->post('content', '');
        $readingTime = Formatter::readingTime($content);

        $updateData = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => trim((string) $request->post('excerpt')),
            'content' => $content,
            'category_id' => $request->filled('category_id') ? (int) $request->post('category_id') : null,
            'tags' => trim((string) $request->post('tags')) ?: null,
            'reading_time_min' => $readingTime,
            'focus_keyword' => trim((string) $request->post('focus_keyword')) ?: null,
            'meta_title' => trim((string) $request->post('meta_title')) ?: $title,
            'meta_description' => trim((string) $request->post('meta_description')),
            'is_published' => (int) ($request->post('is_published') ? 1 : 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Handle Image Replacement
        $file = $request->file('featured_image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            try {
                $up = ImageHandler::processUpload($file, 'blog', true);
                $updateData['featured_image'] = $up['url'];
            } catch (\Throwable $e) {
            }
        }

        Database::update('blog_posts', $updateData, 'id = :id', ['id' => $postId]);

        Session::flash('success', "Article '{$title}' updated.");
        return $this->redirect('/admin/blog');
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$this->validateCsrf())
            return $this->back();

        $postId = (int) $id;
        $post = Database::selectOne("SELECT featured_image FROM blog_posts WHERE id = :id LIMIT 1", ['id' => $postId]);
        if ($post && !empty($post['featured_image'])) {
            ImageHandler::delete($post['featured_image']);
        }

        Database::delete('blog_posts', 'id = :id', ['id' => $postId]);
        Session::flash('success', 'Article deleted.');
        return $this->redirect('/admin/blog');
    }
}

