<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/login/process', 'AuthController::process');
$routes->get('/logout', 'AuthController::logout');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'DashboardController::index');
    
    // Rute Logbook Harian (Taruna)
    $routes->get('/logbook', 'LogbookController::index');
    $routes->get('/logbook/create', 'LogbookController::create');
    $routes->post('/logbook/store', 'LogbookController::store');
    $routes->get('/logbook/edit/(:num)', 'LogbookController::edit/$1');
    $routes->post('/logbook/update/(:num)', 'LogbookController::update/$1');
    $routes->get('/logbook/delete/(:num)', 'LogbookController::delete/$1');

    // Rute Validasi Logbook (Pembimbing)
    $routes->get('/validasi', 'ValidasiLogbookController::index');
    $routes->post('/validasi/update/(:num)', 'ValidasiLogbookController::updateStatus/$1');
    
    // Rute Daftar Taruna Bimbingan (Pembimbing)
    $routes->get('/bimbingan', 'BimbinganController::index');

    // Rute Data Pengguna (Admin Prodi, Pejabat & Superadmin)
    $routes->get('/users', 'UserController::index');
    $routes->get('/users/create', 'UserController::create');
    $routes->post('/users/store', 'UserController::store');
    $routes->get('/users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('/users/update/(:num)', 'UserController::update/$1');
    $routes->get('/users/delete/(:num)', 'UserController::delete/$1');
    $routes->post('/users/batchDelete', 'UserController::batchDelete');

    // Rute Laporan Global
    $routes->get('/laporan', 'LaporanGlobalController::index');

    // Rute Input Data Taruna
    $routes->get('/input-data-taruna', 'PenugasanController::index');
    $routes->post('/input-data-taruna/store', 'PenugasanController::store');
    $routes->post('/input-data-taruna/update/(:num)', 'PenugasanController::update/$1');
    $routes->post('/input-data-taruna/batch-migrate', 'PenugasanController::batchMigrate');
    $routes->post('/input-data-taruna/import', 'PenugasanController::importExcel');
    $routes->get('/input-data-taruna/template', 'PenugasanController::downloadTemplate');

    // Rute Input Data Dosen
    $routes->get('/input-data-dosen', 'DosenController::index');
    $routes->post('/input-data-dosen/store', 'DosenController::store');
    $routes->post('/input-data-dosen/import', 'DosenController::importExcel');
    $routes->get('/input-data-dosen/template', 'DosenController::downloadTemplate');

    // Rute Profil Pengguna
    $routes->get('/profile', 'DashboardController::profile');
    $routes->post('/profile/update-password', 'DashboardController::updatePassword');
});
