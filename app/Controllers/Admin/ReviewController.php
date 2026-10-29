<?php
/**
 * PakAutoParts - Admin Reviews Moderation Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $reviews = Database::select(
            "SELECT r.*, p.name as product_name, p.slug as product_slug
             FROM reviews r
             JOIN products p ON r.product_id = p.id
             ORDER BY r.id DESC"
        );

        return $this->view('admin/reviews/index', [
            'page_title' => 'Customer Reviews Moderation',
            'reviews'    => $reviews,
        ], 'layouts/admin');
    }

    public function toggleApproval(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $rev = Database::selectOne("SELECT id, is_approved FROM reviews WHERE id = :id LIMIT 1", ['id' => (int)$id]);
        if ($rev) {
            $newStatus = (int)$rev['is_approved'] === 1 ? 0 : 1;
            Database::update('reviews', ['is_approved' => $newStatus], 'id = :id', ['id' => $rev['id']]);
            Session::flash('success', 'Review status updated.');
        }

        return $this->back();
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        Database::delete('reviews', 'id = :id', ['id' => (int)$id]);
        Session::flash('success', 'Review removed.');
        return $this->back();
    }
}

