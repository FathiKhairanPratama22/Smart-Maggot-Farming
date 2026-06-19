<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. KARTU BEBAS ANTI-NYANGKUT
        if (url_is('login') || url_is('logout') || url_is('unauthorized') || url_is('api/auth/*')) {
            return;
        }

        // 2. CEK APAKAH SUDAH LOGIN
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // 3. JIKA RUTE TIDAK MEMINTA ROLE, BIARKAN MASUK
        if (empty($arguments)) {
            return;
        }

        // 4. INTEROGASI ROLE (Hancurkan huruf kapital & spasi!)
        $roleRaw = session()->get('role'); 
        $roleClean = strtolower(trim($roleRaw)); // Paksa jadi huruf kecil semua
        $allowedArgs = array_map('strtolower', $arguments); // Syarat route juga dikecilkan

        // 5. CEK APAKAH ROLE USER ADA DI DAFTAR IZIN ROUTE
        if (!in_array($roleClean, $allowedArgs)) {
            // TENDANG PENYUSUP KE HALAMAN UNAUTHORIZED
            return redirect()->to('/unauthorized'); 
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}