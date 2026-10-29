<?php
/**
 * PakAutoParts - Admin Authentication Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Session;

class AuthController extends Controller
{
    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/admin');
        }

        return $this->view('admin/auth/login', [
            'title' => 'Admin Sign In | PakBikeParts',
        ], null); // Render without public storefront layout
    }

    public function login(Request $request): Response
    {
        if (!$this->validateCsrf()) {
            return $this->back();
        }

        $email = trim((string)$request->post('email'));
        $password = (string)$request->post('password');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please provide both email and password.');
            return $this->back();
        }

        if (Auth::attempt($email, $password)) {
            Session::flash('success', 'Welcome back to PakBikeParts Control Panel.');
            return $this->redirect('/admin');
        }

        Session::flash('error', 'Invalid login credentials or account temporarily locked.');
        return $this->back();
    }

    public function logout(Request $request): Response
    {
        if ($this->validateCsrf()) {
            Auth::logout();
            Session::flash('info', 'You have been securely logged out.');
        }

        return $this->redirect('/admin/login');
    }
}

