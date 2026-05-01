<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::prefix('/companies')->controller(CompanyController::class)->name('companies.')->group(function () {
    Route::post('/', 'store')->name('store');
    Route::get('/{company:edrpou}/versions', 'showVersions')->name('versions');
});
