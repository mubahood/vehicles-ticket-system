<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/calendar', 'HomeController@calendar')->name('calendar');
    $router->resource('departmets', DepartmetController::class);
    $router->resource('vehicles', VehicleController::class);
    $router->resource('vehicle-requests', VehicleRequestController::class);
    $router->resource('materials-requests', VehicleRequestController::class);
    $router->resource('leave-requests', VehicleRequestController::class);
    $router->resource('all-requests', VehicleRequestController::class);
    $router->resource('archived-requests', VehicleRequestController::class);
    $router->resource('companies', CompanyController::class); 
});
