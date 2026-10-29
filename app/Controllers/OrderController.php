<?php
/**
 * PakAutoParts - Order Tracking Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\RateLimiter;
use App\Core\Session;
use App\Models\Order;

class OrderController extends Controller
{
    public function track(Request $request): Response
    {
        return $this->view('order/track', [
            'title'       => 'Track Your Order | PakAutoParts',
            'order'       => null,
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Order Tracking', 'url' => ''],
            ]
        ]);
    }

    public function lookup(Request $request): Response
    {
        $ip = $request->ip();
        if (RateLimiter::tooManyAttempts("track_ip_{$ip}", 15, 60)) {
            Session::flash('error', 'Too many tracking attempts. Please wait a minute.');
            return $this->back();
        }

        RateLimiter::hit("track_ip_{$ip}", 60);

        $orderNumber = trim((string)$request->post('order_number', ''));
        $phone = trim((string)$request->post('phone', ''));

        if (empty($orderNumber) || empty($phone)) {
            Session::flash('error', 'Please enter both Order ID and Phone Number.');
            return $this->back();
        }

        $order = Order::findByNumberAndPhone($orderNumber, $phone);

        if (!$order) {
            Session::flash('error', 'No order found matching this Order Number and Phone Number.');
            return $this->view('order/track', [
                'title'        => 'Track Your Order | PakAutoParts',
                'order'        => null,
                'searched_num' => $orderNumber,
                'searched_phone'=> $phone,
                'breadcrumbs'  => [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Order Tracking', 'url' => ''],
                ]
            ]);
        }

        return $this->view('order/track', [
            'title'       => "Order {$orderNumber} Status | PakAutoParts",
            'order'       => $order,
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Order Tracking', 'url' => '/order/track'],
                ['title' => $orderNumber, 'url' => ''],
            ]
        ]);
    }
}

