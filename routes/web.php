<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated; 
use App\Models\User;
use App\Models\Utils;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

 
Route::get('auth/login', function () {
    return view('auth/login');
});
  
 
Route::get('print-gatepass', function (Request $request) {
    $item = VehicleRequest::find($request->gatepass_id);
    if ($item == null) {
        die("Item not found.");
    }
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/print-gatepass', [
        'item' => $item
    ]));
    return $pdf->stream();
});


//endpoint for migration
Route::get('migrate', function () {
    //artisan migrate
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    return nl2br($output); 
});