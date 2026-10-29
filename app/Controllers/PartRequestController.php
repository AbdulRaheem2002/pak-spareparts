<?php
/**
 * PakAutoParts - Part Request Controller ("Can't find your part?")
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\RateLimiter;
use App\Core\Database;
use App\Core\ImageHandler;

class PartRequestController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('part_request/index', [
            'title'       => 'Can\'t Find Your Part? Sourcing Request | PakAutoParts',
            'partName'    => $request->get('part_name', ''),
            'vehicleInfo' => $request->get('vehicle_info', ''),
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Part Sourcing Request', 'url' => ''],
            ]
        ]);
    }

    public function store(Request $request): Response
    {
        $ip = $request->ip();
        if (RateLimiter::tooManyAttempts("part_req_{$ip}", 5, 600)) {
            Session::flash('error', 'Too many requests. Please wait a few minutes.');
            return $this->back();
        }

        $errors = $this->validate($request->all(), [
            'name'         => 'required|min:3|max:80',
            'phone'        => 'required|min:10|max:20',
            'vehicle_info' => 'required|min:3|max:150',
            'part_name'    => 'required|min:3|max:150',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            return $this->back();
        }

        // Handle optional sample photo upload
        $photoUrl = null;
        $file = $request->file('part_photo');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            try {
                $upload = ImageHandler::processUpload($file, 'products', true);
                $photoUrl = $upload['url'];
            } catch (\Throwable $e) {
                // If non-critical image fails, continue
            }
        }

        $phone = normalize_pak_phone((string)$request->post('phone'));
        $id = Database::insert('part_requests', [
            'name'         => trim((string)$request->post('name')),
            'phone'        => $phone,
            'email'        => trim((string)$request->post('email')) ?: null,
            'vehicle_info' => trim((string)$request->post('vehicle_info')),
            'part_name'    => trim((string)$request->post('part_name')),
            'part_number'  => trim((string)$request->post('part_number')) ?: null,
            'description'  => trim((string)$request->post('description')) ?: null,
            'photo'        => $photoUrl,
            'status'       => 'New',
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        RateLimiter::hit("part_req_{$ip}", 600);

        // Build WhatsApp notification link for customer
        $ownerWa = setting('contact_whatsapp', '923001234567');
        $msg = "⚡ *PART SOURCING REQUEST #PR-{$id}*\n"
             . "👤 *Name:* " . $request->post('name') . "\n"
             . "📞 *Phone:* {$phone}\n"
             . "🚗 *Vehicle:* " . $request->post('vehicle_info') . "\n"
             . "🔧 *Part Required:* " . $request->post('part_name') . "\n"
             . ($request->post('part_number') ? "🔢 *OEM/Part #:* " . $request->post('part_number') . "\n" : "")
             . ($request->post('description') ? "📝 *Details:* " . $request->post('description') . "\n" : "")
             . "Please check availability and price quote.";

        $waUrl = "https://wa.me/{$ownerWa}?text=" . urlencode($msg);

        Session::flash('success', "Your part inquiry #PR-{$id} has been submitted! Our procurement team will contact you shortly.");

        return $this->redirect($waUrl);
    }
}

