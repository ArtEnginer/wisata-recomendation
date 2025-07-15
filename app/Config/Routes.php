<?php

use App\Controllers\Api\UserController;
use App\Controllers\Api\WisataController;
use App\Controllers\Api\KriteriaKlasterisasiController;
use App\Controllers\Api\KriteriaPerengkinganController;
use App\Controllers\Api\NilaiKriteriaKlasterisasiController;
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
    $routes->get('dashboard', [Manage::class, 'dashboard']);
    $routes->get('wisata', [Manage::class, 'wisata']);
    $routes->get('kriteria-klasterisasi', [Manage::class, 'kriteriaKlasterisasi']);
    $routes->get('kriteria-perengkingan', [Manage::class, 'kriteriaPerengkingan']);
    $routes->get('nilai-kriteria-klasterisasi', [Manage::class, 'nilaiKriteriaKlasterisasi']);

    $routes->get('user', [Manage::class, 'user']);
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->post('register', [Home::class, 'register']);
    $routes->group('v2', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
        $routes->get('source/storage/(:any)', 'SourceController::storage/$1');
    });
    $routes->get('wisata/clustering', [WisataController::class, 'clustering']);
    $routes->resource('wisata', ['namespace' => '', 'controller' => WisataController::class, 'websafe' => 1]);
    $routes->resource('kriteria-klasterisasi', ['namespace' => '', 'controller' => KriteriaKlasterisasiController::class, 'websafe' => 1]);
    $routes->resource('kriteria-perengkingan', ['namespace' => '', 'controller' => KriteriaPerengkinganController::class, 'websafe' => 1]);
    $routes->post('nilai-kriteria-klasterisasi', [NilaiKriteriaKlasterisasiController::class, 'store']);
    $routes->post('nilai-kriteria-klasterisasi/update', [NilaiKriteriaKlasterisasiController::class, 'storeupdate']);
    $routes->get('nilai-kriteria-klasterisasi/grouped', [NilaiKriteriaKlasterisasiController::class, 'grouped']);
    $routes->resource('nilai-kriteria-klasterisasi', ['namespace' => '', 'controller' => NilaiKriteriaKlasterisasiController::class, 'websafe' => 1]);


    $routes->post('user/activate', [UserController::class, 'activate']);
    $routes->post('user/deactivate', [UserController::class, 'deactivate']);
    $routes->post('user/update/(:uuid)', [UserController::class, 'update']);
    $routes->resource('user', ['namespace' => '', 'controller' => UserController::class]);
});
