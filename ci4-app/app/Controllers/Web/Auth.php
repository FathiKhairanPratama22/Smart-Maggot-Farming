<?php

namespace App\Controllers\Web;

use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        // Redirect jika sudah login
        if (session()->get('isLoggedIn')) {
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/user/dashboard');
        }
        return view('auth/login');
    }
}
