<?php

namespace App\Controllers\Web;

use CodeIgniter\Controller;

class User extends Controller
{
    public function dashboard()
    {
        return view('user/dashboard');
    }

    public function production()
    {
        return view('user/production');
    }

    public function article()
    {
        return view('user/article');
    }

    public function notification()
    {
        return view('user/notification');
    }
}
