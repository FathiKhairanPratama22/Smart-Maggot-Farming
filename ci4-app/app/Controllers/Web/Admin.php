<?php

namespace App\Controllers\Web;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function dashboard()
    {
        return view('admin/dashboard');
    }

    public function sensor()
    {
        return view('admin/sensor');
    }

    public function production()
    {
        return view('admin/production');
    }

    public function article()
    {
        return view('admin/article');
    }

    public function notification()
    {
        return view('admin/notification');
    }

    public function users()
    {
        return view('admin/user_management');
    }

    public function report()
    {
        return view('admin/report');
    }
}
