<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// --- WEB ROUTES ---
$routes->get('login', 'Web\Auth::login'); // Kita pakai satu rute login saja agar tidak tabrakan
$routes->get('unauthorized', function () {
    return "Akses ditolak!";
    
});

// --- AUTH API ---
$routes->post('api/auth/login', 'Auth::login');
$routes->get('api/auth/logout', 'Auth::logout'); // Pastikan bisa diakses lewat URL browser
$routes->post('api/auth/logout', 'Auth::logout');
$routes->get('logout', 'Auth::logout');
$routes->get('auth', 'Auth::logout');

// --- WEB ADMIN ROUTES ---
$routes->group('admin', ['filter' => ['auth', 'role:admin']], static function ($routes) {
    $routes->get('dashboard', 'Web\Admin::dashboard');
    $routes->get('sensor', 'Web\Admin::sensor');
    $routes->get('production', 'Web\Admin::production');
    $routes->get('article', 'Web\Admin::article');
    $routes->get('notification', 'Web\Admin::notification');
    $routes->get('users', 'Web\Admin::users');
    $routes->get('report', 'Web\Admin::report');
});

// --- WEB USER ROUTES ---
$routes->group('user', ['filter' => ['auth', 'role:guru,siswa']], static function ($routes) {
    $routes->get('dashboard', 'Web\User::dashboard');
    $routes->get('production', 'Web\User::production');
    $routes->get('article', 'Web\User::article');
    $routes->get('notification', 'Web\User::notification');
});

// --- API SENSOR (Dipanggil dari ESP32) ---
$routes->post('api/sensor', 'Api\Sensor::create');

// --- ADMIN API ROUTES ---
// (TYPO AI DIPERBAIKI DI SINI: filter dijadikan format array)
$routes->group('api/admin', ['filter' => ['auth', 'role:admin']], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('sensor', 'Admin\Sensor::index');
    $routes->delete('sensor/(:num)', 'Admin\Sensor::delete/$1');
    $routes->get('production', 'Admin\Production::index');
    $routes->post('production', 'Admin\Production::create');
    $routes->put('production/(:num)', 'Admin\Production::update/$1');
    $routes->delete('production/(:num)', 'Admin\Production::delete/$1');
    $routes->get('article', 'Admin\Article::index');
    $routes->post('article', 'Admin\Article::create');
    $routes->put('article/(:num)', 'Admin\Article::update/$1');
    $routes->delete('article/(:num)', 'Admin\Article::delete/$1');
    $routes->get('category', 'Admin\Article::categories');
    $routes->post('category', 'Admin\Article::createCategory');
    $routes->get('notification', 'Admin\Notification::index');
    $routes->get('settings', 'Admin\Notification::settings');
    $routes->post('settings', 'Admin\Notification::updateSettings');
    $routes->get('users', 'Admin\UserManagement::index');
    $routes->post('users', 'Admin\UserManagement::create');
    $routes->put('users/(:num)', 'Admin\UserManagement::update/$1');
    $routes->delete('users/(:num)', 'Admin\UserManagement::delete/$1');
    $routes->get('report/pdf', 'Admin\Report::pdf');
    $routes->get('report/excel', 'Admin\Report::excel');
});

// --- USER API ROUTES (Guru & Siswa) ---
$routes->group('api/user', ['filter' => ['auth', 'role:guru,siswa']], static function ($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');
    $routes->get('production', 'User\Production::index');
    $routes->get('article', 'User\Article::index');
    $routes->get('notification', 'User\Notification::index');
});