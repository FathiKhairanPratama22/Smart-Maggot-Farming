<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
{
    // 1. JIKA BELUM LOGIN & SEDANG TIDAK BERADA DI HALAMAN LOGIN -> LEMPAR KE FORM LOGIN
    if (!session()->get('isLoggedIn') && !url_is('login') && !url_is('api/auth/*')) {
        return redirect()->to('/login');
    }

    // 2. JIKA USER SUDAH LOGIN TAPI MENCOBA BUKA FORM LOGIN / HALAMAN DEPAN LAGI:
    // Arahkan otomatis ke ruangan masing-masing sesuai jabatan!
    if (session()->get('isLoggedIn') && (url_is('login') || url_is('/'))) {
        $role = session()->get('role');
        if ($role === 'admin') {
            return redirect()->to('/admin/dashboard');
        } else {
            return redirect()->to('/user/dashboard');
        }
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
