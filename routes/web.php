<?php
use Illuminate\Support\Facades\Route;

use DevOashim\Automate\src\Controllers\AutomateController;

///////////////////////////////////////////////////////////////////////////////////////////


Route::view('/auto', 'auto::automate.lookUp')->name('auto');
Route::view('/html-to-blade', 'auto::automate.html-to-blade')->name('htmlToBlade');
Route::view('/upload-blade', 'auto::automate.uploadBlade')->name('bladeToRoute');

Route::get('/auto-blade-to-route', [AutomateController::class, 'bladeToRouteAuto'])
    ->name('bladeToRouteAuto');

Route::post('/html-to-blade', [AutomateController::class, 'htmlToBlade'])
    ->name('htmlToBlade.upload');

Route::post('/upload-blade', [AutomateController::class, 'bladeToRoute'])
    ->name('upload.blade');

Route::get('/make-views', [AutomateController::class, 'scanAndCreateViews'])
    ->name('make-views');
Route::get('/make-components', [AutomateController::class, 'make_components'])
    ->name('make-components');

////////////////////////////////////////////////////////////////////////////////////////////////