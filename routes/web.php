<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Application;
use App\Models\LandloadPayment;
use App\Models\Renting;
use App\Models\TenantPayment;
use App\Models\User;
use App\Models\Utils;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('form', [MainController::class, 'form'])->name('form');
Route::get('auth/register', [MainController::class, 'register'])->name('form');
Route::get('generate-class', [MainController::class, 'generate_class']);
Route::get('process-things', [Utils::class, 'process_things']);
 
Route::get('auth/login', function () {
    return view('auth/login');
});
 

 

//tenant receipts
Route::get('receipt', function () {
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/receipt'));
    return $pdf->stream();
});


 


Route::get('quotation', function () {
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/quotation'));
    return $pdf->stream();
});

Route::get('delivery', function () {
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/delivery'));
    return $pdf->stream();
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
 