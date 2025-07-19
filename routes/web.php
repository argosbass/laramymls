<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\MigrateController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PublicPropertyController;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');


Route::get('/property-access/{property}', [PublicPropertyController::class, 'showSigned'])
    ->name('property.signed.show')
    ->middleware('signed');

Route::get('/property/{property}/export', function (Property $property) {
    $signedUrl = null; // O genera el que usas normalmente

    $pdf = Pdf::loadView('properties.showPDF', compact('property', 'signedUrl'));

    //return $pdf->download('property-' . $property->id . '.pdf');
    return $pdf->stream('property-' . $property->id . '.pdf');
    // return view('properties.showPDF', compact('property', 'signedUrl'));

})->name('property.export');


Route::middleware(['auth'])->group(function ()
{
    Route::get('/property-listing/{slug}', [PublicPropertyController::class, 'show'])
        ->name('property.public.show');

    Route::get('/property-listing-id/{id}', [PublicPropertyController::class, 'showById'])
        ->name('property.public.showById');
});
