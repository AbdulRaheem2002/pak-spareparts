<?php
/**
 * PakAutoParts - Admin Part Requests Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;

class PartRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $requests = Database::select("SELECT * FROM part_requests ORDER BY id DESC");

        return $this->view('admin/part_requests/index', [
            'page_title' => 'Part Sourcing Inquiries ("Can\'t Find Your Part?")',
            'requests'   => $requests,
        ], 'layouts/admin');
    }

    public function updateStatus(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $status = $request->post('status', 'New');
        Database::update('part_requests', ['status' => $status], 'id = :id', ['id' => (int)$id]);

        Session::flash('success', "Part request status updated to {$status}.");
        return $this->back();
    }
}

