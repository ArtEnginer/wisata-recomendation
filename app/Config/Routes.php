<?php

use App\Controllers\Api\BarangController;
use App\Controllers\Api\BarangKeluarController;
use App\Controllers\Api\BarangMasukController;
use App\Controllers\Api\KategoriController;
use App\Controllers\Api\MaintenanceController;
use App\Controllers\Api\PeminjamanController;
use App\Controllers\Api\PengadaanKaryawanController;
use App\Controllers\Api\PengadaanTeknisiController;
use App\Controllers\Api\UserController;
use App\Controllers\Frontend\Manage;
use App\Controllers\Migrate;
use CodeIgniter\Router\RouteCollection;
use App\Controllers\Home;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->addPlaceholder('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

service('auth')->routes($routes);

$routes->environment('development', static function ($routes) {
    $routes->get('migrate', [Migrate::class, 'index']);
    $routes->get('migrate/(:any)', [Migrate::class, 'execute']);
});

$routes->group('panel', static function (RouteCollection $routes) {
    $routes->get('', [Manage::class, 'index']);
    $routes->get('barang', [Manage::class, 'barang']);
    $routes->get('kategori', [Manage::class, 'kategori']);
    $routes->get('barang-masuk', [Manage::class, 'barangMasuk']);
    $routes->get('barang-keluar', [Manage::class, 'barangKeluar']);
    $routes->get('pengadaan-karyawan', [Manage::class, 'pengadaanKaryawan']);
    $routes->get('pengadaan-teknisi', [Manage::class, 'pengadaanTeknisi']);
    $routes->get('maintenance', [Manage::class, 'maintenance']);
    $routes->get('peminjaman', [Manage::class, 'peminjaman']);
    $routes->get('user', [Manage::class, 'user']);
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->post('register', [Home::class, 'register']);
    $routes->group('v2', ['namespace' => 'App\Controllers\Api'], static function ($routes) {});
    $routes->resource('barang', ['namespace' => '', 'controller' => BarangController::class, 'websafe' => 1]);
    $routes->resource('kategori', ['namespace' => '', 'controller' => KategoriController::class, 'websafe' => 1]);
    $routes->resource('barang-masuk', ['namespace' => '', 'controller' => BarangMasukController::class, 'websafe' => 1]);
    $routes->resource('barang-keluar', ['namespace' => '', 'controller' => BarangKeluarController::class, 'websafe' => 1]);
    $routes->post('maintenance/acc/(:num)', [MaintenanceController::class, 'acc']);
    $routes->post('maintenance/tolak/(:num)', [MaintenanceController::class, 'tolak']);
    $routes->post('maintenance/selesai/(:num)', [MaintenanceController::class, 'selesai']);
    $routes->resource('maintenance', ['namespace' => '', 'controller' => MaintenanceController::class, 'websafe' => 1]);
    $routes->post('pengadaan-karyawan/acc/(:num)', [PengadaanKaryawanController::class, 'acc']);
    $routes->post('pengadaan-karyawan/tolak/(:num)', [PengadaanKaryawanController::class, 'tolak']);
    $routes->resource('pengadaan-karyawan', ['namespace' => '', 'controller' => PengadaanKaryawanController::class, 'websafe' => 1]);
    $routes->post('pengadaan-teknisi/acc/(:num)', [PengadaanTeknisiController::class, 'acc']);
    $routes->post('pengadaan-teknisi/tolak/(:num)', [PengadaanTeknisiController::class, 'tolak']);
    $routes->resource('pengadaan-teknisi', ['namespace' => '', 'controller' => PengadaanTeknisiController::class, 'websafe' => 1]);

    $routes->post('peminjaman/acc/(:num)', [PeminjamanController::class, 'acc']);
    $routes->post('peminjaman/tolak/(:num)', [PeminjamanController::class, 'tolak']);
    $routes->resource('peminjaman', ['namespace' => '', 'controller' => PeminjamanController::class, 'websafe' => 1]);




    $routes->post('user/activate', [UserController::class, 'activate']);
    $routes->post('user/deactivate', [UserController::class, 'deactivate']);
    $routes->post('user/update/(:uuid)', [UserController::class, 'update']);
    $routes->resource('user', ['namespace' => '', 'controller' => UserController::class]);
});
